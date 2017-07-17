<?php

namespace App\Console\Commands;

use App\Repository;
use App\RepositoryTags;
use Illuminate\Console\Command;
use App\Jobs\ReleaseNotification;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RepositoryNotFound;

class NotifyCommand extends Command
{
    protected $signature = 'bot:notify';
    protected $description = 'Send new tag notifications to users.';

    public function handle()
    {
        $repositories = Repository::all();

        $repositories->each(function ($repository) {

            try {
                $tags = new RepositoryTags($repository->name);
            } catch (RepositoryNotFound $e) {
                Log::warning("Repository {$repository->name} not found.");
                $repository->delete();
                return;
            }

            if ($tags->empty()) {
                return;
            }

            $lastTagName = $tags->latest()->name;

            if ($repository->last_tag_name == $lastTagName) {
                return;
            }

            $repository->last_tag_name = $lastTagName;
            $repository->save();

            $repository->subscriptions->each(function ($subscription) use ($repository, $tags) {
                dispatch(new ReleaseNotification($repository, $tags->latest(), $subscription->chat_id));
            });
        });
    }
}