## Disposable Email Domains

Disposable email domain lists, used in disposable email services, generated every quarter of an hour, in txt and JSON format.

You can find [here](https://github.com/amieiro/disposable-email-domains/blob/master/creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php#L16) the lists used by this project to obtain the blocked and allowed domains. This project maintains this list of [secure domains](https://github.com/amieiro/disposable-email-domains/blob/master/secureDomains.txt).

## Requirements

The project requires:
- **PHP 8.3 or 8.4**
- **Composer 2.x**
- **Laravel 12.x**

## Contact

If you see that some domains should not be in the deny list, if you want to add some other list or if you want 
to suggest some change, improvement, ... you can contact me through the 
[project issues](https://github.com/amieiro/disposable-email-domains/issues) or in my 
[personal blog](https://www.jesusamieiro.com/contactaconmigo/).

## Files

- **denyDomains**. List of known e-mail domains used disposable email services and should be blocked. Available in [txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.txt) and [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json) format.
- **allowDomains**. List of well-known email domains that are not disposable and should be allowed. Available in [txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.txt) and [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json) format.
- **secureDomains**. Internal list of known e-mail domains that are secure. Used to generate the denyDomains files.

## Usage

You can use these files in your projects to block disposable email domains. 
- First, you should check if the domain is in the allow list, using the allowDomains file. 
- If it is not, you should check if the domain is in the deny list, using the denyDomains file.

For example, in PHP:

```php
$emailDomain = 'gmail.com';
$allowDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json');
$allowDomains = json_decode($allowDomains, true);
if (in_array($emailDomain, $allowDomains)) {
    echo 'This domain is allowed.';
}

$emailDomain = 'temp-mail.org';
$denyDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json');
$denyDomains = json_decode($denyDomains, true);
if (in_array($emailDomain, $denyDomains)) {
    echo 'This domain is disposable.';
}
```

## License

This project and the files are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
