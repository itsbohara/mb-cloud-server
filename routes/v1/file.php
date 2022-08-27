<?php

/* restrict route */
$router->group(['prefix' => 'file', 'as' => 'file', 'middleware' => 'auth'], function () use ($router) {

    // $router->get('/all', ['as' => 'all', 'uses' => 'FileController@all']);

    $router->get('/b/{bucket_id}', ['as' => 'all', 'uses' => 'FileController@bucketFiles']);

    $router->post('/upload', ['as' => 'upload file', 'uses' => 'FileController@upload']);

    // $router->post('/{id}/update', ['as' => 'update bucket', 'uses' => 'FileController@update']);

    // $router->delete('/{name}', ['as' => 'delete file', 'uses' => 'FileController@delete']);
    $router->delete('/', ['as' => 'delete file', 'uses' => 'FileController@delete']);

});
