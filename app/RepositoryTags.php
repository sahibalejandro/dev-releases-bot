<?php

namespace App;

use Zttp\Zttp;
use App\Exceptions\NoTagsAvailable;
use App\Exceptions\RepositoryNotFound;

class RepositoryTags
{
    protected $tags = [];

    public function __construct($repositoryName)
    {
        $this->repositoryName = $repositoryName;
        $zttpResponse = Zttp::get("https://api.github.com/repos/{$repositoryName}/tags");

        if ($zttpResponse->status() == 404) {
            throw new RepositoryNotFound("Repository {$repositoryName} not found.");
        }

        $this->tags = json_decode($zttpResponse->response->getBody(), $assoc = false);
    }

    public function empty()
    {
        return count($this->tags) == 0;
    }

    public function latest()
    {
        if ($this->empty()) {
            throw new NoTagsAvailable("No tags available for repository {$this->repositoryName}.");
        }

        return $this->tags[0];
    }
}