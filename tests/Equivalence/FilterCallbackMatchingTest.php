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

class FilterCallbackMatchingTest extends TestCase {
	protected static function instructions(): iterable {
		$callback1 = function(): void {};
		$callback2 = function(): void {};
		$callback3 = function(): void {};
		$callback4 = function(): void {};
		$callback5 = function(): void {};

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback1, 1],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback2, 2],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback3, 3],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback4, 4],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback5, 5],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback1],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback2],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback3],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback4],
		];

		yield [
			'method' => 'addFilter',
			'args' => ['hook', $callback5],
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback1],
			'expect' => 1,
		];

		yield [
			'method' => 'removeFilter',
			'args' => ['hook', $callback1, 1],
			'expect' => true,
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback1],
			'expect' => 10,
		];

		yield [
			'method' => 'removeFilter',
			'args' => ['hook', $callback1, 10],
			'expect' => true,
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback1],
			'expect' => false,
		];

		yield [
			'method' => 'removeFilter',
			'args' => ['hook', $callback1, 1],
			'expect' => false,
		];

		yield [
			'method' => 'removeFilter',
			'args' => ['hook', $callback1, 10],
			'expect' => false,
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback2],
			'expect' => 2,
		];

		yield [
			'method' => 'removeAllFilters',
			'args' => ['hook', 2],
			'expect' => true,
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback2],
			'expect' => 10,
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback3],
			'expect' => 3,
		];

		yield [
			'method' => 'removeAllFilters',
			'args' => ['hook', false],
			'expect' => true,
		];

		yield [
			'method' => 'hasFilter',
			'args' => ['hook', $callback3],
			'expect' => false,
		];
	}
}
