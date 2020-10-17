<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorktimeEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_returns_colliding_entries()
    {
        $collidingEntry1 = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $entry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T08:00:00+00:00',
            'ended_at' => '2020-10-16T10:30:00+00:00',
            'project_id' => null
        ]);

        $entry->collidingEntries()->attach($collidingEntry1);

        $this->assertEquals(
            $entry->collidingEntries()->first()->id,
            $collidingEntry1->id);
    }

    /** @test */
    public function it_returns_entries_that_it_collides_with()
    {
        $collidingEntry1 = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $entry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T08:00:00+00:00',
            'ended_at' => '2020-10-16T10:30:00+00:00',
            'project_id' => null
        ]);

        $entry->collidingEntries()->attach($collidingEntry1);

        $this->assertEquals(
            $collidingEntry1->entriesCollidingWith()->first()->id,
            $entry->id);
    }
}
