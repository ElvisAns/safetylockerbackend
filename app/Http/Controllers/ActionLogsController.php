<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ActionLogs;
use Exception;

class ActionLogsController extends Controller
{
    public function save(Request $request){
        $log = new ActionLogs();
        $log->type = $request->input('type');
        $log->info = $request->input('info');

        try{
            $log->save();
            return ['message'=>"logs saved with success"];
        }
        catch(\Exception $e){
            return ['error'=>true,'message'=>'Error inserting the log information'];
        }
    }

    public function get_all(){
        $res = ActionLogs::latest()->limit(5)->get();
        return ['data'=>$res];
    }
}
