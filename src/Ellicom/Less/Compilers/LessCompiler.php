<?php

namespace Ellicom\Less\Compilers;

use Illuminate\Filesystem;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;

class LessCompiler extends Compiler implements CompilerInterface {

    /**
     * Compile the LESS at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path)
    {
        $less = new \lessc;
        $contents = $less->compileFile($path);

        if ( ! is_null($this->cachePath))
        {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param  string  $path
     * @return bool
     */
    public function isExpired($path)
    {
        if ($this->hasChanged($path))
        {
            return true;
        }

        return parent::isExpired($path);
    }

    /**
     * Determine if a change has occured in an imported file.
     *
     * @param  string  $path
     * @return bool
     */
    public function hasChanged($path)
    {
        $content = $this->files->get($path);
        $lastModified = $this->files->lastModified($path);

        preg_match_all('/@import(-once)?\s*([\'"])(.*)([\'"])/', $content, $matches);

        foreach ($matches[3] as $import)
        {
            $import = substr($path, 0, strrpos($path, '/') + 1) . $import;

            if ( ! preg_match('/(.css|.less)$/', $import))
            {
                $import .= '.less';
            }

            if ($lastModified <= $this->files->lastModified($import) || $this->hasChanged($import))
            {
                return true;
            }
        }

        return false;
    }

}