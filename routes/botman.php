<?php

use BotMan\BotMan\Middleware\ApiAi;
use App\Http\Controllers\BotManController;
use App\Conversations\IntroductionConversation;

$botman = resolve('botman');

$dialogflow = ApiAi::create(config('wave.dialogflow_key'))->listenForAction();
$botman->middleware->received($dialogflow);

$botman->hears('greet', function ($bot) {
    $bot->reply('Hello there.');
    //$bot->startConversation(
    //    new IntroductionConversation()
    //);
})->middleware($dialogflow);

$botman->hears('Start conversation', BotManController::class.'@startConversation');
