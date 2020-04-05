<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'QuinenCake',
    ['path' => '/quinen'],
    function (RouteBuilder $routes) {
        $routes->connect(
            '/:action',
            [
                'controller' => 'Index',
                //'action' => 'bdd'
            ]
        );
        $routes->fallbacks(DashedRoute::class);
    }
);
