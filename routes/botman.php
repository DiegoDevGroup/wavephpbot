<?php

use BotMan\BotMan\Middleware\ApiAi;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$dialogflow = ApiAi::create(config('wave.dialogflow_key'))->listenForAction();
$botman->middleware->received($dialogflow);

$botman->group(['middleware' => $dialogflow], function ($bot){
    $bot->hears('cfp', BotManController::class . '@cfp');
    $bot->hears('greet', BotManController::class . '@greet');
    $bot->hears('help', BotManController::class . '@help');
    $bot->hears('info', BotManController::class . '@info');
    $bot->hears('speaker', BotManController::class . '@speaker');
});
