<?php

/* restrict route */
$router->group(['prefix' => 'file', 'as' => 'file'], function () use ($router) {

    // allow to upload/delete without authorization but proper API validation
    $router->post('/upload', ['as' => 'upload file', 'uses' => 'FileController@upload']);
    $router->delete('/_delete', ['as' => 'delete file using API', 'uses' => 'FileController@deleteAPI']);

/* restrict route */
    $router->group(['middleware' => 'auth'], function () use ($router) {

        // $router->get('/all', ['as' => 'all', 'uses' => 'FileController@all']);

        $router->get('/b/{bucket_id}', ['as' => 'all', 'uses' => 'FileController@bucketFiles']);

        // $router->post('/{id}/update', ['as' => 'update bucket', 'uses' => 'FileController@update']);

        // $router->delete('/{name}', ['as' => 'delete file', 'uses' => 'FileController@delete']);
        $router->delete('/', ['as' => 'delete file', 'uses' => 'FileController@delete']);

    });

});
