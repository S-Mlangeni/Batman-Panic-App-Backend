<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Panic;
use App\Models\User;

class PanicController extends Controller
{
    function panic_history() {
        $panics = Panic::all();
        if (sizeof($panics)) {
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
            $response = [
                "status" => "success",
                "message" => "Action completed successfully",
                "data" => [
                    "panics" => $panicArray
                ]
            ];
            return response($response, 200);
        } else {
            $emptyResponse = [
                "status" => "not found",
                "message" => "No history of panics",
                "data" => []
            ];
            return response($emptyResponse, 404);
        }
    }
    
    function send_panic(Request $sendReq) {
        $user_id = request()->user()->id;
        error_log($user_id);
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
            return response($successResponse, 200);
        } else {
            $errorResponse = [
                "status" => "error",
                "message" => "Failed to raise panic",
                "data" => []
            ];
            return response($errorResponse, 400);
        }
    }

    function cancel_panic(Request $cancelReq) {
        $user_id = request()->user()->id;
        error_log($user_id);
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
