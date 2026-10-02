<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        // IMPORTANT : liaison SettingsInterface -> Settings
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true,
                'logError' => true,
                'logErrorDetails' => true,

                'logger' => [
                    'name' => 'slim-app',
                    'path' => __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],

                'db' => [
                    'host' => 'mysql-ismailmk.alwaysdata.net',
                    'port' => 3306,
                    'database' => 'ismailmk_api',
                    'username' => 'ismailmk',
                    'password' => 'Ismail_63s',
                    'charset' => 'utf8mb4',
                    'flags' => [
                        PDO::ATTR_PERSISTENT => false,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES => true,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ],
                ],
            ]);
        },

        LoggerInterface::class => function (ContainerInterface $c) {

            $settings = $c->get(SettingsInterface::class);
            $loggerSettings = $settings->get('logger');

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());

            $handler = new StreamHandler(
                $loggerSettings['path'],
                $loggerSettings['level']
            );

            $logger->pushHandler($handler);

            return $logger;
        },

        PDO::class => function (ContainerInterface $c) {

            $settings = $c->get(SettingsInterface::class);
            $db = $settings->get('db');

            $dsn = "mysql:host={$db['host']};port={$db['port']};"
                . "dbname={$db['database']};charset={$db['charset']}";

            return new PDO(
                $dsn,
                $db['username'],
                $db['password'],
                $db['flags']
            );
        },

    ]);
};