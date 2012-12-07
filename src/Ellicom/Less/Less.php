<?php

namespace Ellicom\Less;

use Illuminate\View\Engines\EngineInterface;
use Illuminate\Support\Contracts\RenderableInterface as Renderable;

class Less implements Renderable {

    /**
     * The LESS environment instance.
     *
     * @var Ellicom\Less\Environment
     */
    protected $environment;

    /**
     * The engine implementation.
     *
     * @var Illuminate\View\Engines\EngineInterface
     */
    protected $engine;

    /**
     * The name of the LESS.
     *
     * @var string
     */
    protected $less;

    /**
     * The path to the LESS file.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new LESS instance.
     *
     * @param  Ellicom\Less\Environment  $environment
     * @param  Illuminate\View\Engines\EngineInterface  $engine
     * @param  string  $less
     * @param  string  $path
     * @param  array   $data
     * @return void
     */
    public function __construct(Environment $environment, EngineInterface $engine, $less, $path)
    {
        $this->environment = $environment;
        $this->engine = $engine;
        $this->less = $less;
        $this->path = $path;
    }

    /**
     * Get the string contents of the LESS.
     *
     * @return string
     */
    public function render()
    {
        $env = $this->environment;

        $env->incrementRender();

        $contents = $this->getContents();

        $env->decrementRender();

        return $contents;
    }

    /**
     * Get the evaluated contents of the LESS.
     *
     * @return string
     */
    protected function getContents()
    {
        return $this->engine->get($this->path);
    }

    /**
     * Get the LESS environment instance.
     *
     * @return Ellicom\Less\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get the LESS' rendering engine.
     *
     * @return Illuminate\View\Engines\EngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Get the name of the LESS.
     *
     * @return string
     */
    public function getName()
    {
        return $this->less;
    }

    /**
     * Get the array of LESS data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the path to the LESS file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path to the LESS.
     *
     * @param  string  $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the string contents of the LESS.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}