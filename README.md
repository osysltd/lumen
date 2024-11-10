# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/lumen)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

> **Note:** In the years since releasing Lumen, PHP has made a variety of wonderful performance improvements. For this reason, along with the availability of [Laravel Octane](https://laravel.com/docs/octane), we no longer recommend that you begin new projects with Lumen. Instead, we recommend always beginning new projects with [Laravel](https://laravel.com).

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## Software development under Windows
* [PHP](https://windows.php.net/download/) 7.4 VC15 x64 Non Thread Safe. Extract zip and add your PHP runtime directory to your Windows PATH environment variable by using  `sysdm.cpl` or `setx /M path "%path%;C:\php\"`
* [GIT SCM](https://git-scm.com/download/win)
* [TortoiseGit](https://tortoisegit.org/download/) Installer for Windows PC 
* [Composer](https://getcomposer.org/download/) Latest Windows Installer
* [Visual Studio Code](https://code.visualstudio.com/download) for Windows System Installer 
* [VSCode PHP Tools](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode) for VS Code extension
* [VSCode Blade Formatter](https://marketplace.visualstudio.com/items?itemName=amirmarmul.laravel-blade-vscode)
* [VSCode Bootstrap 5 Snippets](https://marketplace.visualstudio.com/items?itemName=anbuselvanrocky.bootstrap5-vscode)
* [VSCode PHP Server](https://marketplace.visualstudio.com/items?itemName=brapifra.phpserver)
* [HeidiSQL](https://www.heidisql.com/download.php)

## Deployment

```sh
git config credential.helper store
cp .env.example .env
composer install
composer dump-autoload --optimize
php artisan key:generate
php artisan cache:clear
```

In case of `PHP Fatal error:  Allowed memory size of bytes exhausted (tried to allocate bytes)`
```sh
php -d memory_limit=-1 /usr/local/bin/composer install
```

## Snippets
```sh
php82-cli artisan cache:clear
```

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
