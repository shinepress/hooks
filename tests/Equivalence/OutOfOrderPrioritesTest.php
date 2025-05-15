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

class OutOfOrderPrioritesTest extends TestCase {
	protected static function instructions(): iterable {
		yield [
			'method' => 'addFilter',
			'args' => ['filter', 'sprintf', 4],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['filter', 'sprintf', 3],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['filter', 'sprintf', 11],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['filter', 'sprintf', 1],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['filter', 'printf', 3],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['filter', 'sprintf', 2],
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['filter', 'sprintf'],
		];
	}
}
