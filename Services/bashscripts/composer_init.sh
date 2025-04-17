#!/bin/sh
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php -r "unlink('composer.lock');"
rm composer.lock
rm package-lock.json
#php -d memory_limit=-1 composer.phar require -W kriswallsmith/assetic
#php -d memory_limit=-1 composer.phar require -W leafo/lessphp
#php -d memory_limit=-1 composer.phar require -W leafo/scssphp
#php -d memory_limit=-1 composer.phar require -W lmammino/jsmin4assetic
#php -d memory_limit=-1 composer.phar require -W maatwebsite/excel
#php -d memory_limit=-1 composer.phar require -W mrclay/jsmin-php
#php -d memory_limit=-1 composer.phar require -W mrclay/minify
#php -d memory_limit=-1 composer.phar require -W natxet/cssmin
#php -d memory_limit=-1 composer.phar require -W patchwork/jsqueeze
php -d memory_limit=-1 composer.phar require -W phpoffice/phpspreadsheet
php -d memory_limit=-1 composer.phar require -W phpoffice/phpword
#php -d memory_limit=-1 composer.phar require -W ptachoire/cssembed
#php -d memory_limit=-1 composer.phar require -W scssphp/scssphp
php -d memory_limit=-1 composer.phar require -W spipu/html2pdf
#php -d memory_limit=-1 composer.phar require -W symfony/dom-crawler
#php -d memory_limit=-1 composer.phar require -W twig/twig
#php -d memory_limit=-1 composer.phar require -W wrklst/docxmustache

php -d memory_limit=-1 composer.phar require -W psr/simple-cache
php -d memory_limit=-1 composer.phar require -W kriswallsmith/assetic
php -d memory_limit=-1 composer.phar require -W leafo/lessphp
php -d memory_limit=-1 composer.phar require -W leafo/scssphp
php -d memory_limit=-1 composer.phar require -W lmammino/jsmin4assetic
php -d memory_limit=-1 composer.phar require -W maatwebsite/excel
php -d memory_limit=-1 composer.phar require -W mrclay/jsmin-php
php -d memory_limit=-1 composer.phar require -W mrclay/minify
php -d memory_limit=-1 composer.phar require -W natxet/cssmin
php -d memory_limit=-1 composer.phar require -W patchwork/jsqueeze
php -d memory_limit=-1 composer.phar require -W phpoffice/phpspreadsheet
php -d memory_limit=-1 composer.phar require -W phpoffice/phpword
php -d memory_limit=-1 composer.phar require -W ptachoire/cssembed
php -d memory_limit=-1 composer.phar require -W scssphp/scssphp
php -d memory_limit=-1 composer.phar require -W spipu/html2pdf
php -d memory_limit=-1 composer.phar require -W symfony/dom-crawler
php -d memory_limit=-1 composer.phar require -W twig/twig
php -d memory_limit=-1 composer.phar require -W wrklst/docxmustache
