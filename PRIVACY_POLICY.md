# Privacy policy

Last updated: June 11, 2026

The short version: this project does not collect personal data. It is a set of static files in a public GitHub repository. There are no user accounts, no cookies, no analytics and no servers run by this project.

## What this project publishes

The published files contain domain names only (for example `temp-mail.org`), never email addresses, names or any other personal information. Almost all of those domain names come from public third-party lists; the rest come from the manually maintained [secureDomains.txt](secureDomains.txt) file.

A domain name normally identifies an organization or a service, not a person. If a domain that identifies you personally appears in a list and you want it reviewed, open an issue in the [issue tracker](https://github.com/amieiro/disposable-email-domains/issues).

## Downloading the files

The files are hosted and served by GitHub. When you download them, GitHub may log technical data such as your IP address. That processing is governed by the [GitHub Privacy Statement](https://docs.github.com/en/site-policy/privacy-policies/github-privacy-statement), not by this project. This project has no access to those logs.

## Issues, comments and contributions

If you open an issue, comment on one or contribute code, everything you write is public and is stored by GitHub together with your GitHub profile information. Keep in mind that removal requests are public too: if you state that a domain belongs to you or to your company, that statement stays visible in the issue history. Do not post information you do not want to be public.

## The generator

The script that builds the lists downloads public files from the upstream sources and identifies itself with a User-Agent header that points to this repository. It does not collect, store or transmit any data about the people who use the published lists.

## Third-party sources

The aggregated lists come from external projects with their own maintainers and policies. This project does not control what they publish or how they handle data. The full list of sources is in the [generator command](creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php).

## Changes to this policy

This policy may change at any time. The current version is always the one published in this repository, and the "Last updated" date at the top reflects the latest revision.

## Contact

Use the [project issues](https://github.com/amieiro/disposable-email-domains/issues) or the maintainer's [contact page](https://www.jesusamieiro.com/contactaconmigo/).
