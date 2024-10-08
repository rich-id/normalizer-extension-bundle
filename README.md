Getting Started With RichCongressNormalizerExtensionBundle
=======================================

This version of the bundle requires Symfony 6.0+ and PHP 8.1+.

[![Package version](https://img.shields.io/packagist/v/richcongress/normalizer-extension-bundle)](https://packagist.org/packages/richcongress/normalizer-extension-bundle)
[![Build Status](https://img.shields.io/travis/richcongress/normalizer-extension-bundle.svg?branch=master)](https://travis-ci.org/richcongress/normalizer-extension-bundle?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/richcongress/normalizer-extension-bundle/badge.svg?branch=master)](https://coveralls.io/github/richcongress/normalizer-extension-bundle?branch=master)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/richcongress/normalizer-extension-bundle/issues)
[![License](https://img.shields.io/badge/license-MIT-red.svg)](LICENSE.md)

The normalizer-extension-bundle provides extended functionalities for the Symfony Serializer.


# Quick start

The normalizer-extension-bundle is ready out of the box. You can now create your own object normalizer extension:

```php
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;

class DummyEntityNormalizerExtension extends AbstractObjectNormalizerExtension
{
    /**
     * @var string
     */
    public static $objectClass = DummyEntity::class;

    /**
     * Prefix to add before every context
     */
    public static $contextPrefix = 'dummy_entity_';


    /**
     * ['serialization_group' => 'propertyName']
     * 
     * @return array
     */
    public static function getSupportedGroups(): array
    {
        return [
            'is_beautiful_enough' => 'isBeautifulEnough',
            'name'                => 'wonderfulName',
        ];
    }
    
    public function isBeautifulEnought(): bool
    {
        return true;
    }   
    
    public function getWonderfulName(): string
    {
        return 'DummyEntity is its name';
    }   
}
```

# Table of content

1. [Installation](#1-installation)
2. [Getting started](#2-getting-started)
        - [AbstractObjectNormalizerExtension](docs/NormalizerExtension.md#abstractobjectnormalizerextension)
        - [Write your own Normalizer Extension](docs/NormalizerExtension.md#write-your-own-normalizer-extension)
4. [Versioning](#3-versioning)
5. [Contributing](#4-contributing)
6. [Hacking](#5-hacking)
7. [License](#6-license)


# 1. Installation

This version of the bundle requires Symfony 6.0+ and PHP 8.1+.

### 1.1 Composer

```bash
composer require richcongress/normalizer-extension-bundle
```

### 1.2 Bundles declaration

After the installation, make sure that the bundle is declared correctly within the Kernel's bundles list.

```php
new RichCongress\NormalizerExtensionBundle\RichCongressNormalizerExtensionBundle::class => ['all' => true],
```


# 2. Getting started

- [Normalizer Extension](docs/NormalizerExtension.md)
    - [AbstractObjectNormalizerExtension](docs/NormalizerExtension.md#abstractobjectnormalizerextension)
    - [Write your own Normalizer Extension](docs/NormalizerExtension.md#write-your-own-normalizer-extension)


# 3. Versioning

normalizer-extension-bundle follows [semantic versioning](https://semver.org/). In short the scheme is MAJOR.MINOR.PATCH where
1. MAJOR is bumped when there is a breaking change,
2. MINOR is bumped when a new feature is added in a backward-compatible way,
3. PATCH is bumped when a bug is fixed in a backward-compatible way.

Versions bellow 1.0.0 are considered experimental and breaking changes may occur at any time.


# 4. Contributing

Contributions are welcomed! There are many ways to contribute, and we appreciate all of them. Here are some of the major ones:

* [Bug Reports](https://github.com/richcongress/normalizer-extension-bundle/issues): While we strive for quality software, bugs can happen and we can't fix issues we're not aware of. So please report even if you're not sure about it or just want to ask a question. If anything the issue might indicate that the documentation can still be improved!
* [Feature Request](https://github.com/richcongress/normalizer-extension-bundle/issues): You have a use case not covered by the current api? Want to suggest a change or add something? We'd be glad to read about it and start a discussion to try to find the best possible solution.
* [Pull Request](https://github.com/richcongress/normalizer-extension-bundle/pulls): Want to contribute code or documentation? We'd love that! If you need help to get started, GitHub as [documentation](https://help.github.com/articles/about-pull-requests/) on pull requests. We use the ["fork and pull model"](https://help.github.com/articles/about-collaborative-development-models/) were contributors push changes to their personnal fork and then create pull requests to the main repository. Please make your pull requests against the `master` branch.

As a reminder, all contributors are expected to follow our [Code of Conduct](CODE_OF_CONDUCT.md).


# 5. Hacking

You might use Docker and `docker-compose` to hack the project. Check out the following commands.

```bash
# Start the project
docker-compose up -d

# Install dependencies
docker-compose exec application composer install

# Run tests
docker-compose exec application vendor/phpunit/phpunit/phpunit

# Run a bash within the container
docker-compose exec application bash
```


# 6. License

normalizer-extension-bundle is distributed under the terms of the MIT license.

See [LICENSE](LICENSE.md) for details.
