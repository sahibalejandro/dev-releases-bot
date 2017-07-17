<?php

use App\Repository;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function find_a_repository_by_name()
    {
        $repository = factory('App\Repository')->make();

        $this->assertNull(Repository::byName($repository->name));

        $repository->save();

        $this->assertInstanceOf(Repository::class, Repository::byName($repository->name));
    }

    /** @test */
    public function check_if_a_chat_is_subscribed_to_a_repository()
    {
        $repository = factory('App\Repository')->create();

        $this->assertFalse($repository->isWatchedBy(12345));

        $subscription = factory('App\Subscription')->create([
            'repository_id' => $repository->id,
            'chat_id' => 12345,
        ]);

        $this->assertTrue($repository->isWatchedBy(12345));
    }

    /** @test */
    public function subscribe_a_chat_to_a_repository()
    {
        $repository = factory('App\Repository')->create();
        $this->assertFalse($repository->isWatchedBy(12345));

        $repository->subscribe(12345);

        $this->assertTrue($repository->isWatchedBy(12345));
    }

    /** @test */
    public function check_if_a_repository_name_is_valid()
    {
        $this->assertFalse(Repository::isValidName('foobar'));
        $this->assertTrue(Repository::isValidName('foo/bar'));
        $this->assertTrue(Repository::isValidName('foo_bar/baz_blim'));
        $this->assertTrue(Repository::isValidName('foo-bar/baz-blim'));
        $this->assertTrue(Repository::isValidName('foo.bar/baz.blim'));
        $this->assertTrue(Repository::isValidName('CamelCase/Repository'));
    }
}
