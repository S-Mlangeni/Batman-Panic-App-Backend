<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Panic;
use App\Models\User;

class PanicController extends Controller
{
    function panic_history() {
        error_log("panic history reached");
        $panics = Panic::all();
        foreach ($panics as $panic) {
            $user = User::find($panic->user_id);
            $panicArray[] = [
                "id" => $panic->id,
                "longitude" => $panic->longitude,
                "latitiude" => $panic->latitude,
                "panic_type" => $panic->panic_type,
                "details" => $panic->details,
                "created_at" => $panic->created_at,
                "created_by" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email
                ]
            ];
        };
        
        if (!empty($panics)) {
            $response = [
                "status" => "success",
                "message" => "Action completed successfully",
                "data" => [
                    "panics" => $panicArray
                ]
            ];
            return $response;
        } else if (empty($panics)) {
            $emptyResponse = [
                "status" => "empty",
                "message" => "No history of panics",
                "data" => []
            ];
            return $emptyResponse;
        }
    }
    
    function send_panic(Request $sendReq) {
        $user_id = request()->user()->id;
        $panic = new Panic;
        $dbRowsCreated = $panic->insert([
            "user_id" => $user_id,
            "longitude" => $sendReq->longitude,
            "latitude" => $sendReq->latitude,
            "panic_type" => $sendReq->panic_type,
            "details" => $sendReq->details
        ]);
        if($dbRowsCreated == 1) {
            $successResponse = [
                "status" => "success",
                "message" => "Panic raised successfully",
                "data" => [
                    "panic_id" => $panic->latest("id")->first()->id
                ]
            ];
            return $successResponse;//Status code 200 is returned by default
        } else {
            $errorResponse = [
                "status" => "error",
                "message" => "Failed to raise panic",
                "data" => []
            ];
            return $errorResponse;//Check status code for this. Should be 400 (see brief)
        }
    }

    function cancel_panic(Request $cancelReq) {
        $dbRowsDeleted = Panic::where("id", "=", $cancelReq->panic_id)->delete();
        if($dbRowsDeleted == 1) {
            $successResponse = [
                "status" => "success",
                "message" => "Panic cancelled successfully",
                "data" => []
            ];
            return response($successResponse, 200);
        } else {
            $errorResponse = [
                "status" => "error",
                "message" => "Failed to cancel panic",
                "data" => []
            ];
            return response($errorResponse, 400);
        }
    }
}
