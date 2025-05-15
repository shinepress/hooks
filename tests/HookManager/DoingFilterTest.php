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

class DoingFilterTest extends TestCase {
	public function testPassthru(): void {
		$globalFunctions = $this->mockGlobalFunctions('doing_filter');

		$globalFunctions
			->expects($this->exactly(2))
			->method('doing_filter')
			->with(
				$this->identicalTo('filter-name'),
			)
			->willReturnOnConsecutiveCalls(true, false)
		;

		$expected1 = true;
		$actual1 = HookManager::doingFilter('filter-name');

		$expected2 = false;
		$actual2 = HookManager::doingFilter('filter-name');

		self::assertSame($expected1, $actual1);
		self::assertSame($expected2, $actual2);
	}

	public function testPreinitialization(): void {
		$expected = false;
		$actual = HookManager::doingFilter('filter-name');

		self::assertSame($expected, $actual);
	}
}
