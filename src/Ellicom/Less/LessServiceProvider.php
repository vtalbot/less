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
        $this->app['config']->package('ellicom/less', 'ellicom/less', 'ellicom/less');

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

        $prefix = $app['config']['ellicom/less::prefix'];

        foreach ($app['config']['ellicom/less::routes'] as $routes)
        {
            foreach ($app['config']['ellicom/less::extensions'] as $ext)
            {
                \Route::get($prefix.$routes.'{file}.'.$ext, function($file) use ($routes, $app)
                {
                    $less = \Less::make($routes.$file);

                    $response = \Response::make($less, 200, array('Content-Type' => 'text/css'));
                    $response->setCache(array('public' => true));

                    if ( ! is_null($app['config']['ellicom/less::expires']))
                    {
                        $date = date_create();
                        $date->add(new \DateInterval('PT'.$app['config']['ellicom/less::expires'].'M'));
                        $response->setExpires($date);
                    }

                    return $response;
                })->where('file', '.*');
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
            $paths = $app['config']['ellicom/less::paths'];

            foreach ($paths as $key => $path)
            {
                $paths[$key] = $app['path'].$path;
            }

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