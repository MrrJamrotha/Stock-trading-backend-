<?php

namespace App\Service;

class Service {
    public function validate($validate){
        return response()->json([
            'status'=>'failed',
            'message'=>$validate->errors()->first(),
        ],400);
    }

    public function notFound(){
        return response()->json([
            'status'=>'failed',
            'message'=>'Not Found',
        ],404);
    }

    public function errorMessage(){
        return response()->json([
            'status'=>'failed',
            'message'=>'Something went wrong',
        ],500);
    }
}
