<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateDisposableEmailDomainsFilesCommand extends Command
{
    protected $textDenyFiles = [
        'https://raw.githubusercontent.com/andreis/disposable/master/blacklist.txt',
        'https://raw.githubusercontent.com/FGRibreau/mailchecker/master/list.txt',
        'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf',
        'https://raw.githubusercontent.com/MattKetmo/EmailChecker/master/res/throwaway_domains.txt',
        'https://raw.githubusercontent.com/micke/valid_email2/master/config/disposable_email_domains.txt',
        'https://raw.githubusercontent.com/wesbos/burner-email-providers/master/emails.txt',
        'https://gist.githubusercontent.com/adamloving/4401361/raw/e81212c3caecb54b87ced6392e0a0de2b6466287/temporary-email-address-domains',
        'https://gist.githubusercontent.com/codeAshu/ebade8f300809a4079220f771265b0c4/raw/a16e5dea96e0df3fc63165e258596682f4cbd4c1/fakemails.txt',
        'https://gist.githubusercontent.com/michenriksen/8710649/raw/e09ee253960ec1ff0add4f92b62616ebbe24ab87/disposable-email-provider-domains',
    ];

    protected $jsonDenyFiles = [
        'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/wildcard.json',
    ];

    protected $textAllowFiles = [
        'https://raw.githubusercontent.com/andreis/disposable/master/whitelist.txt',
        'https://raw.githubusercontent.com/maximeg/email_inquire/master/data/common_providers.txt',
    ];

    protected $textDenyFile = '../denyDomains.txt';
    protected $jsonDenyFile = '../denyDomains.json';
    protected $textAllowFile = '../allowDomains.txt';
    protected $jsonAllowFile = '../allowDomains.json';

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
        foreach ($this->textDenyFiles as $textDenyFile) {
            try {
                $denyDomains = array_merge($denyDomains, file($textDenyFile, FILE_IGNORE_NEW_LINES));
            } catch (\Exception $error) {
                Log::error('Error reading ' . $textDenyFile . PHP_EOL . $error);
            }
        }
        foreach ($this->jsonDenyFiles as $jsonDenyFile) {
            try {
                $denyDomains = array_merge($denyDomains, json_decode(file_get_contents($jsonDenyFile)));
            } catch (\Exception $error) {
                Log::error('Error reading ' . $jsonDenyFile . PHP_EOL . $error);
            }
        }
        foreach ($this->textAllowFiles as $textAllowFile) {
            try {
                $allowDomains = array_merge($allowDomains, file($textAllowFile, FILE_IGNORE_NEW_LINES));
            } catch (\Exception $error) {
                Log::error('Error reading ' . $textAllowFile . PHP_EOL . $error);
            }
        }
        try {
            sort($denyDomains, SORT_STRING);
            $denyDomains = array_unique($denyDomains, SORT_STRING);
            file_put_contents($this->textDenyFile, implode(PHP_EOL, array_values($denyDomains)));
            file_put_contents($this->jsonDenyFile, json_encode(array_values($denyDomains), JSON_PRETTY_PRINT));

            sort($allowDomains, SORT_STRING);
            $allowDomains = array_unique($allowDomains, SORT_STRING);
            file_put_contents($this->textAllowFile, implode(PHP_EOL, array_values($allowDomains)));
            file_put_contents($this->jsonAllowFile, json_encode(array_values($allowDomains), JSON_PRETTY_PRINT));
            exec('git -C .. add . && git -C .. commit -m ' . '"Updated automatically generated files. ' . Carbon::now()->utc() . ' UTC"');
            exec('ssh-agent $(ssh-add ' . getenv('SSH_RSA_KEY_PATH') . '; git -C .. push)');
        } catch (\Exception $error) {
            Log::error('Error processing the domains. ' . PHP_EOL . $error);
        }
    }
}
