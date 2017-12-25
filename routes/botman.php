<?php

use BotMan\BotMan\Middleware\ApiAi;
use App\Http\Controllers\BotManController;
use App\Conversations\IntroductionConversation;

$botman = resolve('botman');

$dialogflow = ApiAi::create(config('wave.dialogflow_key'))->listenForAction();
$botman->middleware->received($dialogflow);

$botman->hears('greet', function ($bot) {
    $bot->reply('Hello there.');
})->middleware($dialogflow);

$botman->hears('info', function ($bot) {
    $user = $bot->getUser();
    //$extras = $bot->getMessage()->getExtras();
    //$bot->reply($extras['apiReply']);
    $bot->reply('WavePHP will be from '.config('wave.start').' until '. config('wave.end').' at '.config('wave.venue') .
        '. Tickets can be purchased at this url => '. config('wave.purchase') .
        '. You can also reserve a room at the conference hotel at a discounted rate at this url => ' . config('wave.venue_booking') .
        '. Hope to see you there '. $user->getFirstName());
})->middleware($dialogflow);

$botman->hears('speaker', function ($bot) {
    $bot->reply('Speakers have not been announced yet. Be patient, we promise it will be worth it.');
})->middleware($dialogflow);

$botman->hears('cfp', function ($bot) {
    $bot->reply('Call for papers will be opened sometime in January.');
})->middleware($dialogflow);

$botman->hears('help', function ($bot) {
    $bot->reply('You can ask me a couple thing like "When is the conference?" or "Who is speaking?".');
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');
