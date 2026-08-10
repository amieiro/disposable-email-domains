<!-- hy-mt2-i18n:start -->
[English](./README.md) | [中文](./README_zh-CN.md) | [日本語](./README_ja.md) | **Español**
<!-- hy-mt2-i18n:end -->

## Dominios de correo electrónico desechable

Listas de dominios de correo electrónico desechable, utilizadas en los servicios de correo temporal, que se generan cada cuarto de hora en formato TXT y JSON.

Puede encontrar [aquí](https://github.com/amieiro/disposable-email-domains/blob/master/creator/app/Console/Commands/CreateDisposableEmailDomainsFilesCommand.php#L16) las listas que utiliza este proyecto para determinar los dominios bloqueados y permitidos. Este proyecto mantiene esta lista de [dominios seguros](https://github.com/amieiro/disposable-email-domains/blob/master/secureDomains.txt).

## Requisitos

Requisitos del proyecto:  
- **PHP 8.4 o 8.5**  
- **Composer 2.x**  
- **Laravel 13.x**

## Contacto

Si observa que hay dominios que no deberían formar parte de la lista de denegación, si desea agregar alguna otra lista o proponer cambios, mejoras, etc., puede ponerse en contacto conmigo a través de los [problemas del proyecto](https://github.com/amieiro/disposable-email-domains/issues) o en mi [blog personal](https://www.jesusamieiro.com/contactaconmigo/).

## Archivos

- **denyDomains**: Lista de dominios de correo electrónico conocidos que se utilizan en servicios de correo temporal y deben bloquearse. Está disponible en formato [txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.txt) y [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json).  
- **allowDomains**: Lista de dominios de correo electrónico reconocidos que no son temporales y deben permitirse. Está disponible en formato [txt](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.txt) y [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json).  
- **secureDomains**: Lista interna de dominios de correo electrónico conocidos que son seguros. Se utiliza para generar los archivos denyDomains.  
- **meta**: Cifras legibles por máquinas de las listas generadas, en formato [json](https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/meta.json). La fecha de generación corresponde a la última fecha de commit del archivo.

## Uso

Puede utilizar estos archivos en sus proyectos para bloquear dominios de correo electrónico desechables.  
- Primero, debe comprobar si el dominio está en la lista de permisos, utilizando el archivo allowDomains.  
- Si no está allí, debe verificar si el dominio se encuentra en la lista de denegaciones, usando el archivo denyDomains.

Por ejemplo, en PHP:

```php
$emailDomain = 'gmail.com';
$allowDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json');
$allowDomains = json_decode($allowDomains, true);
if (in_array($emailDomain, $allowDomains)) {
    echo 'Este dominio está permitido.';
}

$emailDomain = 'temp-mail.org';
$denyDomains = file_get_contents('https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json');
$denyDomains = json_decode($denyDomains, true);
if (in_array($emailDomain, $denyDomains)) {
    echo 'Este dominio es de correo desechable.';
}
```

## Términos de servicio y política de privacidad

Este proyecto recopila listas publicadas por fuentes externas y solo mantiene manualmente el archivo secureDomains.txt; por lo tanto, las listas se proporcionan tal como están, sin ninguna garantía ni aceptación de responsabilidad. Consulte los [términos de servicio](TERMS_OF_SERVICE.md) y la [política de privacidad](PRIVACY_POLICY.md) para obtener más detalles.

## Licencia

Este proyecto y los archivos correspondientes son software de código abierto licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).
