## Disposable Email Domains

Disposable email domain lists, used in disposable email services, generated every quarter of an hour, in txt and JSON format.

You can find [here](https://github.com/amieiro/disposable-email-domains/blob/master/creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php#L11) the lists used by this project to obtain the blocked and allowed domains. This project maintains this list of [secure domains](https://github.com/amieiro/disposable-email-domains/blob/master/secureDomains.txt).

## Contact

If you see that some domains should not be in the deny list, if you want to add some other list or if you want 
to suggest some change, improvement, ... you can contact me through the 
[project issues](https://github.com/amieiro/disposable-email-domains/issues) or in my 
[personal blog](https://www.jesusamieiro.com/contactaconmigo/).

## Files

- **denyDomains**. List of known e-mail domains used disposable email services and should be blocked. Available in [txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.txt) and [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json) format.
- **allowDomains**. List of well-known email domains that are not disposable and should be allowed. Available in [txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.txt) and [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json) format.
- **secureDomains**. Internal list of known e-mail domains that are secure. Used to generate the denyDomains files.

## License

This project and the files are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
