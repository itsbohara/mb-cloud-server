<?php

$router->group(['prefix' => 'api', 'as' => 'movie'], function () use ($router) {

    $router->get('/generate', ['as' => 'all', 'uses' => 'ApisController@generate']);

/* restrict route */
    $router->group(['middleware' => 'auth'], function () use ($router) {

        $router->get('/all', ['as' => 'all', 'uses' => 'ApisController@all']);

        $router->post('/new', ['as' => 'new bucket', 'uses' => 'ApisController@create']);

        // $router->post('/{id}/update', ['as' => 'update bucket', 'uses' => 'ApisController@update']);

        $router->delete('/{id}', ['as' => 'delete bucket', 'uses' => 'ApisController@delete']);

    });

});
