<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\PredictionIoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EndroidPredictionIoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $eventServer = $config['event_server'];
        foreach ($config['apps'] as $app => $appConfig) {
            $eventClient = new Definition('Endroid\PredictionIo\EventClient');
            $eventClient
                ->addArgument($appConfig['key'])
                ->addArgument($eventServer['url'])
                ->addArgument($eventServer['timeout'])
                ->addArgument($eventServer['connect_timeout']);
            $eventClient->setLazy(true);
            $container->setDefinition(sprintf('endroid.prediction_io.%s.event_client', $app), $eventClient);
            foreach ($appConfig['engines'] as $engine => $engineConfig) {
                $engineClient = new Definition('Endroid\PredictionIo\EngineClient');
                $engineClient
                    ->addArgument($engineConfig['url'])
                    ->addArgument($engineConfig['timeout'])
                    ->addArgument($engineConfig['connect_timeout']);
                $engineClient->setLazy(true);
                $container->setDefinition(sprintf('endroid.prediction_io.%s.%s.engine_client', $app, $engine),
                    $engineClient
                );
            }
        }
    }
}
