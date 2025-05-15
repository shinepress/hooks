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

class DidActionTest extends TestCase {
	public function testPassthru(): void {
		$globalFunctions = $this->mockGlobalFunctions('did_action');

		$globalFunctions
			->expects($this->once())
			->method('did_action')
			->with(
				$this->identicalTo('action-name'),
			)
			->willReturn(42)
		;

		$expected = 42;
		$actual = HookManager::didAction('action-name');

		self::assertSame($expected, $actual);
	}

	public function testPreinitialization(): void {
		$expected = 0;
		$actual = HookManager::didAction('action-name');

		self::assertSame($expected, $actual);
	}
}
