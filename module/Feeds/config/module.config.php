<?php

return array(
    'router' => array(
        'routes' => array(
            'feeds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    /*
                     * Matches the route:
                     * Username (required)
                     * /cgoodric/feed/[:optional_feed_id][/optional_page/:page]
                     */
                    'route'    => '/:username/feeds[/:feed_id][/page/:page]',
                    'constraints' => array(
                        'username' => '\w+',
                        'feed_id' => '\d*',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index',
                        'action' => 'index',
                        'page' => 1
                    ),
                ),
            ),
            'feeds-subscribe' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    /*
                     * Matches the route
                     *
                     * /cgoodric/feeds/subscrive
                     * (All parameters required)
                     */
                    'route'    => '/:username/feeds/subscribe',
                    'constraints' => array(
                        'username' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index',
                        'action' => 'subscribe'
                    ),
                ),
            ),
            'feeds-unsubscribe' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    /*
                     * Matches the route
                     *
                     * /cgoodric/feeds/unsubscribe
                     * (All parameters required)
                     */
                    'route'    => '/:username/feeds/unsubscribe',
                    'constraints' => array(
                        'username' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index',
                        'action' => 'unsubscribe'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            // We have to be able to retrieve the index controller
            // from the Service Manager so that it can be used by
            // the url route matches.
            'Feeds\Controller\Index' => 'Feeds\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
