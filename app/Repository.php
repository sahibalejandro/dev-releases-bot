<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    protected $guarded = [];

    /**
     * Find a repository by name.
     * 
     * @param  string $name
     * @return \App\Repository|null
     */
    public static function byName($name)
    {
        return self::where('name', $name)->first();
    }

    /**
     * Check if the given repository name is valid.
     *
     * @param  string $name
     * @return bool
     */
    public static function isValidName($name)
    {
        return preg_match('/^[a-z0-9-_\.]+\/[a-z0-9-_\.]+$/i', $name) > 0;
    }

    /**
     * Check if this repository has a subscription for the chat with
     * the given id.
     * 
     * @param  int $id
     * @return bool
     */
    public function isWatchedBy($id)
    {
        return $this->subscriptions()->where('chat_id', $id)->exists();
    }

    /**
     * Subscriptions to this repository.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Subscribe a chat to this repository.
     * 
     * @param  int $chatId
     */
    public function subscribe($chatId)
    {
        $this->subscriptions()->save(
            new Subscription(['chat_id' => $chatId])
        );
    }
}
