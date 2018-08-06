<?php

namespace Tests\BotMan;

use Tests\TestCase;

class SpeakerTest extends TestCase
{

    public function testRespondsToRequestForSpecificSpeakerInformation()
    {
        $responseText = $this->bot->receives('tell me about Juan Torres')->getMessages()[0]->getText();

        $expectedFragment = 'Juan started programming back in 1999 using C';

        $this->assertTrue(str_contains($responseText, $expectedFragment));
    }

}
