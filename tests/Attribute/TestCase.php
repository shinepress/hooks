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

namespace ShinePress\Hooks\Tests\Attribute;

use PHPUnit\Framework\Attributes\DataProvider;
use ShinePress\Framework\MethodAttributeInterface;
use ShinePress\Hooks\Hook;
use ShinePress\Hooks\Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	#[DataProvider('hookDataProvider')]
	public function testInstance(string $name, int $priority): void {
		$attribute = $this->createInstance($name, $priority);

		self::assertInstanceOf(MethodAttributeInterface::class, $attribute);
		self::assertInstanceOf(Hook::class, $attribute);

		self::assertSame($name, $attribute->getName());
		self::assertSame($priority, $attribute->getPriority());
	}

	/**
	 * @return iterable<string, array{0: string, 1: int}>
	 */
	public static function hookDataProvider(): iterable {
		yield 'name (1)' => ['name', 1];
		yield 'filter (10)' => ['filter', 10];
		yield 'action (-5)' => ['action', -5];
	}

	abstract protected function createInstance(string $name, int $priority): object;
}
