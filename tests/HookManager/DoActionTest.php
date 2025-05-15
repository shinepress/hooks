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

class DoActionTest extends TestCase {
	public function testPassthru(): void {
		$globalFunctions = $this->mockGlobalFunctions('do_action');

		$globalFunctions
			->expects($this->once())
			->method('do_action')
			->with(
				$this->identicalTo('action-name'),
				$this->identicalTo('test-value'),
			)
			->willReturn(null)
		;

		HookManager::doAction('action-name', 'test-value');
	}

	public function testPreinitialization(): void {
		self::expectNotToPerformAssertions();

		HookManager::doAction('action-name', 'test-value');
	}
}
