<?php
/**
 * Defines all routes that router will map using alto router.
 *
 * Sample route
 *
 * 'controller.action_method' => [
 *                                   'method'      => 'GET',
 *                                   'path'       => 'sample_route',
 *                               ]
 */
namespace Router;

return [
    //Hotel search Call
    'hotel.search' => [
        'method'       => 'GET',
        'path'         => '/hotel/search'
    ]
];