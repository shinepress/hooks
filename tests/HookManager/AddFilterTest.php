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

namespace ShinePress\Hooks\Tests\HookManager;

use ShinePress\Hooks\HookManager;
use SplQueue;

class AddFilterTest extends TestCase {
	public function testPassthruStringCallback(): void {
		$globalFunctions = $this->mockGlobalFunctions('add_filter');

		$callback = 'sprintf';

		$globalFunctions
			->expects($this->once())
			->method('add_filter')
			->with(
				$this->identicalTo('filter-name'),
				$this->identicalTo($callback),
				$this->identicalTo(6),
				$this->identicalTo(9),
			)
			->willReturn(true)
		;

		$expected = true;
		$actual = HookManager::addFilter('filter-name', $callback, 6, 9);

		self::assertSame($expected, $actual);
	}

	public function testPassthruArrayCallback(): void {
		$globalFunctions = $this->mockGlobalFunctions('add_filter');

		$callback = [new SplQueue(), 'add'];

		$globalFunctions
			->expects($this->once())
			->method('add_filter')
			->with(
				$this->identicalTo('filter-name'),
				$this->identicalTo($callback),
				$this->identicalTo(6),
				$this->identicalTo(9),
			)
			->willReturn(true)
		;

		$expected = true;
		$actual = HookManager::addFilter('filter-name', $callback, 6, 9);

		self::assertSame($expected, $actual);
	}

	public function testPassthruClosureCallback(): void {
		$globalFunctions = $this->mockGlobalFunctions('add_filter');

		$closure = fn(string $foo, string $bar): string => $foo . ':' . $bar;

		$globalFunctions
			->expects($this->once())
			->method('add_filter')
			->with(
				$this->identicalTo('filter-name'),
				$this->identicalTo($closure),
				$this->identicalTo(6),
				$this->identicalTo(9),
			)
			->willReturn(true)
		;

		$expected = true;
		$actual = HookManager::addFilter('filter-name', $closure, 6, 9);

		self::assertSame($expected, $actual);
	}

	public function testUnsetPreexisting(): void {
		global $wp_filter;

		$wp_filter = null;

		$callback = 'sprintf';

		HookManager::addFilter('filter-name', $callback, 6, 9);

		$expected = [
			'filter-name' => [
				6 => [
					[
						'accepted_args' => 9,
						'function' => $callback,
					],
				],
			],
		];

		self::assertSame($expected, $wp_filter);
	}

	public function testNewHookName(): void {
		global $wp_filter;

		$wp_filter = [
			'other-filter1' => [],
			'other-filter2' => [],
			'other-filter3' => [],
		];

		$callback = 'sprintf';

		HookManager::addFilter('filter-name', $callback, 6, 9);

		$expected = [
			'other-filter1' => [],
			'other-filter2' => [],
			'other-filter3' => [],
			'filter-name' => [
				6 => [
					[
						'accepted_args' => 9,
						'function' => $callback,
					],
				],
			],
		];

		self::assertSame($expected, $wp_filter);
	}

	public function testNewPriority(): void {
		global $wp_filter;

		$wp_filter = [
			'other-filter1' => [],
			'other-filter2' => [],
			'other-filter3' => [],
			'filter-name' => [
				1 => [],
				2 => [],
				3 => [],
				7 => [],
				8 => [],
			],
		];

		$callback = 'sprintf';

		HookManager::addFilter('filter-name', $callback, 6, 9);

		$expected = [
			'other-filter1' => [],
			'other-filter2' => [],
			'other-filter3' => [],
			'filter-name' => [
				1 => [],
				2 => [],
				3 => [],
				6 => [
					[
						'accepted_args' => 9,
						'function' => $callback,
					],
				],
				7 => [],
				8 => [],
			],
		];

		self::assertSame($expected, $wp_filter);
	}

	public function testExistingHookNameAndPriority(): void {
		global $wp_filter;

		$wp_filter = [
			'other-filter1' => [],
			'other-filter2' => [],
			'other-filter3' => [],
			'filter-name' => [
				1 => [],
				2 => [],
				3 => [],
				6 => [
					['accepted_args' => 2, 'function' => 'strtolower'],
					['accepted_args' => 3, 'function' => 'strtoupper'],
					['accepted_args' => 1, 'function' => 'rtrim'],
					['accepted_args' => 2, 'function' => 'ltrim'],
				],
				7 => [],
				8 => [],
			],
		];

		$callback = 'sprintf';

		HookManager::addFilter('filter-name', $callback, 6, 9);

		$expected = [
			'other-filter1' => [],
			'other-filter2' => [],
			'other-filter3' => [],
			'filter-name' => [
				1 => [],
				2 => [],
				3 => [],
				6 => [
					['accepted_args' => 2, 'function' => 'strtolower'],
					['accepted_args' => 3, 'function' => 'strtoupper'],
					['accepted_args' => 1, 'function' => 'rtrim'],
					['accepted_args' => 2, 'function' => 'ltrim'],
					[
						'accepted_args' => 9,
						'function' => $callback,
					],
				],
				7 => [],
				8 => [],
			],
		];

		self::assertSame($expected, $wp_filter);
	}
}
