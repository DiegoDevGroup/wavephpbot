<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;

class BotManController extends Controller
{

    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    public function cfp(Botman $bot)
    {
        $bot->reply('Call for papers has closed and speakers will be announced soon.');
    }

    public function greet(Botman $bot)
    {
        $user = $bot->getUser();

        $greetings = array_random([
            'Hello',
            'Hey there',
            'Greetings',
            'Yo, what\'s up',
            'Salutations',
            'Bonjour',
            'Hola',
            'Namaste',
        ]);

        $bot->reply($greetings . ' ' . $user->getFirstName());
    }

    public function help(Botman $bot)
    {
        $bot->reply('You can ask me a couple things like "When is the conference?" or "Who is speaking?".');
    }

    public function info(Botman $bot)
    {
        $user = $bot->getUser();

        $bot->reply('WavePHP will be from ' . config('wave.start') . ' until ' . config('wave.end') . ' at ' . config('wave.venue') .
            '. Tickets can be purchased at this url => ' . config('wave.purchase') .
            '. You can also reserve a room at the conference hotel at a discounted rate at this url => ' . config('wave.venue_booking') .
            '. Hope to see you there ' . $user->getFirstName() . '!');
    }

    public function speaker(Botman $bot)
    {
        $bot->reply('Speakers have not been announced yet. Be patient, we promise it will be worth it.');
    }

    public function speaker_bio(Botman $bot)
    {
        $speaker = data_get($bot->getMessage()->getExtras(), 'apiParameters.speaker');

        $url = "https://wavephp-conf.firebaseio.com/speakers/{$speaker}.json";

        $results = json_decode(file_get_contents($url), true);

        $bio = $results['bio'];
        $photo_url = $results['photo'];

        $bot->reply($bio);

        // Create attachment
        $attachment = new Image($photo_url, [
            'custom_payload' => true,
        ]);

        // Build message object
        $message = OutgoingMessage::create()->withAttachment($attachment);

        // Reply message object
        $bot->reply($message);
    }

    public function sponsor_information(Botman $bot)
    {
        $sponsor = data_get($bot->getMessage()->getExtras(), 'apiParameters.sponsor');

        $url = "https://wavephp-conf.firebaseio.com/sponsors/{$sponsor}.json";

        $results = json_decode(file_get_contents($url), true);

        $name = $results['name'];
        $description = $results['description'];
        $twitter = $results['twitter'];
        $url = $results['url'];
        $logo = $results['logo'];

        $attachment = new Image($logo, [
            'custom_payload' => true,
        ]);

        $message = OutgoingMessage::create()->withAttachment($attachment);
        $bot->reply($message);

        $bot->reply(vsprintf("%s: %s\n%s", [
            $name,
            $description,
            $url,
        ]));
    }
}
