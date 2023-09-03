<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CattleStateLogs;
use Illuminate\Support\Facades\Validator;

class CattleLogsController extends Controller
{
    public function index(Request $request)
    {
        $not_sent_logs = CattleStateLogs::where('sent', false)->get();
        CattleStateLogs::where('sent', false)->update(['sent' => true]);
        return $not_sent_logs;
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
        $cattleStateLog->sent = false; // Assuming you want to set 'sent' to false by default

        // Save the record to the database
        $cattleStateLog->save();

        return response()->json(['message' => 'Log saved successfully'], 200); // Respond with a success message and HTTP status 201 (Created)
    }
}
