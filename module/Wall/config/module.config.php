<?php

return array(
    'router' => array(
        'routes' => array(
            'wall' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:username',
                    'constraints' => array(
                        'username' => '\w+'
                    ),
                    'defaults' => array(
                        'controller' => 'Wall\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            )
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Wall\Controller\Index' => 'Wall\Controller\IndexController'
        ),
    ),
    // this is the client application, so we are going to take care
    // configuring the View Manager.
    'view_manager' => array(
        'display_not_found_reason'  => true,
        'display_exceptions'        => true,
        'doctype'                   => HTML5,
        'not_found_template'        => 'error/404',
        'exception_template'        => 'error/index',
        'template_path_stack'       => array(
            __DIR__ . '/../view',
        ),
    ),
);
