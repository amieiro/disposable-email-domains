<?php

namespace Tests\Feature;

use App\Console\Commands\CreateDisposableEmailDomainsFilesCommand;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use Tests\TestCase;

class FetchBodiesTest extends TestCase
{
    private CreateDisposableEmailDomainsFilesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        // Absorb the warning/error log calls so the test does not write log files.
        Log::spy();
        $this->command = new CreateDisposableEmailDomainsFilesCommand;
    }

    public function test_fetch_bodies_returns_only_the_successful_responses(): void
    {
        $mock = new MockHandler([
            new Response(200, [], "a.com\nb.com"),
            new Response(404, [], 'not found'),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false]);
        $this->command->setClient($client);

        $bodies = $this->invokeFetchBodies(['https://u1.test', 'https://u2.test']);

        $this->assertSame(['https://u1.test' => "a.com\nb.com"], $bodies);
    }

    private function invokeFetchBodies(array $urls): array
    {
        $method = (new ReflectionClass($this->command))->getMethod('fetchBodies');
        $method->setAccessible(true);

        return $method->invoke($this->command, $urls);
    }
}
