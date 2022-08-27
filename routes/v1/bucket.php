<?php

/* restrict route */
$router->group(['prefix' => 'bucket', 'as' => 'movie', 'middleware' => 'auth'], function () use ($router) {

    $router->get('/all', ['as' => 'all', 'uses' => 'BucketController@all']);

    $router->post('/new', ['as' => 'new bucket', 'uses' => 'BucketController@create']);

    $router->post('/{id}/update', ['as' => 'update bucket', 'uses' => 'BucketController@update']);

    $router->delete('/{id}', ['as' => 'delete bucket', 'uses' => 'BucketController@delete']);

});
