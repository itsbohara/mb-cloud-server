<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Bucket;
use App\Models\File;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

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
        $bucketFiles = new stdClass();
        $bucketFiles->files = $response;
        $bucketFiles->bucket = $bucket;

        return $this->core->setResponse('success', 'Got Specified Bucket Files', $bucketFiles);
    }

    public function upload(Request $request)
    {
        // !validate key
        $api_id = $request->input('api');

        $api = Api::where('key', $api_id)->get();

        if ($api->count() < 1) {
            return $this->core->setResponse('error', 'Invalid API Key', null, false, 404);
        }

        if (!$bucket = Bucket::find($api[0]->bucket_id)) {
            return $this->core->setResponse('error', 'Bucket not available for provide API key.', null, false, 404);
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

        $_dir = '';
        if($_dir = $request->input('dir')) $_dir = $_dir . '/';
        $destination_path = './..'.env("APP_UPLOAD_ROOT"). $bucket->slug . '/' . $_dir;
        // replace any multiple dir seperator before saving path to db
        $destination_path = str_replace( "///", "/", $destination_path); 
        $destination_path = str_replace( "//", "/", $destination_path); 
        
        $file->move($destination_path, $fileName);

        $_path = $bucket->slug . '/'.$_dir. $fileName;

        $input = [
            'name' => $fileName,
            'extension' => $extension,
            'path' => env("APP_UPLOAD_ROOT") . $_path ,
            // 'user_id' => Auth::user()->id, //!
            'user_id' => $api[0]->user_id,
            'bucket_id' => $api[0]->bucket_id,
        ];
        $uploadedFile = File::create($input);

        $newFile = new stdClass();
        $newFile->file = $uploadedFile;
        
        //! use server url
        $newFile->downloadURL = 'https://s1.itsbohara.com/' . $_path;

        return $this->core->setResponse('success', 'File uploaded successfully', $newFile);

    }

    // delete by ID or fileName
    public function delete(Request $request)
    {
        $filname = $request->input("filename");
        $_id = $request->input("_id");
        $forceDelete = $request->input("force");
        $usingID = false;

        if($_id) {
            $file = File::find($_id);
            $usingID = true;
        } else {
            $file = File::where('name', $filname)->get();
             if($file->count() > 0) $file = $file[0];
        }
        
        if (!$file || $file->count() < 1) {
            return $this->core->setResponse('error', 'File not found', null, false, 404);
        }

        $filePath = '..'. $file->path;
        // Storage::delete($filePath);
        
        try {
            if(!is_file($filePath) && !$forceDelete){
                return $this->core->setResponse('error', "File doesn't exist!", null, false, 404);
            }
            
            if(is_file($filePath)) unlink($filePath);
            
            if($forceDelete) {
                $deleteFile = $file;
                if(!$usingID) $deleteFile = File::find($file->id);
                $deleteFile->delete();
            }
            
            return $this->core->setResponse('success', 'File delete successfully');
        } catch(Exception $e) {
            return $this->core->setResponse('error', 'File delete error', null, false, 404);
        }

    }

    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'upload':

                $validator = [
                    'file' => 'required|file',
                    'api' => 'required|string',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
