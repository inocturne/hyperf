<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Tracer;

use GuzzleHttp\Client;
use Hyperf\Tracer\Aspect\HttpClientAspect;
use Hyperf\Tracer\Aspect\RedisAspect;
use Hyperf\Tracer\Aspect\TraceAnnotationAspect;
use Hyperf\Tracer\Listener\DbQueryExecutedListener;
use Jaeger\ThriftUdpTransport;
use OpenTracing\Tracer;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Tracer::class => TracerFactory::class,
                SwitchManager::class => SwitchManagerFactory::class,
                SpanTagManager::class => SpanTagManagerFactory::class,
                Client::class => Client::class,
            ],
            'listeners' => [
                DbQueryExecutedListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'class_map' => [
                        ThriftUdpTransport::class => __DIR__ . '/../class_map/ThriftUdpTransport.php',
                    ],
                ],
            ],
            'aspects' => [
                HttpClientAspect::class,
                RedisAspect::class,
                TraceAnnotationAspect::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for tracer.',
                    'source' => __DIR__ . '/../publish/opentracing.php',
                    'destination' => BASE_PATH . '/config/autoload/opentracing.php',
                ],
            ],
        ];
    }
}
