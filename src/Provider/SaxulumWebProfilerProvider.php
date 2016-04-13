<?php

namespace Saxulum\SaxulumWebProfiler\Provider;

use Saxulum\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistry;
use Saxulum\SaxulumWebProfiler\DataCollector\DoctrineDataCollector;
use Saxulum\SaxulumWebProfiler\Logger\DbalLogger;
use Saxulum\SaxulumWebProfiler\Twig\DoctrineExtension;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SaxulumWebProfilerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot(Container $app) {}

    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['saxulum.orm.logger'] = function ($app) {
            return new DbalLogger($app['monolog'], $app['stopwatch']);
        };

        if (isset($app['profiler'])) {

            $app->extend('twig', function (\Twig_Environment $twig) {
                $twig->addExtension(new DoctrineExtension());

                return $twig;
            });

            $app->extend('twig.loader.filesystem',
                function (\Twig_Loader_Filesystem $twigLoaderFilesystem) {
                    $twigLoaderFilesystem->addPath(dirname(__DIR__). '/Resources/views', 'SaxulumWebProfilerProvider');

                    return $twigLoaderFilesystem;
                }
            );

            $app->extend('data_collectors',
                function(array $collectors) use ($app) {
                    if(isset($app['doctrine'])) {
                        $collectors['db'] = function ($app) {
                            $dataCollector = new DoctrineDataCollector($app['doctrine']);
                            foreach ($app['doctrine']->getConnectionNames() as $name) {
                                $logger = $app['saxulum.orm.logger'];
                                $app['doctrine']->getConnection($name)->getConfiguration()->setSQLLogger($logger);
                                $dataCollector->addLogger($name, $logger);
                            }

                            return $dataCollector;
                        };
                    }

                    return $collectors;
                }
            );

            $app->extend('data_collector.templates',
                function(array $dataCollectorTemplates) use ($app) {
                    if(isset($app['doctrine'])) {
                        $dataCollectorTemplates[] = array('db', '@SaxulumWebProfilerProvider/Collector/db.html.twig');
                    }

                    return $dataCollectorTemplates;
                }
            );
        }
    }
}
