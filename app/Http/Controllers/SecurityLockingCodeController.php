<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecurityLockingCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SecurityLockingCodeController extends Controller
{
    public function get_new(Request $request){
        $security_code = SecurityLockingCode::where("security_code",$request->input('security_code'))->first();
        if(!$security_code){
            http_response_code(401);
            return ["error"=>true, "message"=>"You are not allowed to perform this action, please supply the correct password"];
        }
        $new_code = Str::random(5);
        $new_code = Str::upper($new_code);
        $security_code->security_code = $new_code;
        $security_code->update();

        return ["message"=>$new_code];
    }

    public function password_forgotten(){
        $pin = SecurityLockingCode::get()->first();
        $mail1 = env("EMAIL_ADDRESS_ADMIN");
        $mail2 = env("EMAIL_ADDRESS_OWNER");
        
        $email =new \SendGrid\Mail\Mail(); 
        $email->setFrom("ansimapersic@gmail.com", "Elvis Dev@");
        $email->setSubject("YOUR PIN SAFETY LOCKER PIN");
        $email->addTo($mail2, "Admin User");
        $email->addTo($mail1, "Root User");
        $email->addContent(
            "text/html", 
            "<p style='line-height:1.6; font-family: Arial, Helvetica, sans-serif;'>
                Hey!
                <p>
                    Someone claimed he forget the current pin for the safety locker, you are the admin and we trust you
                    care about your worker. Make sure you trust someone you want to give it to.
                </p>
                <p>
                    The current locker pin is <strong style='color:darkblue;'>$pin->security_code</strong>
                </p>
                <hr>
                <p style='text-align:center; padding:30px; color:darkblue;'>
                    Security locker | by Elvis@perfecto-group
                </p>
            </p>"
        );
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY_FULL'));
        try {
            $response = $sendgrid->send($email);
            http_response_code($response->statusCode());
            return ["message"=>"Success, the current pin has been sent to $mail1"];
        } catch (\Exception $e) {
            http_response_code(400);
            Log::error('Caught exception: '. $e->getMessage());
            return ["error"=>true,"message"=>"The email with the current pin was not delivered please try again soon"];
        }
    }
}
