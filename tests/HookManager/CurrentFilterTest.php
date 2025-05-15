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

class CurrentFilterTest extends TestCase {
	public function testPassthru(): void {
		$globalFunctions = $this->mockGlobalFunctions('current_filter');

		$globalFunctions
			->expects($this->once())
			->method('current_filter')
			->willReturn('filter-name')
		;

		$expected = 'filter-name';
		$actual = HookManager::currentFilter();

		self::assertSame($expected, $actual);
	}

	public function testPreinitialization(): void {
		$expected = false;
		$actual = HookManager::currentFilter();

		self::assertSame($expected, $actual);
	}
}
