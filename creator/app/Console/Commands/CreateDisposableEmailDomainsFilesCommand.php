<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class CreateDisposableEmailDomainsFilesCommand extends Command
{
    /**
     * The text files with the deny domains
     *
     * @var array
     */
    protected $textDenyFiles = [
        'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/internalLists/temp-mail.lol.txt',
        'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/internalLists/tmail-mmomekong-com.txt',
        'https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains.txt',
        'https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains_mx.txt',
        'https://raw.githubusercontent.com/auth0-signals/disposable-email-domains/master/dea.txt',
        'https://raw.githubusercontent.com/castle/disposable-email-domains/master/disposable-email-domains.txt',
        'https://raw.githubusercontent.com/di/disposable-email-domains/master/source_data/disposable_email_blocklist.conf',
        'https://raw.githubusercontent.com/disposable/disposable-email-domains/master/domains.txt',
        'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/disposable_email_blocklist.conf',
        'https://raw.githubusercontent.com/FGRibreau/mailchecker/master/list.txt',
        'https://raw.githubusercontent.com/GeroldSetz/emailondeck.com-domains/master/emailondeck.com_domains_from_bdea.cc.txt',
        'https://raw.githubusercontent.com/iocium/download.throwaway.cloud/main/list.txt',
        'https://raw.githubusercontent.com/jespernissen/disposable-maildomain-list/master/disposable-maildomain-list.txt',
        'https://raw.githubusercontent.com/kslr/disposable-email-domains/master/list.txt',
        'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf',
        'https://raw.githubusercontent.com/MattKetmo/EmailChecker/master/res/throwaway_domains.txt',
        'https://raw.githubusercontent.com/micke/valid_email2/master/config/disposable_email_domains.txt',
        'https://raw.githubusercontent.com/smudge/freemail/master/data/disposable.txt',
        'https://raw.githubusercontent.com/sublime-security/static-files/master/disposable_email_providers.txt',
        'https://raw.githubusercontent.com/unkn0w/disposable-email-domain-list/main/domains.txt',
        'https://raw.githubusercontent.com/wesbos/burner-email-providers/master/emails.txt',
        'https://raw.githubusercontent.com/willwhite/freemail/master/data/disposable.txt',
        'https://gist.githubusercontent.com/adamloving/4401361/raw/e81212c3caecb54b87ced6392e0a0de2b6466287/temporary-email-address-domains',
        'https://gist.githubusercontent.com/codeAshu/ebade8f300809a4079220f771265b0c4/raw/a16e5dea96e0df3fc63165e258596682f4cbd4c1/fakemails.txt',
        'https://throwaway.cloud/list.txt',
    ];

    /**
     * The json files with the deny domains
     *
     * @var array
     */
    protected $jsonDenyFiles = [
        'https://raw.githubusercontent.com/Dahoom152/disposable-email/main/domains.json',
        'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/index.json',
        'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/wildcard.json',
        'https://raw.githubusercontent.com/Propaganistas/Laravel-Disposable-Email/master/domains.json',
    ];

    /**
     * The text files with the allow domains
     *
     * @var array
     */
    protected $textAllowFiles = [
        'https://raw.githubusercontent.com/andreis/disposable/master/whitelist.txt',
        'https://raw.githubusercontent.com/di/disposable-email-domains/master/source_data/allowlist.conf',
        'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/allowlist.conf',
        'https://raw.githubusercontent.com/kslr/disposable-email-domains/master/whitelist.txt',
        'https://raw.githubusercontent.com/maximeg/email_inquire/master/data/common_providers.txt',
        'https://raw.githubusercontent.com/sublime-security/static-files/master/high_trust_sender_root_domains.txt',
    ];

    /**
     * The json files with the allow domains
     *
     * @var array
     */
    protected $jsonAllowFiles = [];

    /**
     * The secure domains
     *
     * @var array
     */
    protected $secureDomainsArray;

    /* The files to save the domains */
    protected $textDenyFile = '../denyDomains.txt';

    protected $jsonDenyFile = '../denyDomains.json';

    protected $textAllowFile = '../allowDomains.txt';

    protected $jsonAllowFile = '../allowDomains.json';

    /* The internal file with the secure domains */
    protected $secureDomainsFile = '../secureDomains.txt';

    /* The file with machine-readable metadata about the generated lists */
    protected $metaFile = '../meta.json';

    /**
     * The minimum fraction of the previous deny list size that a new run must
     * reach before it is allowed to overwrite the files. Guards against an
     * upstream source going down and silently shrinking the published list.
     */
    protected float $shrinkThreshold = 0.9;

    /**
     * The HTTP client used to fetch the source lists. Lazily created so it can
     * be replaced with a mock in tests.
     */
    protected ?ClientInterface $client = null;

    /**
     * How many times a failed request is retried before it is given up on.
     */
    protected int $maxRetries = 2;

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
     */
    public function handle(): int
    {
        ini_set('memory_limit', '256M');
        $denyDomains = [];
        $allowDomains = [];

        try {
            $this->secureDomainsArray = file($this->secureDomainsFile, FILE_IGNORE_NEW_LINES);
            $denyDomains = $this->obtainAllDomains($this->textDenyFiles, $this->jsonDenyFiles);
            $allowDomains = $this->obtainAllDomains($this->textAllowFiles, $this->jsonAllowFiles);
            $denyDomains = $this->removeLinesWithoutDomain($denyDomains);
            $allowDomains = $this->removeLinesWithoutDomain($allowDomains);
            $denyDomains = $this->cleanDomains($denyDomains);
            $allowDomains = $this->cleanDomains($allowDomains);
            $denyDomains = $this->normalizeDomains($denyDomains);
            $allowDomains = $this->normalizeDomains($allowDomains);

            $denyDomains = $this->removeSecureDomains($denyDomains);
            $denyDomains = $this->removeDuplicates($denyDomains);
            $denyDomains = $this->removeAllowedDomains($denyDomains, $allowDomains);

            $previousDenyCount = $this->countExistingDomains($this->textDenyFile);
            if (! $this->isDenyListSizeAcceptable(count($denyDomains), $previousDenyCount)) {
                $message = sprintf(
                    'Aborting: the new deny list (%d domains) dropped below %d%% of the previous one (%d domains). '
                    .'Files were not overwritten, most likely an upstream source failed.',
                    count($denyDomains),
                    (int) round($this->shrinkThreshold * 100),
                    $previousDenyCount
                );
                Log::error($message);
                $this->error($message);

                return 1; // Failure, leave the existing files untouched
            }

            $this->saveToFiles($denyDomains, $this->textDenyFile, $this->jsonDenyFile);

            $allowDomains = $this->addSecureDomains($allowDomains);
            $allowDomains = $this->removeDuplicates($allowDomains);
            $this->saveToFiles($allowDomains, $this->textAllowFile, $this->jsonAllowFile);

            $this->writeMetadata($this->metaFile, count($denyDomains), count($allowDomains), count($this->secureDomains()));

            // $this->commitChanges();

            return 0; // Success
        } catch (\Exception $error) {
            Log::error('Error processing the domains. '.PHP_EOL.$error);

            return 1; // Failure
        }
    }

    /**
     * Obtain all the domains from the text and json files
     *
     * @param  array  $textFiles  The text files with the domains.
     * @param  array  $jsonFiles  The json files with the domains.
     */
    protected function obtainAllDomains(array $textFiles, array $jsonFiles): array
    {
        $domains = [];

        foreach ($this->fetchBodies($textFiles) as $body) {
            $domains = array_merge($domains, $this->linesFromText($body));
        }

        foreach ($this->fetchBodies($jsonFiles) as $body) {
            $domains = array_merge($domains, $this->decodeJsonDomains($body));
        }

        return $domains;
    }

    /**
     * Fetch a list of URLs concurrently and return the bodies of the ones that
     * answered with HTTP 200, keyed by URL. Anything else (a non-200 status or
     * a transport failure) is logged and skipped, so a single broken source
     * cannot inject an error page or abort the whole run.
     */
    protected function fetchBodies(array $urls): array
    {
        $client = $this->client();

        $promises = [];
        foreach ($urls as $url) {
            $promises[$url] = $client->getAsync($url);
        }

        $bodies = [];
        foreach (Utils::settle($promises)->wait() as $url => $result) {
            if ($result['state'] !== 'fulfilled') {
                Log::error('Error fetching '.$url.PHP_EOL.$result['reason']->getMessage());

                continue;
            }

            $response = $result['value'];
            if ($response->getStatusCode() !== 200) {
                Log::warning('Skipping '.$url.': HTTP '.$response->getStatusCode());

                continue;
            }

            $bodies[$url] = (string) $response->getBody();
        }

        return $bodies;
    }

    /**
     * Split a fetched text body into lines, dropping the trailing newline.
     * Mirrors file(..., FILE_IGNORE_NEW_LINES) but works on a string.
     */
    public function linesFromText(string $body): array
    {
        $body = rtrim($body, "\r\n");
        if ($body === '') {
            return [];
        }

        return preg_split('/\r\n|\r|\n/', $body);
    }

    /**
     * Decode a JSON body into a list of domains, returning an empty array when
     * the body is not valid JSON or is not an array.
     */
    public function decodeJsonDomains(string $body): array
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Decide whether a failed request should be retried: on a connection error
     * or a 5xx response, up to $maxRetries times.
     */
    public function shouldRetry(int $retries, ?ResponseInterface $response = null, ?Throwable $exception = null): bool
    {
        if ($retries >= $this->maxRetries) {
            return false;
        }

        if ($exception instanceof ConnectException) {
            return true;
        }

        return $response !== null && $response->getStatusCode() >= 500;
    }

    /**
     * Replace the HTTP client, used to inject a mock client in tests.
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Build (once) the HTTP client used to fetch the sources, with a timeout,
     * an identifying User-Agent, and a retry on transient failures.
     */
    protected function client(): ClientInterface
    {
        if ($this->client === null) {
            $stack = HandlerStack::create();
            $stack->push(Middleware::retry(
                fn (int $retries, $request, ?ResponseInterface $response = null, ?Throwable $exception = null): bool => $this->shouldRetry($retries, $response, $exception),
                fn (int $retries): int => $retries * 1000
            ));

            $this->client = new Client([
                'handler' => $stack,
                'timeout' => 30,
                'connect_timeout' => 10,
                'http_errors' => false,
                'headers' => [
                    'User-Agent' => 'amieiro/disposable-email-domains generator (+https://github.com/amieiro/disposable-email-domains)',
                ],
            ]);
        }

        return $this->client;
    }

    /**
     * Add the secure domains (internal list) to the allowed domains.
     */
    protected function addSecureDomains(array $domains): array
    {
        return array_merge($domains, $this->secureDomains());
    }

    /**
     * The secure domains (internal list), without comments or blank lines.
     */
    protected function secureDomains(): array
    {
        return $this->normalizeDomains($this->removeLinesWithoutDomain($this->secureDomainsArray ?? []));
    }

    /**
     * Remove lines without a domain
     */
    protected function removeLinesWithoutDomain(array $domains): array
    {
        return array_filter($domains, function ($domain) {
            return $domain !== '' && ! str_starts_with($domain, '#');
        });
    }

    /**
     * Clean the domains
     *
     * Remove the "*" and "." from the start of the domain.
     */
    public function cleanDomains(array $domains): array
    {
        return array_map(function ($domain) {
            if (str_starts_with($domain, '*.')) {
                return substr($domain, 2);
            }
            if (str_starts_with($domain, '.')) {
                return substr($domain, 1);
            }

            return $domain;
        }, $domains);
    }

    /**
     * Normalize a list of domains, dropping any that normalize to an empty string.
     */
    public function normalizeDomains(array $domains): array
    {
        $normalized = [];
        foreach ($domains as $domain) {
            $domain = $this->normalizeDomain($domain);
            if ($domain !== '') {
                $normalized[] = $domain;
            }
        }

        return $normalized;
    }

    /**
     * Normalize a single domain so the lists store one canonical form: trimmed,
     * without a trailing dot, and lowercased.
     *
     * Domains are not converted to punycode here. idn_to_ascii can crash the
     * intl extension on malformed input, and this command processes hundreds of
     * thousands of unvetted domains from external sources, so the risk is not
     * worth it for the rare non-ASCII entry.
     */
    public function normalizeDomain(string $domain): string
    {
        $domain = rtrim(trim($domain), '.');
        if ($domain === '') {
            return '';
        }

        return mb_strtolower($domain);
    }

    /**
     * Remove the secure domains, and any of their subdomains, from the deny domains.
     */
    protected function removeSecureDomains(array $domains): array
    {
        $secureLookup = $this->buildSecureLookup($this->secureDomainsArray ?? []);

        return array_filter($domains, function ($domain) use ($secureLookup) {
            return ! $this->isSecureDomain($domain, $secureLookup);
        });
    }

    /**
     * Build a fast lookup set (domain => true) from the raw secure domains list,
     * skipping blank lines and comments.
     */
    protected function buildSecureLookup(array $secureDomains): array
    {
        $lookup = [];
        foreach ($this->removeLinesWithoutDomain($secureDomains) as $secureDomain) {
            $secureDomain = $this->normalizeDomain($secureDomain);
            if ($secureDomain !== '') {
                $lookup[$secureDomain] = true;
            }
        }

        return $lookup;
    }

    /**
     * Decide whether a domain is secured: it either matches a secure domain
     * exactly or is a subdomain of one. Matching happens on label boundaries,
     * so "myexample.com" is not treated as a subdomain of "example.com", and
     * "evil.co.uk" is not removed because of a secure "good.co.uk".
     */
    public function isSecureDomain(string $domain, array $secureLookup): bool
    {
        if ($domain === '') {
            return false;
        }

        if (isset($secureLookup[$domain])) {
            return true;
        }

        $labels = explode('.', $domain);
        // The smallest parent worth checking has two labels (a registrable domain).
        $lastParentIndex = count($labels) - 2;
        for ($i = 1; $i <= $lastParentIndex; $i++) {
            $parent = implode('.', array_slice($labels, $i));
            if (isset($secureLookup[$parent])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove the duplicates from a domain list.
     */
    protected function removeDuplicates(array $domains): array
    {
        sort($domains, SORT_STRING);

        return array_unique($domains, SORT_STRING);
    }

    /**
     * Remove the allowed domains from the denied domains.
     */
    protected function removeAllowedDomains(array $denyDomains, array $allowDomains): array
    {
        return array_diff($denyDomains, $allowDomains);
    }

    /**
     * Decide whether a newly built deny list is large enough to publish.
     *
     * A run is acceptable when there is no previous baseline (first run) or
     * when the new size is at least $shrinkThreshold of the previous size.
     */
    public function isDenyListSizeAcceptable(int $newCount, int $previousCount): bool
    {
        if ($previousCount <= 0) {
            return true;
        }

        return $newCount >= (int) floor($previousCount * $this->shrinkThreshold);
    }

    /**
     * Count the domains stored in an existing generated file.
     */
    protected function countExistingDomains(string $file): int
    {
        if (! is_file($file)) {
            return 0;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return $lines === false ? 0 : count($lines);
    }

    /**
     * Save the domains to the text and json files.
     */
    protected function saveToFiles(array $domains, string $textFile, string $jsonFile): void
    {
        file_put_contents($textFile, implode(PHP_EOL, array_values($domains)));
        file_put_contents($jsonFile, json_encode(array_values($domains), JSON_PRETTY_PRINT));
    }

    /**
     * Build the machine-readable metadata for the generated lists.
     *
     * Only counts are recorded, not a timestamp: a timestamp would change on
     * every run and defeat the "commit only when the lists changed" check,
     * producing a commit every fifteen minutes. The generation time is already
     * available from the commit date.
     */
    public function buildMetadata(int $denyCount, int $allowCount, int $secureCount): array
    {
        return [
            'denyDomains' => $denyCount,
            'allowDomains' => $allowCount,
            'secureDomains' => $secureCount,
        ];
    }

    /**
     * Write the metadata file as JSON.
     */
    public function writeMetadata(string $file, int $denyCount, int $allowCount, int $secureCount): void
    {
        file_put_contents($file, json_encode($this->buildMetadata($denyCount, $allowCount, $secureCount), JSON_PRETTY_PRINT));
    }

    /**
     * Commit the changes to the repository
     */
    protected function commitChanges(): void
    {
        exec('git -C .. add . && git -C .. commit -m '.'"Updated automatically generated files. '.Carbon::now()->utc().' UTC"');
        exec('ssh-agent $(ssh-add '.getenv('SSH_RSA_KEY_PATH').' -p '.getenv('SSH_RSA_KEY_PASS').'; git -C .. push)');
    }
}
