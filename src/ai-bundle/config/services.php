<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\AI\Agent\StructuredOutput\AgentProcessor as StructureOutputProcessor;
use Symfony\AI\Agent\StructuredOutput\ResponseFormatFactory;
use Symfony\AI\Agent\StructuredOutput\ResponseFormatFactoryInterface;
use Symfony\AI\Agent\Toolbox\AgentProcessor as ToolProcessor;
use Symfony\AI\Agent\Toolbox\Toolbox;
use Symfony\AI\Agent\Toolbox\ToolboxInterface;
use Symfony\AI\Agent\Toolbox\ToolCallArgumentResolver;
use Symfony\AI\Agent\Toolbox\ToolFactory\ReflectionToolFactory;
use Symfony\AI\Agent\Toolbox\ToolFactoryInterface;
use Symfony\AI\Agent\Toolbox\ToolResultConverter;
use Symfony\AI\AIBundle\Profiler\DataCollector;
use Symfony\AI\AIBundle\Profiler\TraceableToolbox;
use Symfony\AI\AIBundle\Security\EventListener\IsGrantedToolAttributeListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->autowire()

        // structured output
        ->set(ResponseFormatFactory::class)
            ->alias(ResponseFormatFactoryInterface::class, ResponseFormatFactory::class)
        ->set(StructureOutputProcessor::class)
            ->tag('ai.agent.input_processor')
            ->tag('ai.agent.output_processor')

        // tools
        ->set('ai.toolbox.abstract')
            ->class(Toolbox::class)
            ->autowire()
            ->abstract()
            ->args([
                '$toolFactory' => service(ToolFactoryInterface::class),
                '$tools' => abstract_arg('Collection of tools'),
            ])
        ->set(Toolbox::class)
            ->parent('ai.toolbox.abstract')
            ->args([
                '$tools' => tagged_iterator('ai.tool'),
            ])
            ->alias(ToolboxInterface::class, Toolbox::class)
        ->set(ReflectionToolFactory::class)
            ->alias(ToolFactoryInterface::class, ReflectionToolFactory::class)
        ->set(ToolResultConverter::class)
        ->set(ToolCallArgumentResolver::class)
        ->set('ai.tool.agent_processor.abstract')
            ->class(ToolProcessor::class)
            ->abstract()
            ->args([
                '$toolbox' => abstract_arg('Toolbox'),
            ])
        ->set(ToolProcessor::class)
            ->parent('ai.tool.agent_processor.abstract')
            ->tag('ai.agent.input_processor')
            ->tag('ai.agent.output_processor')
            ->args([
                '$toolbox' => service(ToolboxInterface::class),
                '$eventDispatcher' => service('event_dispatcher')->nullOnInvalid(),
            ])
        ->set('ai.security.is_granted_attribute_listener', IsGrantedToolAttributeListener::class)
            ->tag('kernel.event_listener')

        // profiler
        ->set(DataCollector::class)
            ->tag('data_collector')
        ->set(TraceableToolbox::class)
            ->decorate(ToolboxInterface::class)
            ->tag('ai.traceable_toolbox')
    ;
};
