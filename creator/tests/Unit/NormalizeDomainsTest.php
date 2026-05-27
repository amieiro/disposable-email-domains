<?php

namespace Tests\Unit;

use App\Console\Commands\CreateDisposableEmailDomainsFilesCommand;
use PHPUnit\Framework\TestCase;

class NormalizeDomainsTest extends TestCase
{
    private CreateDisposableEmailDomainsFilesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CreateDisposableEmailDomainsFilesCommand;
    }

    public function test_lowercases_the_domain(): void
    {
        $this->assertSame('example.com', $this->command->normalizeDomain('Example.COM'));
    }

    public function test_trims_surrounding_whitespace(): void
    {
        $this->assertSame('example.com', $this->command->normalizeDomain('  example.com  '));
    }

    public function test_strips_a_trailing_dot(): void
    {
        $this->assertSame('example.com', $this->command->normalizeDomain('example.com.'));
    }

    public function test_lowercases_unicode_without_converting_to_punycode(): void
    {
        $this->assertSame('münchen.de', $this->command->normalizeDomain('MÜNCHEN.DE'));
    }

    public function test_leaves_an_existing_punycode_domain_unchanged(): void
    {
        $this->assertSame('xn--mnchen-3ya.de', $this->command->normalizeDomain('xn--mnchen-3ya.de'));
    }

    public function test_preserves_underscores_while_lowercasing(): void
    {
        $this->assertSame('foo_bar.com', $this->command->normalizeDomain('Foo_Bar.com'));
    }

    public function test_lowercases_without_rejecting_unusual_shapes(): void
    {
        // Normalization does not validate the domain, it only canonicalizes case
        // and trimming, so an odd shape is kept rather than dropped.
        $this->assertSame('a..b.com', $this->command->normalizeDomain('A..B.com'));
    }

    public function test_an_empty_or_whitespace_string_normalizes_to_empty(): void
    {
        $this->assertSame('', $this->command->normalizeDomain('   '));
    }

    public function test_normalize_domains_maps_and_drops_empty_results(): void
    {
        $this->assertSame(
            ['a.com', 'b.com'],
            $this->command->normalizeDomains(['A.com', '   ', 'b.com.'])
        );
    }
}
