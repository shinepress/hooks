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

namespace ShinePress\Hooks\Tests\Equivalence;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use ShinePress\Hooks\HookManager;
use ShinePress\Hooks\Tests\TestCase as BaseTestCase;
use WP_Hook;

/**
 * @phpstan-type FilterMethodName 'addFilter'|'hasFilter'|'removeFilter'|'removeAllFilters'
 * @phpstan-type ActionMethodName 'addAction'|'hasAction'|'removeAction'|'removeAllActions'
 * @phpstan-type MethodName FilterMethodName|ActionMethodName
 */
abstract class TestCase extends BaseTestCase {
	#[DataProvider('equivalenceStepProvider')]
	public function testCommands(int $step): void {
		global $wp_filter;

		// initialize $wp_filter
		$wp_filter = static::initial();

		// run all instructions except last
		$count = 1;
		$instruction = null;
		foreach (static::instructions() as $instruction) {
			if ($count >= $step) {
				// this will leave the last instruction un-executed and in the $instruction variable
				break;
			}
			$count++;

			$callable = [HookManager::class, $instruction['method']];
			if (!is_callable($callable)) {
				continue;
			}

			call_user_func_array($callable, $instruction['args']);
		}

		// $instruction will still be null if the instructions() function returns an empty iterable
		self::assertNotNull($instruction, 'there must be at least one instruction defined');

		// save starting state
		$startingState = $wp_filter;

		// run hookmanager instruction uninitialized
		$uninitializedResult = call_user_func_array(
			[HookManager::class, $instruction['method']],
			$instruction['args'],
		);
		$uninitializedState = $wp_filter;

		// initialize wordpress
		$wp_filter = null;
		require_once __DIR__ . '/../../vendor/roots/wordpress-no-content/wp-includes/plugin.php';

		// preserve initialized start state
		$initializedStartState = $wp_filter;

		// restore starting state (initialized)
		$wp_filter = WP_Hook::build_preinitialized_hooks($startingState);

		// run hookmanager instruction initialized
		$initializedResult = call_user_func_array(
			[HookManager::class, $instruction['method']],
			$instruction['args'],
		);
		$initializedState = $wp_filter;

		// restore starting state (initialized)
		$wp_filter = WP_Hook::build_preinitialized_hooks($startingState);

		// run wordpress instruction
		$wordpressResult = call_user_func_array(
			self::getReferenceCallable($instruction['method']),
			$instruction['args'],
		);
		$wordpressState = $wp_filter;

		if (isset($instruction['expect'])) {
			// verify that reference result matches optional expected result
			self::assertSame($instruction['expect'], $wordpressResult, 'result does not match expected');
		}

		// verify that uninitialized result matches wordpress
		self::assertSame($wordpressResult, $uninitializedResult, 'uninitialized hookmanager result does not match wordpress reference result');

		// verify that initialized result matches wordpress
		self::assertSame($wordpressResult, $initializedResult, 'initialized hookmanager result does not match wordpress reference result');

		// verify that uninitalized final state matches wordpress
		self::assertEquals($wordpressState, WP_Hook::build_preinitialized_hooks($uninitializedState), 'uninitialized hookmanager final state does not match wordpress reference state');

		// verify that initialized final state matches wordpress
		self::assertEquals($wordpressState, $initializedState, 'initialized hookmanager final state does not match wordpress reference state');
	}

	public static function equivalenceStepProvider(): Generator {
		$instructions = static::instructions();

		$step = 1;
		foreach (static::instructions() as $instruction) {
			$label = sprintf('Step #%d (%s)', $step, $instruction['method']);
			yield $label => [$step];

			$step++;
		}
	}

	/**
	 * Returns a set of equivalent instructions to peform before and after initialization.
	 *
	 * @return iterable<array{'method': MethodName, 'args': array<mixed>, 'expect'?: mixed}>
	 */
	abstract protected static function instructions(): iterable;

	/**
	 * Returns an initial pre-initialization state for $wp_filter.
	 *
	 * @return array<string, array<int, array<array{'accepted_args': int, 'function': callable}>>>
	 */
	protected static function initial(): array {
		return [];
	}

	/**
	 * @param MethodName $method
	 */
	private static function getReferenceCallable(string $method): callable {
		switch ($method) {
			case 'addFilter':
				return 'add_filter';
			case 'hasFilter':
				return 'has_filter';
			case 'removeFilter':
				return 'remove_filter';
			case 'removeAllFilters':
				return 'remove_all_filters';
			case 'addAction':
				return 'add_action';
			case 'hasAction':
				return 'has_action';
			case 'removeAction':
				return 'remove_action';
			case 'removeAllActions':
				return 'remove_all_actions';
			default:
				return fn(mixed ...$args): array => $args;
		}
	}
}
