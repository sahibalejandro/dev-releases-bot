<?php

namespace App\Jobs;

use App\Repository;

class ReleaseNotification extends Job
{
    protected $chatId;
    protected $repository;
    protected $tag;

    public function __construct(Repository $repository, $tag, $chatId)
    {
        $this->tag = $tag;
        $this->chatId = $chatId;
        $this->repository = $repository;
    }

    public function handle()
    {
        $message = sprintf('*%s* has been tagged to *%s*, [click here](https://github.com/%s/releases/tag/%s) to check it out.',
            $this->repository->name,
            $this->tag->name,
            $this->repository->name,
            $this->tag->name
        );

        app('botman')->say(
            $message,
            $this->chatId,
            null,
            [
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
            ]
        );
    }
}
