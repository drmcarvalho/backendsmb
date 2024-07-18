<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Pessoa;

/**
 * @var RouteCollection $routes
 */
$routes->group('', ['filter' => 'cors'], static function (RouteCollection $routes): void {
    $routes->resource('pessoas', ['namespace' => '', 'controller' => Pessoa::class]);

    $routes->options('pessoas', static function () {
        // Implement processing for normal non-preflight OPTIONS requests,
        // if necessary.
        $response = response();
        $response->setStatusCode(204);
        $response->setHeader('Allow:', 'OPTIONS, GET, POST, PUT, PATCH, DELETE');

        return $response;
    });

    $routes->options('pessoas/(:any)', static function () {});
});
