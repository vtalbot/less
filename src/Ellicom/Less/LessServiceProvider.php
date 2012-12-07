<?php

namespace Ellicom\Less;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;
use Ellicom\Less\Compilers\LessCompiler;

class LessServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoutes();

        $this->registerEngineResolver();

        $this->registerLessFinder();

        $this->registerEnvironment();
    }

    /**
     * Register routes to catch LESS request.
     *
     * @return void
     */
    public function registerRoutes()
    {
        $app = $this->app;

        $prefix = $app['config']['less.prefix'];

        foreach ($app['config']['less.routes'] as $routes)
        {
            foreach ($app['config']['less.extensions'] as $ext)
            {
                \Route::get($prefix.$routes.'{file}.'.$ext, function($file) use ($routes)
                {
                    return \Less::make($routes.$file);
                });
            }
        }
    }

    /**
     * Register the engine resolver instance.
     *
     * @return void
     */
    public function registerEngineResolver()
    {
        list($me, $app) = array($this, $this->app);

        $app['less.engine.resolver'] = $app->share(function($app) use ($me)
        {
            $resolver = new EngineResolver;

            foreach (array('less') as $engine)
            {
                $me->{'register'.ucfirst($engine).'Engine'}($resolver);
            }

            return $resolver;
        });
    }

    /**
     * Register the LESS engine implementation.
     *
     * @param  Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerLessEngine($resolver)
    {
        $app = $this->app;

        $resolver->register('less', function() use ($app)
        {
            $cache = $app['path'].'/storage/less';

            if ( ! $app['files']->isDirectory($cache))
            {
                $app['files']->makeDirectory($cache);
            }

            $compiler = new LessCompiler($app['files'], $cache);

            return new CompilerEngine($compiler, $app['files']);
        });
    }

    /**
     * Register the LESS finder implementation.
     *
     * @return void
     */
    public function registerLessFinder()
    {
        $this->app['less.finder'] = $this->app->share(function($app)
        {
            $paths = $app['config']['less.paths'];

            return new FileViewFinder($app['files'], $paths, array('less'));
        });
    }

    /**
     * Register the LESS environment.
     *
     * @return void
     */
    public function registerEnvironment()
    {
        $me = $this;

        $this->app['less'] = $this->app->share(function($app) use ($me)
        {
            $resolver = $app['less.engine.resolver'];

            $finder = $app['less.finder'];

            $events = $app['events'];

            $environment = new Environment($resolver, $finder, $events);

            $environment->setContainer($app);

            $environment->share('app', $app);

            return $environment;
        });
    }

}