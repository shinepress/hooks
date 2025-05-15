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

class CurrentActionTest extends TestCase {
	public function testPassthru(): void {
		$globalFunctions = $this->mockGlobalFunctions('current_action');

		$globalFunctions
			->expects($this->once())
			->method('current_action')
			->willReturn('action-name')
		;

		$expected = 'action-name';
		$actual = HookManager::currentAction();

		self::assertSame($expected, $actual);
	}

	public function testPreinitialization(): void {
		$expected = false;
		$actual = HookManager::currentAction();

		self::assertSame($expected, $actual);
	}
}
