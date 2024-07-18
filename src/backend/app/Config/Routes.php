<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Pessoa;

/**
 * @var RouteCollection $routes
 */
$routes->group('', ['filter' => 'cors'], static function (RouteCollection $routes): void {
    $routes->resource('pessoas', ['namespace' => '', 'controller' => Pessoa::class]);
    
    //$routes->post('/pessoas/upload/(:segment)', 'Pessoa::upload/$1');    
    
    $routes->options('pessoas', static function () {        
        $response = response();
        $response->setStatusCode(204);
        $response->setHeader('Allow:', 'OPTIONS, GET, POST, PUT, PATCH, DELETE');

        return $response;
    });

    /*$routes->options('upload', static function () {        
        $response = response();
        $response->setStatusCode(204);
        $response->setHeader('Allow:', 'OPTIONS, GET, POST, PUT, PATCH, DELETE');

        return $response;
    }); */

    $routes->options('pessoas/(:any)', static function () {});

    //$routes->options('upload/(:any)', static function () {});
});
