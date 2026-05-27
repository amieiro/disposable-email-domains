<?php

namespace Tests\Unit;

use App\Console\Commands\CreateDisposableEmailDomainsFilesCommand;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RobustFetchingTest extends TestCase
{
    private CreateDisposableEmailDomainsFilesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CreateDisposableEmailDomainsFilesCommand;
    }

    public function test_lines_from_text_splits_on_newlines(): void
    {
        $this->assertSame(['a.com', 'b.com', 'c.com'], $this->command->linesFromText("a.com\nb.com\nc.com"));
    }

    public function test_lines_from_text_handles_carriage_returns(): void
    {
        $this->assertSame(['a.com', 'b.com'], $this->command->linesFromText("a.com\r\nb.com"));
    }

    public function test_lines_from_text_preserves_internal_blank_lines(): void
    {
        $this->assertSame(['a.com', '', 'c.com'], $this->command->linesFromText("a.com\n\nc.com"));
    }

    public function test_lines_from_text_drops_the_trailing_newline(): void
    {
        $this->assertSame(['a.com'], $this->command->linesFromText("a.com\n"));
    }

    public function test_lines_from_text_on_empty_string_is_empty(): void
    {
        $this->assertSame([], $this->command->linesFromText(''));
    }

    public function test_decode_json_domains_returns_the_array(): void
    {
        $this->assertSame(['a.com', 'b.com'], $this->command->decodeJsonDomains('["a.com","b.com"]'));
    }

    public function test_decode_json_domains_on_invalid_json_returns_empty(): void
    {
        $this->assertSame([], $this->command->decodeJsonDomains('this is not json'));
    }

    public function test_decode_json_domains_on_non_array_returns_empty(): void
    {
        $this->assertSame([], $this->command->decodeJsonDomains('"a.com"'));
    }

    public function test_retries_on_a_connection_error(): void
    {
        $exception = new ConnectException('boom', new Request('GET', 'https://u.test'));
        $this->assertTrue($this->command->shouldRetry(0, null, $exception));
    }

    public function test_retries_on_a_server_error(): void
    {
        $this->assertTrue($this->command->shouldRetry(0, new Response(503), null));
    }

    public function test_does_not_retry_on_a_successful_response(): void
    {
        $this->assertFalse($this->command->shouldRetry(0, new Response(200), null));
    }

    public function test_does_not_retry_after_the_maximum_attempts(): void
    {
        $this->assertFalse($this->command->shouldRetry(2, new Response(503), null));
    }
}
