# Terms of service

Last updated: June 11, 2026

These terms apply to the disposable-email-domains project and to all the files it publishes, including `denyDomains.txt`, `denyDomains.json`, `allowDomains.txt`, `allowDomains.json`, `secureDomains.txt` and `meta.json`. By downloading or using any of these files you accept these terms.

## What this project is

This project aggregates lists of disposable email domains published by third parties. A script downloads those external lists on a schedule, merges them, removes duplicates and publishes the result. You can see the exact sources in the [generator command](creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php).

The only data maintained by hand in this project is the [secureDomains.txt](secureDomains.txt) file, a short list of domains that are known to be legitimate and are always excluded from the deny list.

## No warranty

The lists are provided "as is" and "as available", without warranty of any kind, express or implied. This includes, but is not limited to, warranties of accuracy, completeness, merchantability, fitness for a particular purpose and non-infringement.

Because almost all the data comes from external sources that this project does not control, there is no way to guarantee that:

- every domain in the deny list is actually a disposable email domain (false positives exist);
- every disposable email domain is in the deny list (false negatives exist);
- the lists are available, complete or up to date at any given moment;
- the format, the URLs or the publication schedule will not change without notice.

The presence or absence of a domain in any list is not a statement about the reputation, legitimacy or trustworthiness of that domain or its owner. It only means that the domain did or did not appear in the aggregated sources at generation time.

## Limitation of liability

To the maximum extent permitted by applicable law, the maintainer of this project shall not be liable for any direct, indirect, incidental, special, consequential or exemplary damages arising from the use of, or the inability to use, these lists. This includes, among others, rejected sign-ups, undelivered email, loss of business, loss of data or loss of profits, even if advised of the possibility of such damages.

You use these lists at your own risk and under your own responsibility.

## Your responsibilities

If you use these lists to accept or reject email addresses in your own service, you are the one making that decision, not this project. In particular, you should:

- test the lists against your own user base before relying on them in production;
- give your users a way to appeal or to contact you when a legitimate domain is blocked;
- keep your local copy of the lists reasonably up to date, since wrongly listed domains are removed over time.

## Removal requests

If your domain appears in the deny list and it is not a disposable email service, open an issue in the [issue tracker](https://github.com/amieiro/disposable-email-domains/issues). Requests are reviewed manually and handled on a best-effort basis, with no guaranteed response time. A verified legitimate domain is added to `secureDomains.txt`, which permanently excludes it from the deny list published by this project. This project cannot remove a domain from the upstream sources it aggregates; you would need to contact those projects separately.

## License

The code and the published files are licensed under the [MIT license](https://opensource.org/licenses/MIT). The MIT license also disclaims warranties and liability; in case of conflict between that license and these terms, the license prevails for the code, and these terms apply to the use of the published lists.

## Changes to these terms

These terms may change at any time. The current version is always the one published in this repository, and the "Last updated" date at the top reflects the latest revision.

## Contact

Use the [project issues](https://github.com/amieiro/disposable-email-domains/issues) or the maintainer's [contact page](https://www.jesusamieiro.com/contactaconmigo/).
