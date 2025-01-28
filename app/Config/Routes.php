<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/', 'Home::index');
$routes->get('/getHotels', 'Home::getHotels');
$routes->get('/deleteExpiredHotels', 'Home::deleteExpiredHotels');
