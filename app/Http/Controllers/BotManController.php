<?php

namespace App\Http\Controllers;

use App\Conversations\AddRepositoryConversation;

class BotManController extends Controller
{
    protected $botman;

    public function handle()
    {
        $this->botman = app('botman');

        $this->botman->hears('^/start(@dev_releases_bot)?$', function ($bot) {
            $bot->reply('Hello, see the commands list.');
        });

        $this->botman->hears('^/addrepository(@dev_releases_bot)?$', function ($bot) {
            $bot->startConversation(new AddRepositoryConversation);
        });

        $this->botman->listen();
    }
}
