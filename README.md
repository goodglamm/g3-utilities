# g3-utilities

## Introduction

`g3-utilities` is a collection of utility libraries helpful when working with WordPress. It is derived from the utilities library written for internal use on our websites at [The Good Glamm Group](https://www.goodglamm.com/). We decided to make part of that library an Open Source `composer` package in the hope that others can benefit from these utilities in their projects.

On its own this package does not do anything, which is why we decided not to make it available as a WordPress plugin. It is meant to be used as a support/utility library for existing/new WordPress code, be it theme or plugin.

- [License](#License)
- [Requirements](#Requirements)
- [Installation](#Installation)
- [Usage](docs/index.md)


## License

This package is licensed under `GPL v2.0-or-later`. You are free to use this package however you like as long as you comply with the terms of the license.

## Requirements

This composer package has following requirements for use:

- **PHP >= 7.4** : This package was written for PHP 7.4.x and uses some features which might not be available in lower versions.
- **WordPress** : Some of the functionality in this package uses WordPress functions. This package is not meant to be used without WordPress and we do not have any plans of adding/supporting polyfills for those functions to make this package work without WordPress. If you want to use this package without WordPress, you are free to do so and add any polyfills you might need.

## Installation

This is a `composer` package and to use it, you should install `composer` and fetch it through that. That way you would be able to easily have control over which version you use and `composer` would take care of any dependencies this package might have.

Once you have `composer` installed, run the following command in your project directory.

```
composer require g3/utilities
```

Once the package is installed, put the following in your project code before you use any of the package API. If you are not adding this in a file which resides in the same directory as your `composer.json` file or `vendor` directory then adjust the path below accordingly.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

This will allow you to use this package's API (& that of any other `composer` package you install) without bothering about loading up package files.

