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

    public function cfp(Botman $bot)
    {
        $bot->reply('Call for papers has closed and you can get speaking information by asking "who is speaking?"');
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
        $bot->reply('You can ask me a few things like such as 
        "When is the conference?", 
        "Who is speaking?"');

        $bot->typesAndWaits(1);

        $bot->reply('You can even ask me about a specific speaker or sponsor with 
        "Tell me about SDPHP" or 
        "tell me about Cal Evans"');
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
        $speakers = json_decode(
            file_get_contents("https://www.wavephp.com/api/speakers"),
            true
        );


        $reply = '';
        foreach ($speakers as $speaker) {

            $reply .= ($speaker['twitter'] != null) ? $speaker['first_name'].' '.
                $speaker['last_name'] .' (@'. $speaker['twitter'] .") \n " :
                $speaker['first_name'].' '. $speaker['last_name'] ." \n ";
        }
        $bot->reply('Our awesome speaker line up:');
        $bot->reply($reply);
        $bot->reply('You can find out more about a speaker by typing');
        $bot->reply('`tell me about <speaker\'s name>`');
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
        $bot->reply(vsprintf("%s\n%s", [
            $speakerData['bio'],
            'https://twitter.com/' . $speakerData['twitter'],
        ]));
    }

    public function speaker_schedule(Botman $bot)
    {
        // get the id of the speaker the user is asking about
        $speakerId = data_get($bot->getMessage()->getExtras(), 'apiParameters.speaker');

        // fetch the speaker details
        $speakerData = json_decode(
            file_get_contents("https://wavephp-conf.firebaseio.com/speakers/{$speakerId}.json"),
            true
        );

        // get the schedule for WavePHP
        $schedule = json_decode(file_get_contents('https://wavephp-conf.firebaseio.com/schedule.json'), true);

        // map the speaker's presentations to a nice format
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
                return vsprintf('%s in %s at %s on %s', [
                    $presentation['talk']['title'],
                    $presentation['location'],
                    Carbon::parse($presentation['start'])->format('g:ia'),
                    Carbon::parse($presentation['start'])->format('l'),
                ]);
            });

        // format the response
        $responseText = vsprintf("%s's %s:\n%s", [
            $speakerData['name'],
            str_plural('presentation', $speakersPresentations->count()),
            $speakersPresentations->implode("\n"),
        ]);

        // send reply
        $bot->reply($responseText);
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
