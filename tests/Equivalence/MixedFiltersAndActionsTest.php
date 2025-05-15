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

class MixedFiltersAndActionsTest extends TestCase {
	protected static function instructions(): iterable {
		yield [
			'method' => 'addFilter',
			'args' => ['test-filter', 'sprintf'],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['test-filter', 'vsprintf'],
		];

		yield [
			'method' => 'addAction',
			'args' => ['test-action', 'sprintf'],
		];

		yield [
			'method' => 'addAction',
			'args' => ['test-action', 'vsprintf'],
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['test-filter'],
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['test-action', 'sprintf'],
		];

		yield [
			'method' => 'hasAction',
			'args' => ['test-action', 'vsprintf'],
		];

		yield [
			'method' => 'removeAllFilters',
			'args' => ['test-action'],
		];

		yield [
			'method' => 'hasAction',
			'args' => ['test-action', 'vsprintf'],
		];
	}
}
