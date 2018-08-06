<?php

namespace Tests\BotMan;

use Tests\TestCase;

class GreetingTest extends TestCase
{

    public function testGreetsUsers()
    {
        $responseText = $this->bot->receives('Hi')->getMessages()[0]->getText();

        $this->assertContains($responseText, [
            'Hello Marcel',
            'Hey there Marcel',
            'Greetings Marcel',
            'Yo, what\'s up Marcel',
            'Salutations Marcel',
            'Bonjour Marcel',
            'Hola Marcel',
            'Namaste Marcel',
        ]);
    }

}
