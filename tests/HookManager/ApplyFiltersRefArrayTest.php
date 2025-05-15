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

class ApplyFiltersRefArrayTest extends TestCase {
	public function testPassthru(): void {
		$globalFunctions = $this->mockGlobalFunctions('apply_filters_ref_array');

		$globalFunctions
			->expects($this->once())
			->method('apply_filters_ref_array')
			->with(
				$this->identicalTo('filter-name'),
				$this->identicalTo(['test-value']),
			)
			->willReturn('test-result')
		;

		$expected = 'test-result';
		$actual = HookManager::applyFiltersRefArray('filter-name', ['test-value']);

		self::assertSame($expected, $actual);
	}

	public function testPreinitialization(): void {
		$expected = 'test-value';
		$actual = HookManager::applyFiltersRefArray('filter-name', ['test-value']);

		self::assertSame($expected, $actual);
	}
}
