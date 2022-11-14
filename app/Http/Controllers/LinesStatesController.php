<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinesStates;
use App\Models\SecurityLockingCode;
use App\Models\ActionLogs;

class LinesStatesController extends Controller
{
    function create_line(Request $request){
        $line = new LinesStates();
        $line->line_name = $request->input('line_name');
        $line->state = $request->input('state');
        try{
            $line->save();
            return ['message'=>'line created with success'];
        }
        catch(\Exception $e){
            http_response_code(400);
            return ['error'=>true,'message'=>'failed to create line, possibly duplicate entry or bad request'];
        }
    }

    function get_all(){
        return LinesStates::get()->all();
    }

    function get_line_state($line_id){
        $line = LinesStates::findOrFail($line_id);
        return $line;
    }

    function change_line_state($line_id, Request $request){
        $security_code = SecurityLockingCode::where("security_code",$request->input('security_code'))->first();
        if(!$security_code){
            http_response_code(401);
            return ["error"=>true, "message"=>"You are not allowed to perform this action, please supply the correct password"];
        }
        $line = LinesStates::findOrFail($line_id);
        $line->state = $request->input('state');
        $state =  $request->input('state')?'ON':'OFF';
        $name = $request->input('name');
        $tel = $request->input('telephone');
        $logs = ActionLogs::where("type","System maintance")->latest()->first();
        if(preg_match('/OFF/',$logs->info)){
            http_response_code(400);
            return ["error"=>true,'message'=>"You must switch to maintance before controlling the lines states"];
        }
        try{
            $line->save();
            ActionLogs::create([
                "type"=>"State changed",
                "info" =>"$line->line_name state has been updated to $state <!> $name, tel : $tel",
            ]);
            return ['message'=>"success, the $line->line_name state has been updated to $state"];
        }
        catch(\Exception $e){
            http_response_code(400);
            return ['message'=>'failed to update the state'];
        }
    }

    function switch_on_or_off_maintance($option, Request $request){
        $security_code = SecurityLockingCode::where("security_code",$request->input('security_code'))->first();
        if(!$security_code){
            http_response_code(401);
            return ["error"=>true, "message"=>"You are not allowed to perform this action, please supply the correct password"];
        }
        $maintance = $option=="on"?"ON":"OFF";
        $name = $request->input('name');
        $tel = $request->input('telephone');
        ActionLogs::create([
            "type"=>"System maintance",
            "info" =>"The system mode has switched to maintance: $maintance <!> $name, tel : $tel"
        ]);
        if($option=="on") return ['message'=>"The system mode has switched to maintance: $maintance"];
        
        LinesStates::where("state",false)->update(["state"=>true]);

        return ['message'=>"The system mode has switched to maintance: $maintance and all the lines are ON"];
    }

    function get_status(){
        $logs = ActionLogs::where("type","System maintance")->latest()->first();
        if(preg_match('/OFF/',$logs->info)){
            return ['message'=>"Running"];
        }
        return ['message'=>"Under maintenance"];
    }
}
