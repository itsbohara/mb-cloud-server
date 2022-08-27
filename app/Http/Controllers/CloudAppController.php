<?php

namespace App\Http\Controllers;

use \stdClass;

class CloudAppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //

        // parent::__construct();
    }

    //
    public function hello()
    {

        $object = new stdClass();
        $object->app = 'Hello World';
        // var_dump($object);
        return response()->json($object);
        // return response()->json(['name' => 'Abigail', 'state' => 'CA']);
    }
}
