<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionLogsController;
use App\Http\Controllers\SecurityLockingCodeController;
use App\Http\Controllers\LinesStatesController;
use App\Http\Controllers\CattleLogsController;
use Illuminate\Support\Facades\Validator;
use App\Models\carState;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\SmartGateState;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/iot/sync', [LinesStatesController::class,'get_min']);
Route::get('/lines', [LinesStatesController::class,'get_all']);
Route::post('/lines/state/set/{line_id}', [LinesStatesController::class,'change_line_state']);
Route::get('/lines/state/{line_id}', [LinesStatesController::class,'get_line_state']);
Route::post('/lines/maintenance/{option}', [LinesStatesController::class,'switch_on_or_off_maintance']);
Route::post('/lines', [LinesStatesController::class,'create_line']);
Route::get('/auth/password/forgotten', [SecurityLockingCodeController::class,'password_forgotten']);
Route::post('/auth/password/get_new', [SecurityLockingCodeController::class,'get_new']);
Route::get('/lines/logs/list', [ActionLogsController::class,'get_all']);
Route::post('/lines/logs', [ActionLogsController::class,'save']);
Route::get('/current_status', [LinesStatesController::class,'get_status']);
Route::get('/cattle', [CattleLogsController::class, 'index'])->middleware(\App\Http\Middleware\EnsureCorsAreEnabledWithCredentials::class);
Route::post('/cattle', [CattleLogsController::class, 'store'])->middleware(\App\Http\Middleware\EnsureCorsAreEnabledWithCredentials::class);
Route::get('/cattle/log/confirm', [CattleLogsController::class, 'confirm'])->middleware(\App\Http\Middleware\EnsureCorsAreEnabledWithCredentials::class);
Route::options('/cattle', function () {
    return response()->json([], 200); // Respond with an empty JSON response
})->middleware(\App\Http\Middleware\EnsureCorsAreEnabledWithCredentials::class);

Route::get('/cars/alcohol', function (Request $request) {
    $code = $request->code;
    $car = carState::all()->first();
    if (!$car) {
        return "not a valid operation";
    }
    try {
        $decrypted =  Crypt::decryptString(urldecode($code));

        if ($decrypted == $car->last_unlock_pass) {
            $car->last_unlock_pass = '1234'; //reset to default code to invalidate
            $car->locked_state = false;
            $car->save();
            return "Thank you for your confirmation!";
        } else {
            return "The link mismatch with the current unlocking code!";
        }
    } catch (DecryptException  $e) {
         return "The link is not genuine, we have sent the email with the correct code, kindly check your inbox";
    }
});

Route::post('/cars/alcohol', function (Request $request) {
    $jsonData = json_decode($request->getContent(), true);
    // Validate the parsed JSON data
    $validator = Validator::make($jsonData ?? [], [
            'alcohol' => 'required|integer',
            'emailto' => 'required|email',
            'detected' => 'required|string',
        ]);

    if ($validator->fails()) {
        // Validation failed
        return response()->json(['errors' => $validator->errors()], 422);
    }
    $car = carState::all()->first();
    $car = $car ?: new carState(["locked_state" => true]);
    $car->save();

    if ($jsonData['detected'] == 'y') {
        if (!$car->locked_state) {
        //send email and persit to DB
            $car->locked_state =  true;
            $randomString = Str::random(6); // Change the length as needed
            $car->last_unlock_pass = $randomString;
            $car->save();
            $crypted = Crypt::encryptString($randomString);

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom("ansimapersic@gmail.com", "Elvis Dev@");
            $email->setSubject("YOUR CAR IS PROTECTED");
            $email->addTo($jsonData['emailto'], "Admin");
            $email->addContent(
                "text/html",
                "<p style='line-height:1.6; font-family: Arial, Helvetica, sans-serif;'>
                Hey!
                <p>
                    Our system detected that the current driver of your car is drunk.(level was approximately " . $jsonData['alcohol'] . ")
                </p>
                <p>
                   For your car's safety, we have desactivated the engine.
                   <br>
                   Please <a href='" . env('APP_URL', 'https://demo.kvolts-lab.com') . "/api/cars/alcohol/?code=" . urlencode($crypted) . "'>click here</a> to unlock!
                </p>
                <hr>
                <p style='text-align:center; padding:30px; color:darkblue;'>
                    Car alcohol Detector | by Elvis@
                </p>
            </p>"
            );
            $sendgrid = new \SendGrid(config('custom.sendgrid_api'));
            try {
                $response = $sendgrid->send($email);
                http_response_code($response->statusCode());
            } catch (\Exception $e) {
                echo $e->getMessage();
                http_response_code(400);
            } finally {
                return "locked";
            }
        }
    }
    return $car->locked_state ? "locked" : "unlocked";
});

Route::get('/gate/changestate', function (Request $request) {
    $state = SmartGateState::find(1);
    $command = $request->get("command");
    if (!$command) {
        return "Failed to change the gate state";
    }
    if (str_contains($command, "open")) {
        $state->closed = false;
        $state->save();
        $gateOpeningVariants = [
            'Gate opening complete.',
            'Gate unlocked.',
            'Gate is now open.',
            'Proceed through the gate.',
            'Gate is unlocked and opening.',
            'Gate opening sequence initiated.',
            'Gate opening in progress.',
            'Gate opening successful.',
            'Gate is open, please proceed.',
            'Gate is now open for traffic.',
            'Gate is open, welcome!',
          ];
          // Get a random index in the array.
        $randomIndex = array_rand($gateOpeningVariants);

          // Get the random gate opening variant.
        $randomGateOpeningVariant = $gateOpeningVariants[$randomIndex];
        return $randomGateOpeningVariant;
    }
    if (str_contains($command, "close")) {
        $state->closed = true;
        $state->save();
        $gateClosingVariants = [
            'Gate closing complete.',
            'Gate locked.',
            'Gate is now closed.',
            'Gate is locked and closing.',
            'Gate closing sequence initiated.',
            'Gate closing in progress.',
            'Gate closing successful.',
            'Gate is closed, please do not enter.',
            'Gate is now closed for traffic.',
            'Gate is closed, thank you!',
          ];

          // Get a random index in the array.
        $randomIndex = array_rand($gateClosingVariants);

          // Get the random gate closing variant.
        $randomGateClosingVariant = $gateClosingVariants[$randomIndex];
        return $randomGateClosingVariant;
    }

    if (str_contains($command, "thank")) {
        $youAreWelcomeVariants = [
            "You're welcome.",
            "No problem.",
            "It's my pleasure.",
            "Anytime.",
            "Don't mention it.",
            "Happy to help.",
            "You got it.",
            "Not a big deal.",
            "My pleasure.",
            "It was nothing."
        ];

        // Pick a variant randomly
        $randomResponse = $youAreWelcomeVariants[array_rand($youAreWelcomeVariants)];
        return  $randomResponse;
    }

    return "We are not able to recognize this command at the moment!";
});

Route::get('/gate/state', function (Request $request) {
    $state = SmartGateState::find(1);
    return $state->closed;
});

Route::get('/gate/grant', function (Request $request) {
    $code = $request->get("code");
    if (Str::isUuid($code)) {
        echo "granted access";
    } else {
        echo "bad grant link";
    }
});

Route::get('/gate/knock', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return 'error';
    }

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("ansimapersic@gmail.com", "Elvis Dev@");
    $email->setSubject("SOMEONE WANT TO GET INTO YOUR HOUSE");
    $email->addTo($request->get('email'), "Admin");
    $email->addContent(
        "text/html",
        "<p style='line-height:1.6; font-family: Arial, Helvetica, sans-serif;'>
        Hey!
        <p>
           Hey, someone want to get access on your gate. 
        </p>
        <p>
            Do you recognize this request?
           <br>
           Please <a href='" . env('APP_URL', 'https://demo.kvolts-lab.com') . "/api/gate/grant?code=" . Str::uuid() . "'>click here</a> to grant access!
        </p>
        <hr>
        <p style='text-align:center; padding:30px; color:orange;'>
            Smart gate contoller | by Elvis@
        </p>
    </p>"
    );
    $sendgrid = new \SendGrid(config('custom.sendgrid_api'));
    try {
        $response = $sendgrid->send($email);
        http_response_code($response->statusCode());
    } catch (\Exception $e) {
        echo $e->getMessage();
        http_response_code(400);
    } finally {
        return "done";
    }
});
