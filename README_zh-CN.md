<!-- hy-mt2-i18n:start -->
[English](./README.md) | **中文** | [日本語](./README_ja.md) | [Español](./README_es.md)
<!-- hy-mt2-i18n:end -->

## 一次性邮箱域名列表

用于临时邮箱服务的临时邮箱域名列表，每十五分钟更新一次，以 TXT 和 JSON 格式提供。

您可以在[此处](https://github.com/amieiro/disposable-email-domains/blob/master/creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php#L16)查看该项目用于确定被封禁域名与允许域名的列表。该项目还维护着一份[安全域名](https://github.com/amieiro/disposable-email-domains/blob/master/secureDomains.txt)清单。

## 要求条件

该项目需要以下环境：
- **PHP 8.4 或 8.5**
- **Composer 2.x**
- **Laravel 13.x**

## 联系方式

如果您发现某些域名本不应出现在拒绝列表中，或者想要添加其他列表，又或是希望提出某些修改、改进建议等，您可以通过[项目问题反馈](https://github.com/amieiro/disposable-email-domains/issues)或我的[个人博客](https://www.jesusamieiro.com/contactaconmigo/)与我联系。

## 文件

- **denyDomains**：包含已知用于临时邮箱服务的、应当被屏蔽的电子邮件域名列表。提供[文本](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.txt)和[JSON](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json)格式。
- **allowDomains**：包含已知并非临时邮箱、应当被允许通过的知名电子邮件域名列表。提供[文本](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.txt)和[JSON](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json)格式。
- **secureDomains**：包含已知安全电子邮件域名的内部列表，用于生成denyDomains文件。
- **meta**：以[JSON](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/meta.json)格式存储的、可供机器读取的已生成列表数量信息。生成时间即为该文件的最后提交日期。

## 使用方法

您可以在自己的项目中使用这些文件来屏蔽一次性邮箱域名。
- 首先，应通过 `allowDomains` 文件检查该域名是否在允许列表中。
- 如果不在，则需通过 `denyDomains` 文件查看该域名是否在禁止列表中。

例如，在 PHP 中：

```php
$emailDomain = 'gmail.com';
$allowDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json');
$allowDomains = json_decode($allowDomains, true);
if (in_array($emailDomain, $allowDomains)) {
    echo '该域名是允许使用的。';
}

$emailDomain = 'temp-mail.org';
$denyDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json');
$denyDomains = json_decode($denyDomains, true);
if (in_array($emailDomain, $denyDomains)) {
    echo '该域名属于一次性邮箱。';
}
```

## 服务条款与隐私政策

本项目仅汇总了外部来源发布的列表，并手动维护 secureDomains.txt 文件，因此这些列表按原样提供，不附带任何担保，亦不承担任何责任。详情请参阅[服务条款](TERMS_OF_SERVICE.md)与[隐私政策](PRIVACY_POLICY.md)。

## 许可证

本项目及其相关文件均为开源软件，采用[MIT许可证](https://opensource.org/licenses/MIT)进行授权。
