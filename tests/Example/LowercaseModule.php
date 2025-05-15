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

namespace ShinePress\Hooks\Tests\Example;

use ShinePress\Hooks\Filter;
use ShinePress\Framework\Module;

class LowercaseModule extends Module {
	#[Filter('lowercase')]
	public function toLowercase(string $input): string {
		return strtolower($input);
	}
}
