<?php

namespace Tests\BotMan;

use Tests\TestCase;

class SponsorInformationTest extends TestCase
{

    public function testRespondsToRequestForSpecificSponsorInformation()
    {
        $responseText = $this->bot->receives('tell me about osmi')->getMessages()[1]->getText();

        $expectedFragment = 'is a non-profit';

        $this->assertTrue(str_contains($responseText, $expectedFragment));
    }

}
