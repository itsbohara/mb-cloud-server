<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Bucket;
use App\Models\File;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    //
    // if(hash_equals($api_token, $key)){
    //     echo 'Valid';
    // }else{
    //     echo 'Invalid';
    // }

    public function all()
    {

        // return $this->core->setResponse('error', 'Buckes Not Found', null, false, 404);
        // return $this->core->setResponse('success', 'Get Buckets by current user', Bucket::paginate($per_page));
        return $this->core->setResponse('success', 'Get All User Files', File::all());
    }

    public function bucketFiles($bucket_id)
    {
        if (!$bucket = Bucket::find($bucket_id)) {
            return $this->core->setResponse('error', 'Invalid Bucket', null, false, 404);
        }

        $response = File::where('bucket_id', $bucket_id)->get();
        return $this->core->setResponse('success', 'Got Specified Bucket Files', $response);
    }

    public function upload(Request $request)
    {
        // !validate key & bucket first
        $bucket_id = $request->input('bucket_id');
        $api_id = $request->input('api');

        $api = Api::where('key', $api_id)->get();

        if ($api->count() < 1) {
            return $this->core->setResponse('error', 'Invalid API Key', null, false, 404);
        }

        if (!$bucket = Bucket::find($bucket_id)) {
            return $this->core->setResponse('error', 'Invalid Bucket', null, false, 404);
        }

        /* validation requirement */
        $validator = $this->validation('upload', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), null, false, 400);
        }

        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        $fileName = uniqid() . '_' . $file_name;
        $extension = $file->getClientOriginalExtension();
        $destination_path = './bucket/' . $bucket->slug . '/';
        $file->move($destination_path, $fileName);

        $input = [
            'name' => $fileName,
            'extension' => $extension,
            'path' => 'bucket/' . $bucket->slug,
            'user_id' => Auth::user()->id,
            'bucket_id' => $bucket_id,
        ];
        $newFile = File::create($input);

        return $this->core->setResponse('success', 'File uploaded successfully', $newFile);

    }

    public function delete(Request $request)
    {
        $filname = $request->input('filname');
        $file = File::where('name', $filname)->get();
        if ($file->count() < 1) {
            return $this->core->setResponse('error', 'File not found', null, false, 404);
        }

        $filePath = $file[0]->path . '/' . $file[0]->name;
        // Storage::delete($filePath);

        if (!unlink($filePath)) {
            return $this->core->setResponse('error', 'File delete error', null, false, 404);
        } else {
            $deleteFile = File::find($file[0]->id);
            $deleteFile->delete();

            return $this->core->setResponse('success', 'File delete successfully');
        }

    }

    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'upload':

                $validator = [
                    'file' => 'required|file',
                    'api' => 'required|string',
                    'bucket_id' => 'required|string',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
