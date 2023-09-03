<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CattleStateLogs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class CattleLogsController extends Controller
{
    public function index(Request $request)
    {
        $device_uuid = $request->cookie('X-device-uuid');
        if (!$device_uuid) {
            $device_uuid = (string) Str::uuid();
            return response(CattleStateLogs::orderBy('created_at', 'desc')->select('created_at', 'json_data')->get())->cookie('X-device-uuid', $device_uuid, 5256000);
        }
        $not_sent_logs = CattleStateLogs::whereJsonDoesntContain('seen_by', $device_uuid)->orderBy('created_at', 'desc')
                        ->limit(10)->select('created_at', 'json_data')->get();
        return response($not_sent_logs);
    }

    public function confirm(Request $request)
    {
        $device_uuid = $request->cookie('X-device-uuid');
        if (!Str::isUuid($device_uuid)) {
            return response(['error' => 'Unrecognized device'], 403);
        }
        $not_sent_logs = CattleStateLogs::whereJsonDoesntContain('seen_by', $device_uuid)->orderBy('created_at', 'desc')
                        ->limit(10)->get();
        $not_sent_logs->each(function ($log) use ($device_uuid) {
            $seenBy = json_decode($log->seen_by, true);
            $seenBy[] = $device_uuid; //push
            $log->seen_by = json_encode($seenBy);
            $log->save();
        });

        return ['message' => 'okay'];
    }

    public function store(Request $request)
    {
        // Validate the parsed JSON data
        $validator = Validator::make($request->json_data, [
            'bpm' => 'required|integer',
            'temperature' => 'required|numeric',
            'acceleration_x' => 'required|numeric',
            'acceleration_y' => 'required|numeric',
            'acceleration_z' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            // Validation failed
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new CattleStateLogs instance
        $cattleStateLog = new CattleStateLogs();

        // Fill the model with the validated JSON data and set 'sent' to false
        $cattleStateLog->json_data = json_encode($request->json_data);

        // Save the record to the database
        $cattleStateLog->save();

        return response()->json(['message' => 'Log saved successfully'], 200); // Respond with a success message and HTTP status 201 (Created)
    }
}
