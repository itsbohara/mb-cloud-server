<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BucketController extends Controller
{
    //

    public function all()
    {

        // return $this->core->setResponse('error', 'Buckes Not Found', null, false, 404);
        // return $this->core->setResponse('success', 'Get Buckets by current user', Bucket::paginate($per_page));
        return $this->core->setResponse('success', 'Get All Buckets', Bucket::all());
    }

    public function single($id)
    {

        if (!$bucket = Bucket::find($id)) {

            return $this->core->setResponse('error', 'Bucket Not Found', null, false, 404);
        }

        return $this->core->setResponse('success', 'Bucket Found', $bucket);
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
        $bucket = Bucket::create($input);

        return $this->core->setResponse('success', 'Bucket Created', $bucket);

    }

    // update bucket
    public function update(Request $request, $id)
    {

        /* validation requirement */
        $validator = $this->validation('update', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), null, false, 400);
        }

        $bucket = Bucket::find($id);

        $bucket->fill($request->only(['name', 'slug']))->save();

        return $this->core->setResponse('success', 'Bucket Updated', $bucket);

    }

    // delete bucket and related files

    public function delete($id)
    {

        if (!$bucket = Bucket::find($id)) {

            return $this->core->setResponse('error', 'Bucket Not Found', null, false, 404);
        }

        // $bucket->delete();

        return $this->core->setResponse('success', 'Bucket deleted');

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
                    'slug' => 'required|max:100|min:2',
                    // 'user_id' => 'required|string',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
