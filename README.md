---
### THE PROJECT IS UNDER PROGRESS AND IS NOT READY YET
---

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/migration74)
[![Build Status](https://travis-ci.com/etraxis/etraxis.svg?branch=master)](https://travis-ci.com/etraxis/etraxis)

eTraxis is an issue tracking system with ability to set up an unlimited number of customizable workflows.
It can be used to track almost anything, though the most popular cases are a *bug tracker* and a *help desk system*.

### Features

* Custom workflows
* Fine-tuned permissions
* History of events and changes
* Filters and views
* Attachments
* Project metrics
* Authentication through Azure, GitHub, Google
* Authentication through Active Directory (LDAP)
* MySQL and PostgreSQL support
* Localization and multilingual support
* Mobile-friendly web interface
* and more...

### Prerequisites

* [PHP](https://php.net/)
* [Composer](https://getcomposer.org/)
* [Symfony](https://symfony.com/download)

### Install

```bash
composer install
symfony serve
```

### Upgrade

```bash
composer update "symfony/*" --with-all-dependencies
```

### Development

```bash
./vendor/bin/php-cs-fixer fix
XDEBUG_MODE=coverage ./bin/phpunit --coverage-html=var/coverage
```
