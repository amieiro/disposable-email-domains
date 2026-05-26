<?php

namespace Tests\Unit;

use App\Console\Commands\CreateDisposableEmailDomainsFilesCommand;
use PHPUnit\Framework\TestCase;

class DenyListSizeGuardTest extends TestCase
{
    private CreateDisposableEmailDomainsFilesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CreateDisposableEmailDomainsFilesCommand;
    }

    public function test_first_run_without_a_previous_baseline_is_accepted(): void
    {
        $this->assertTrue($this->command->isDenyListSizeAcceptable(100, 0));
    }

    public function test_a_larger_list_is_accepted(): void
    {
        $this->assertTrue($this->command->isDenyListSizeAcceptable(200, 100));
    }

    public function test_a_small_shrink_within_the_threshold_is_accepted(): void
    {
        // floor(100 * 0.9) = 90, so 95 is acceptable.
        $this->assertTrue($this->command->isDenyListSizeAcceptable(95, 100));
    }

    public function test_a_shrink_exactly_at_the_threshold_is_accepted(): void
    {
        $this->assertTrue($this->command->isDenyListSizeAcceptable(90, 100));
    }

    public function test_a_catastrophic_shrink_is_rejected(): void
    {
        // floor(100 * 0.9) = 90, so 50 is below the floor and must be rejected.
        $this->assertFalse($this->command->isDenyListSizeAcceptable(50, 100));
    }
}
