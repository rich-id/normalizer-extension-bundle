Getting Started With RichCongressSerializerBundle
=======================================

This version of the bundle requires Symfony 4.4+ and PHP 7.3+.

[![Package version](https://img.shields.io/packagist/v/richcongress/normalizer-bundle)](https://packagist.org/packages/richcongress/normalizer-bundle)
[![Build Status](https://img.shields.io/travis/richcongress/normalizer-bundle.svg?branch=master)](https://travis-ci.org/richcongress/normalizer-bundle?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/richcongress/normalizer-bundle/badge.svg?branch=master)](https://coveralls.io/github/richcongress/normalizer-bundle?branch=master)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/richcongress/normalizer-bundle/issues)
[![License](https://img.shields.io/badge/license-MIT-red.svg)](LICENSE.md)

The normalizer-bundle provides extended functionnalities for the Symfony Serializer.


# Quick start

The normalizer-bundle is ready out of the box. The VirtualProperty will be added to the normalized data when you use the annotation.

```php
use RichCongress\NormalizerBundle\Serializer\Annotation\VirtualProperty;

class DummyEntity
{
    /**
     * @VirtualProperty("data", groups={"dummy_entity_wonderful_data"})
     * 
     * @return array
     */
    public function getWonderfulData(): array
    {
        return ['something'];
    }
}
```


You may also create your own object normalizer extension:

```php
use RichCongress\NormalizerBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;

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
    - [Configuration](Docs/Configuration.md)
    - [Normalizer Extension](Docs/NormalizerExtension.md)
        - [VirtualPropertyNormalizerExtension](Docs/NormalizerExtension.md#virtualpropertynormalizerextension)
        - [AbstractObjectNormalizerExtension](Docs/NormalizerExtension.md#abstractobjectnormalizerextension)
        - [Write your own Normalizer Extension](Docs/NormalizerExtension.md#write-your-own-normalizer-extension)
4. [Versioning](#3-versioning)
5. [Contributing](#4-contributing)
6. [Hacking](#5-hacking)
7. [License](#6-license)


# 1. Installation

This version of the bundle requires Symfony 4.4+ and PHP 7.3+.

### 1.1 Composer

```bash
composer require richcongress/normalizer-bundle
```

### 1.2 Bundles declaration

After the installation, make sure that the bundle is declared correctly within the Kernel's bundles list.

```php
new RichCongress\NormalizerBundle\RichCongressNormalizerBundle::class => ['all' => true],
```


# 2. Getting started

- [Configuration](Docs/Configuration.md)
- [Normalizer Extension](Docs/NormalizerExtension.md)
    - [VirtualPropertyNormalizerExtension](Docs/NormalizerExtension.md#virtualpropertynormalizerextension)
    - [AbstractObjectNormalizerExtension](Docs/NormalizerExtension.md#abstractobjectnormalizerextension)
    - [Write your own Normalizer Extension](Docs/NormalizerExtension.md#write-your-own-normalizer-extension)


# 3. Versioning

normalizer-bundle follows [semantic versioning](https://semver.org/). In short the scheme is MAJOR.MINOR.PATCH where
1. MAJOR is bumped when there is a breaking change,
2. MINOR is bumped when a new feature is added in a backward-compatible way,
3. PATCH is bumped when a bug is fixed in a backward-compatible way.

Versions bellow 1.0.0 are considered experimental and breaking changes may occur at any time.


# 4. Contributing

Contributions are welcomed! There are many ways to contribute, and we appreciate all of them. Here are some of the major ones:

* [Bug Reports](https://github.com/richcongress/normalizer-bundle/issues): While we strive for quality software, bugs can happen and we can't fix issues we're not aware of. So please report even if you're not sure about it or just want to ask a question. If anything the issue might indicate that the documentation can still be improved!
* [Feature Request](https://github.com/richcongress/normalizer-bundle/issues): You have a use case not covered by the current api? Want to suggest a change or add something? We'd be glad to read about it and start a discussion to try to find the best possible solution.
* [Pull Request](https://github.com/richcongress/normalizer-bundle/pulls): Want to contribute code or documentation? We'd love that! If you need help to get started, GitHub as [documentation](https://help.github.com/articles/about-pull-requests/) on pull requests. We use the ["fork and pull model"](https://help.github.com/articles/about-collaborative-development-models/) were contributors push changes to their personnal fork and then create pull requests to the main repository. Please make your pull requests against the `master` branch.

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

normalizer-bundle is distributed under the terms of the MIT license.

See [LICENSE](LICENSE.md) for details.