<!-- hy-mt2-i18n:start -->
[English](./README.md) | [中文](./README_zh-CN.md) | **日本語** | [Español](./README_es.md)
<!-- hy-mt2-i18n:end -->

## 使い捨てメールドメイン

使い捨てメールサービスで使用される使い捨てメールドメインのリストで、15分ごとにTXTおよびJSON形式で生成されます。

ブロックされたドメインと許可されたドメインを取得するためにこのプロジェクトで使用されているリストは、[こちら](https://github.com/amieiro/disposable-email-domains/blob/master/creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php#L16)で確認できます。また、このプロジェクトでは[セキュアなドメイン](https://github.com/amieiro/disposable-email-domains/blob/master/secureDomains.txt)のリストも管理しています。

## 要件

このプロジェクトには以下が必要です：
- **PHP 8.4または8.5**
- **Composer 2.x**
- **Laravel 13.x**

## お問い合わせ

もしブロックリストに含まれるべきでないドメインがあると思われる場合、別のリストを追加したい場合、または何らかの変更や改善を提案したい場合は、[プロジェクトのイシュー](https://github.com/amieiro/disposable-email-domains/issues)経由、または私の[個人ブログ](https://www.jesusamieiro.com/contactaconmigo/)でご連絡ください。

## ファイル

- **denyDomains**：廃棄用メールサービスで使用され、ブロックすべき既知のメールドメインのリストです。[txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.txt)および[json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json)形式で提供されています。
- **allowDomains**：廃棄用ではなく、許可すべき著名なメールドメインのリストです。[txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.txt)および[json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json)形式で提供されています。
- **secureDomains**：安全であると判明している既知のメールドメインの内部リストです。denyDomainsファイルを生成するために使用されます。
- **meta**：生成されたリストの機械読み取り可能な統計情報で、[json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/meta.json)形式です。生成日時は該当ファイルの最終コミット日付となります。

## 使用方法

これらのファイルをプロジェクトで利用して、使い捨てメールドメインをブロックできます。  
- まず、allowDomainsファイルを使って、そのドメインが許可リストに含まれているか確認してください。  
- もし含まれていなければ、denyDomainsファイルを使って、そのドメインが拒否リストに含まれているか確認してください。

例えば、PHPでは次のようになります：

```php
$emailDomain = 'gmail.com';
$allowDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json');
$allowDomains = json_decode($allowDomains, true);
if (in_array($emailDomain, $allowDomains)) {
    echo 'このドメインは許可されています。';
}

$emailDomain = 'temp-mail.org';
$denyDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json');
$denyDomains = json_decode($denyDomains, true);
if (in_array($emailDomain, $denyDomains)) {
    echo 'このドメインは使い捨てメールです。';
}
```

## 利用規約およびプライバシーポリシー

このプロジェクトは外部ソースで公開されているリストを集約したもので、secureDomains.txtファイルのみを手動で管理しているため、これらのリストは何ら保証や責任の負担なしにそのまま提供されます。詳細については、【利用規約】(TERMS_OF_SERVICE.md)および【プライバシーポリシー】(PRIVACY_POLICY.md)をご覧ください。

## ライセンス

このプロジェクトおよび関連ファイルは、[MITライセンス](https://opensource.org/licenses/MIT)の下で提供されているオープンソースソフトウェアです。
