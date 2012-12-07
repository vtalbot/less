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
        $contents = $less->compile($this->files->get($path));

        if ( ! is_null($this->cachePath))
        {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

}