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

use ShinePress\Framework\Module;
use ShinePress\Hooks\Filter;

class AppendWorldModule extends Module {
	#[Filter('append')]
	public function appendWorld(string $input): string {
		return $input . 'world';
	}
}
