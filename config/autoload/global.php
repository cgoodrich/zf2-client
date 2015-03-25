<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=zf2appdevelopment;host=www.beaconstagezero.com',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' =>
                'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'navigation' => array(
        /*
         * We have the default menu containing only labels and routes. This
         * will force the Navigation components to use MVC pages and as
         * you can imagine, we will benefit from the RouteMatch component
         * that the component will generate.
         */
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'wall',
            ),
            array(
                'label' => 'Feeds',
                'route' => 'feeds',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            // Point navigation calls to the right file within ZF2.
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
);
