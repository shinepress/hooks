# shinepress/hooks

[![License](https://img.shields.io/packagist/l/shinepress/hooks)](https://github.com/shinepress/hooks/blob/main/LICENSE)
[![Latest Version](https://img.shields.io/packagist/v/shinepress/hooks?label=latest)](https://packagist.org/packages/shinepress/hooks/)
[![PHP Version](https://img.shields.io/packagist/dependency-v/shinepress/hooks/php?label=php)](https://www.php.net/releases/index.php)
[![Main Status](https://img.shields.io/github/actions/workflow/status/shinepress/hooks/verify.yml?branch=main&label=main)](https://github.com/shinepress/hooks/actions/workflows/verify.yml?query=branch%3Amain)
[![Release Status](https://img.shields.io/github/actions/workflow/status/shinepress/hooks/verify.yml?branch=release&label=release)](https://github.com/shinepress/hooks/actions/workflows/verify.yml?query=branch%3Arelease)
[![Develop Status](https://img.shields.io/github/actions/workflow/status/shinepress/hooks/verify.yml?branch=develop&label=develop)](https://github.com/shinepress/hooks/actions/workflows/verify.yml?query=branch%3Adevelop)


## Description

A tool for managing WordPress hooks. Allows registration of hooks prior to WordPress initialization as well as providing attributes that can be used for the same purpose in conjuction with the ShinePress [framework](https://packagist.org/packages/shinepress/framework/).


## Installation

The recommendend installation method is with composer:
```sh
$ composer require shinepress/hooks
```


## Usage


### HookManager

The HookManager class provides static methods for managing WordPress hooks.

```php
use ShinePress\Hooks\HookManager;

function callback_function($param1, $param2) {
	// code
}

// this can be used prior to initialization
HookManager::addFilter('filter_name', 'callback_function', 10, 2);
```

### Attribute Hooks

When using the framework, hooks can be defined by adding attributes to functions in your custom module class.

```php
use ShinePress\Framework\Module;
use ShinePress\Hooks\Filter;

class CustomModule extends Module {

	#[Filter('lowercase')]
	public function lowercase(string $value): string {
		return strtolower($value);
	}

	#[Filter('uppercase')]
	public function uppercase(string $value): string {
		return strtoupper($value);
	}
}

CustomModule::register();
// WordPress Equivalent:
//     add_filter('lowercase', [CustomModule::instance(), 'lowercase'], 10, 1);
//     add_filter('uppercase', [CustomModule::instance(), 'uppercase'], 10, 1);
```

Multiple hooks can also be applied to a single callback.
```php
use ShinePress\Framework\Module;
use ShinePress\Hooks\Action;
use ShinePress\Hooks\Filter;

class CustomModule extends Module {

	#[Filter('example-filter', 12)]
	#[Action('example-action')]
	public function exampleFunction(mixed $value) {
		// do something
	}
}

CustomModule::register();
// WordPress Equivalent:
//     add_filter('example-filter', [CustomModule::instance(), 'exampleFunction'], 12, 1);
//     add_action('example-action', [CustomModule::instance(), 'exampleFunction'], 10, 1);
```