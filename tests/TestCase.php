<?php

/*
 * This file is part of ShinePress.
 *
 * (c) Shine United LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ShinePress\Hooks\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected static function toDo(): void {
		$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

		$function = $caller['function'];
		if (isset($caller['class'])) {
			$function = $caller['class'] . '::' . $function;
		}

		$message = 'To-Do: ' . $function;

		self::markTestIncomplete($function);
	}

	protected function initializeWordpressHooks(): void {
		require_once __DIR__ . '/../vendor/roots/wordpress-no-content/wp-includes/plugin.php';
	}

	protected function mockGlobalFunctions(string ...$functions): MockObject {
		$key = uniqid('mock_global_', false);
		$target = $key . '_target';
		$placeholder = $key . '_placeholder';

		$targetFunctions = [];
		$placeholderFunctions = [];
		$globalFunctions = [];

		foreach ($functions as $function) {
			$placeholderFunctions[] = '
					function ' . $function . '() {
						// placeholder
					}';

			$targetFunctions[] = '
					static function ' . $function . '(...$args) {
						return call_user_func_array([self::$mock, \'' . $function . '\'], $args);
					}';

			$globalFunctions[] = '
				function ' . $function . '() {
					return call_user_func_array([' . $target . '::class, \'' . $function . '\'], func_get_args());
				}';
		}

		$code = '
			namespace {
				class ' . $placeholder . ' {
					' . implode("\n", $placeholderFunctions) . '
				}

				class ' . $target . ' {
					static $mock;

					' . implode("\n", $targetFunctions) . '
				}

				' . implode("\n", $globalFunctions) . '
			}
		';

		eval($code);

		if (!class_exists($placeholder, false)) {
			throw new RuntimeException(sprintf('Missing placeholder class "%s"', $placeholder));
		}

		$mock = $this->createMock($placeholder);
		$target::$mock = $mock;

		return $mock;
	}
}
