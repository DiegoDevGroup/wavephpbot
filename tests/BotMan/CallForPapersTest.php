<?php

namespace Tests\BotMan;

use Tests\TestCase;

class CallForPapersTest extends TestCase
{

    public function testRespondsWithInformationAboutCallForPapers()
    {
        $responseText = $this->bot->receives('when is call for papers?')->getMessages()[0]->getText();

        $expectedFragment = 'Call for papers has closed';

        $this->assertTrue(str_contains($responseText, $expectedFragment));
    }

}
