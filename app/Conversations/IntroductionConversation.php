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
        $this->say('Hi! I am the WavePHP Bot.');
    }
}
