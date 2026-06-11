<?php

namespace Tests\Unit;

use App\Console\Commands\CreateDisposableEmailDomainsFilesCommand;
use PHPUnit\Framework\TestCase;

class RunMetadataTest extends TestCase
{
    private CreateDisposableEmailDomainsFilesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CreateDisposableEmailDomainsFilesCommand;
    }

    public function test_build_metadata_contains_the_counts(): void
    {
        $this->assertSame(
            ['denyDomains' => 100, 'allowDomains' => 5, 'secureDomains' => 7],
            $this->command->buildMetadata(100, 5, 7)
        );
    }

    public function test_write_metadata_writes_parseable_json(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'ded-meta-');

        try {
            $this->command->writeMetadata($path, 198000, 960, 230);

            $decoded = json_decode(file_get_contents($path), true);
            $this->assertSame(
                ['denyDomains' => 198000, 'allowDomains' => 960, 'secureDomains' => 230],
                $decoded
            );
        } finally {
            @unlink($path);
        }
    }
}
