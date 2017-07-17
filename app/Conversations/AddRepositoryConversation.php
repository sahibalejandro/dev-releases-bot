<?php

namespace App\Conversations;

use App\Repository;
use App\RepositoryTags;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Conversation;
use App\Jobs\ReleaseNotification;
use App\Exceptions\RepositoryNotFound;

class AddRepositoryConversation extends Conversation
{
    protected $additionalParameters = [
        'parse_mode' => 'markdown',
        'disable_notification' => true,
    ];

    public function run()
    {
        $this->askRepositoryUrl();
    }

    public function askRepositoryUrl($question = null)
    {
        if (is_null($question)) {
            $question = 'Please type the repository name, for example: *vendor/package*';
        }

        $this->ask($question, function (Answer $answer) {
            // Get the CHAT ID to support group chats.
            $chatId = $this->bot->getMessage()->getChannel();
            $repositoryName = $answer->getText();

            if (! Repository::isValidName($repositoryName)) {
                $this->askRepositoryUrl('Invalid repository name, try again.');
                return;
            }

            try {
                $tags = new RepositoryTags($repositoryName);
            } catch (RepositoryNotFound $e) {
                $this->say("The repository *{$repositoryName}* does not exists.", $this->additionalParameters);
                return;
            }

            $repository = Repository::byName($repositoryName);
            $lastTagName = $tags->empty() ? null : $tags->latest()->name;

            if ($repository) {
                $repository->last_tag_name = $lastTagName;
                $repository->save();
            } else {
                $repository = Repository::create([
                    'name' => $repositoryName,
                    'last_tag_name' => $lastTagName,
                ]);
            }

            if (! $repository->isWatchedBy($chatId)) {
                $repository->subscribe($chatId);
                $this->say("I'll keep you posted when *{$repositoryName}* is tagged.", $this->additionalParameters);
            } else {
                $this->say("You are already watching repository *{$repositoryName}*.", $this->additionalParameters);
            }

            if ($tags->empty()) {
                $this->say("No tags published for *{$repositoryName}* until now.", $this->additionalParameters);
            } else {
                dispatch(new ReleaseNotification($repository, $tags->latest(), $chatId));
            }

        }, $this->additionalParameters);
    }
}