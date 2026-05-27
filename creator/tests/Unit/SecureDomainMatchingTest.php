<?php

namespace Tests\Unit;

use App\Console\Commands\CreateDisposableEmailDomainsFilesCommand;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SecureDomainMatchingTest extends TestCase
{
    private CreateDisposableEmailDomainsFilesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CreateDisposableEmailDomainsFilesCommand;
    }

    public function test_exact_domain_match_is_secure(): void
    {
        $this->assertTrue($this->command->isSecureDomain('example.com', ['example.com' => true]));
    }

    public function test_subdomain_of_a_secure_domain_is_secure(): void
    {
        $this->assertTrue($this->command->isSecureDomain('mail.example.com', ['example.com' => true]));
    }

    public function test_deep_subdomain_of_a_secure_domain_is_secure(): void
    {
        $this->assertTrue($this->command->isSecureDomain('a.b.example.com', ['example.com' => true]));
    }

    public function test_unrelated_domain_is_not_secure(): void
    {
        $this->assertFalse($this->command->isSecureDomain('notexample.com', ['example.com' => true]));
    }

    public function test_domain_sharing_a_suffix_but_not_a_label_boundary_is_not_secure(): void
    {
        // "myexample.com" ends with the text "example.com" but is not a subdomain of it.
        $this->assertFalse($this->command->isSecureDomain('myexample.com', ['example.com' => true]));
    }

    public function test_different_domain_under_a_multi_part_tld_is_not_secure(): void
    {
        // The previous eTLD+1 logic reduced both to "co.uk" and wrongly treated them as equal.
        $this->assertFalse($this->command->isSecureDomain('evil.co.uk', ['good.co.uk' => true]));
    }

    public function test_subdomain_under_a_multi_part_tld_is_secure(): void
    {
        $this->assertTrue($this->command->isSecureDomain('mail.good.co.uk', ['good.co.uk' => true]));
    }

    public function test_remove_secure_domains_keeps_only_unrelated_domains(): void
    {
        $this->setSecureDomains(['example.com', 'good.co.uk', '# a comment', '']);

        $result = array_values($this->invokeRemoveSecureDomains([
            'example.com',      // exact match -> removed
            'mail.example.com', // subdomain -> removed
            'notexample.com',   // unrelated -> kept
            'evil.co.uk',       // different domain on the same multi-part TLD -> kept
            'mail.good.co.uk',  // subdomain on a multi-part TLD -> removed
        ]));

        $this->assertSame(['notexample.com', 'evil.co.uk'], $result);
    }

    private function setSecureDomains(array $domains): void
    {
        $property = (new ReflectionClass($this->command))->getProperty('secureDomainsArray');
        $property->setAccessible(true);
        $property->setValue($this->command, $domains);
    }

    private function invokeRemoveSecureDomains(array $domains): array
    {
        $method = (new ReflectionClass($this->command))->getMethod('removeSecureDomains');
        $method->setAccessible(true);

        return $method->invoke($this->command, $domains);
    }
}
