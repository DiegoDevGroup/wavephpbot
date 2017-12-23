<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

class IntroductionConversation extends Conversation
{
    /**
     * Start the conversation
     */
    public function run()
    {
        $question = Question::create( 'Hey there, I am the WavePHPBot. Is there something I can help you with?');

        $this->ask( $question, function (Answer $answer) {
            if ($answer->getText() === 'yes') {
                $this->say('Wonderful, what can I help you with?');
            } else {
                $this->say('No problem, thanks for stopping by');
            }
        });
    }
}
