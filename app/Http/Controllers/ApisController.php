<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ApisController extends Controller
{

    public function generate()
    {
        $object = new stdClass();
        $object->key = bin2hex(random_bytes(32));

        return $this->core->setResponse('success', 'Random API KEY', $object);
    }

    public function all()
    {
        // return $this->core->setResponse('error', 'Buckes Not Found', null, false, 404);
        // return $this->core->setResponse('success', 'Get Buckets by current user', Api::paginate($per_page));
        // return $this->core->setResponse('success', 'Get All APIs', Api::all());
        $response = Api::where('user_id', Auth::user()->id)->get();
        return $this->core->setResponse('success', 'Get All APIs', $response);
    }

    public function single($id)
    {

        if (!$api = Api::find($id)) {

            return $this->core->setResponse('error', 'Api Not Found', null, false, 404);
        }

        return $this->core->setResponse('success', 'Api Found', $api);
    }

    // create bucket
    public function create(Request $request)
    {

        /* validation requirement */
        $validator = $this->validation('create', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), null, false, 400);
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $api = Api::create($input);

        return $this->core->setResponse('success', 'Api Created', $api);

    }

    // update bucket
    public function update(Request $request, $id)
    {

        /* validation requirement */
        $validator = $this->validation('update', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), null, false, 400);
        }

        $api = Api::find($id);

        $api->fill($request->only(['name', 'slug']))->save();

        return $this->core->setResponse('success', 'Api Updated', $api);

    }

    // delete bucket and related files

    public function delete($id)
    {

        if (!$api = Api::find($id)) {

            return $this->core->setResponse('error', 'Api Not Found', null, false, 404);
        }

        // $api->delete();

        return $this->core->setResponse('success', 'Api deleted');

    }

    /**
     * validation requirement
     *
     * @param  string $type
     * @param  request $request
     * @return object
     */
    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'create' || 'update':

                $validator = [
                    'name' => 'required|max:100|min:2',
                    'key' => 'required|string',
                    'bucket_id' => 'required|string',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
