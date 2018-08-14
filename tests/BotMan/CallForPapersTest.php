<?php

namespace Tests\BotMan;

use Tests\TestCase;

class CallForPapersTest extends TestCase
{

    public function testRespondsWithInformationAboutCallForPapers()
    {
        $responseText = $this->bot->receives('when is call for papers?')->getMessages()[0]->getText();

        $this->assertEquals('Call for papers has closed and speakers will be announced soon.', $responseText);
    }

}
