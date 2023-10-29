<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateDisposableEmailDomainsFilesCommand extends Command
{
    protected $textDenyFiles = [
        'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/internalLists/tmail-mmomekong-com.txt',
        'https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains.txt',
        'https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains_mx.txt',
        'https://raw.githubusercontent.com/auth0-signals/disposable-email-domains/master/dea.txt',
        'https://raw.githubusercontent.com/di/disposable-email-domains/master/source_data/disposable_email_blocklist.conf',
        'https://raw.githubusercontent.com/FGRibreau/mailchecker/master/list.txt',
        'https://raw.githubusercontent.com/GeroldSetz/emailondeck.com-domains/master/emailondeck.com_domains_from_bdea.cc.txt',
        'https://raw.githubusercontent.com/jespernissen/disposable-maildomain-list/master/disposable-maildomain-list.txt',
        'https://raw.githubusercontent.com/kslr/disposable-email-domains/master/list.txt',
        'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf',
        'https://raw.githubusercontent.com/MattKetmo/EmailChecker/master/res/throwaway_domains.txt',
        'https://raw.githubusercontent.com/micke/valid_email2/master/config/disposable_email_domains.txt',
        'https://raw.githubusercontent.com/smudge/freemail/master/data/disposable.txt',
        'https://raw.githubusercontent.com/wesbos/burner-email-providers/master/emails.txt',
        'https://raw.githubusercontent.com/willwhite/freemail/master/data/disposable.txt',
        'https://gist.githubusercontent.com/adamloving/4401361/raw/e81212c3caecb54b87ced6392e0a0de2b6466287/temporary-email-address-domains',
        'https://gist.githubusercontent.com/codeAshu/ebade8f300809a4079220f771265b0c4/raw/a16e5dea96e0df3fc63165e258596682f4cbd4c1/fakemails.txt',
        'https://throwaway.cloud/list.txt',
    ];

    protected $jsonDenyFiles = [
        'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/index.json',
        'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/wildcard.json',
        'https://raw.githubusercontent.com/Propaganistas/Laravel-Disposable-Email/master/domains.json',
    ];

    protected $textAllowFiles = [
        'https://raw.githubusercontent.com/andreis/disposable/master/whitelist.txt',
        'https://raw.githubusercontent.com/di/disposable-email-domains/master/source_data/allowlist.conf',
        'https://raw.githubusercontent.com/kslr/disposable-email-domains/master/whitelist.txt',
        'https://raw.githubusercontent.com/maximeg/email_inquire/master/data/common_providers.txt',
    ];

    protected $jsonAllowFiles = [];

    protected $secureDomainsArray;

    protected $textDenyFile = '../denyDomains.txt';
    protected $jsonDenyFile = '../denyDomains.json';
    protected $textAllowFile = '../allowDomains.txt';
    protected $jsonAllowFile = '../allowDomains.json';
    protected $secureDomainsFile = '../secureDomains.txt';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ded:create-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the allow and the deny files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param $file
     * @return void
     */
    public function handle()
    {
        $denyDomains = [];
        $allowDomains = [];

        try {
            $this->secureDomainsArray = file($this->secureDomainsFile, FILE_IGNORE_NEW_LINES);
            $denyDomains = $this->obtainAllDomains($this->textDenyFiles, $this->jsonDenyFiles);
            $allowDomains = $this->obtainAllDomains($this->textAllowFiles, $this->jsonAllowFiles);

            $denyDomains = $this->removeSecureDomains($denyDomains);
            $denyDomains = $this->removeDuplicates($denyDomains);
            $denyDomains = $this->removeAllowedDomains($denyDomains, $allowDomains);
            $this->saveToFiles($denyDomains, $this->textDenyFile, $this->jsonDenyFile);

            $allowDomains = $this->addSecureDomains($allowDomains);
            $allowDomains = $this->removeDuplicates($allowDomains);
            $this->saveToFiles($allowDomains, $this->textAllowFile, $this->jsonAllowFile);

            $this->commitChanges();
        } catch (\Exception $error) {
            Log::error('Error processing the domains. ' . PHP_EOL . $error);
        }
    }

    protected function obtainAllDomains(array $textFiles, array $jsonFiles): array
    {
        $domains = [];

        foreach ($textFiles as $textFile) {
            try {
                $domains = array_merge($domains, file($textFile, FILE_IGNORE_NEW_LINES));
            } catch (\Exception $error) {
                Log::error('Error reading ' . $textFile . PHP_EOL . $error);
            }
        }

        foreach ($jsonFiles as $jsonFile) {
            try {
                $domains = array_merge($domains, json_decode(file_get_contents($jsonFile), true));
            } catch (\Exception $error) {
                Log::error('Error reading ' . $jsonFile . PHP_EOL . $error);
            }
        }

        return $domains;
    }

    protected function addSecureDomains(array $domains): array
    {
        return array_merge($domains, $this->secureDomainsArray);
    }

    protected function removeSecureDomains(array $domains): array
    {
        return array_udiff($domains, $this->secureDomainsArray, function ($domain, $secureDomain) {
            // Remove domain with subdomain if the main domain is in the secure domains array
            $matchesDomain = [];
            $matchesSecureDomain = [];
            preg_match('/(?<=\.)[^.]+\.[^.]+$/', $domain, $matchesDomain);
            if (isset($matchesDomain[0])) {
                $domain = $matchesDomain[0];
            }
            preg_match('/(?<=\.)[^.]+\.[^.]+$/', $secureDomain, $matchesSecureDomain);
            if (isset($matchesSecureDomain[0])) {
                $secureDomain = $matchesSecureDomain[0];
            }
            return strcmp($domain, $secureDomain);
        });
    }

    protected function removeDuplicates(array $domains): array
    {
        sort($domains, SORT_STRING);
        return array_unique($domains, SORT_STRING);
    }

    protected function removeAllowedDomains(array $denyDomains, array $allowDomains): array
    {
        return array_diff($denyDomains, $allowDomains);
    }

    protected function saveToFiles(array $domains, string $textFile, string $jsonFile): void
    {
        file_put_contents($textFile, implode(PHP_EOL, array_values($domains)));
        file_put_contents($jsonFile, json_encode(array_values($domains), JSON_PRETTY_PRINT));
    }

    protected function commitChanges()
    {
        exec('git -C .. add . && git -C .. commit -m ' . '"Updated automatically generated files. ' . Carbon::now()->utc() . ' UTC"');
        exec('ssh-agent $(ssh-add ' . getenv('SSH_RSA_KEY_PATH') . '; git -C .. push)');
    }
}
