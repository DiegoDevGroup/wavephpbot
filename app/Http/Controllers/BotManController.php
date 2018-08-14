<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Carbon\Carbon;
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
        // get the id of the speaker the user is asking about
        $speaker = data_get($bot->getMessage()->getExtras(), 'apiParameters.speaker');

        // fetch the speaker details
        $speakerData = json_decode(
            file_get_contents("https://wavephp-conf.firebaseio.com/speakers/{$speaker}.json"),
            true
        );

        // create attachment with speaker image
        $attachment = new Image($speakerData['photo'], [
            'custom_payload' => true,
        ]);

        // build the speaker image message
        $message = OutgoingMessage::create()->withAttachment($attachment);

        // Reply with speaker image
        $bot->reply($message);

        // reply with the speaker's biography and twitter
        $result = $bot->reply(vsprintf("%s\n%s", [
            $speakerData['bio'],
            'https://twitter.com/' . $speakerData['twitter'],
        ]));
    }

    public function speaker_schedule(Botman $bot)
    {
        $speakerId = data_get($bot->getMessage()->getExtras(), 'apiParameters.speaker');

        $schedule = json_decode(file_get_contents('https://wavephp-conf.firebaseio.com/schedule.json'), true);

        $speakersPresentations = collect($schedule)
            // filter out the talks that do not belong to the given speaker
            ->filter(function ($presentation) use ($speakerId){
                return $presentation['speaker']['id'] === $speakerId;
            })
            // sort multiple talks by the start timestamp
            ->sortBy(function ($presentation){
                return $presentation['start'];
            })
            // map to a nice sentence
            ->map(function ($presentation){
                $title = $presentation['talk']['title'];
                $start = $presentation['start'];
                $location = $presentation['location'];

                return vsprintf('%s in %s at %s on %s', [
                    $title,
                    $location,
                    Carbon::parse($start)->format('g:ia'),
                    Carbon::parse($start)->format('l'),
                ]);
            })
            ->implode("\n");
        
        $bot->reply($speakersPresentations);
    }

    public function sponsor_information(Botman $bot)
    {
        // get the sponsor the user is asking about
        $sponsor = data_get($bot->getMessage()->getExtras(), 'apiParameters.sponsor');

        // fetch the sponsor details from Firebase
        $sponsorData = json_decode(
            file_get_contents("https://wavephp-conf.firebaseio.com/sponsors/{$sponsor}.json"),
            true
        );

        // generate formatted response text
        $formattedResponse = vsprintf("%s\n%s\n%s", [
            $sponsorData['description'],
            $sponsorData['url'],
            'https://twitter.com/' . $sponsorData['twitter'],
        ]);

        // create attachment with sponsor logo image
        $attachment = new Image($sponsorData['logo'], [
            'custom_payload' => true,
        ]);

        // build the logo message
        $message = OutgoingMessage::create()
                                  ->withAttachment($attachment);

        // send the logo
        $bot->reply($message);

        // send text
        $bot->reply($formattedResponse);
    }
}
