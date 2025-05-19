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

namespace ShinePress\Hooks;

use Attribute;
use Closure;
use ReflectionException;
use ReflectionMethod;
use ShinePress\Framework\Attribute\MethodAttributeInterface;
use ShinePress\Framework\Module;
use ValueError;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Hook implements MethodAttributeInterface {
	private string $name;
	private int $priority;

	public function __construct(string $name, int $priority = 10) {
		$this->name = $name;
		$this->priority = $priority;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getPriority(): int {
		return $this->priority;
	}

	public function register(Module $module, ReflectionMethod $method): void {
		try {
			$callback = $this->createClosure($module, $method);
		} catch (ValueError $error) {
			// should never happen, but exit if it does
			return;
		} catch (ReflectionException $exception) {
			// should never happen, but exit if it does
			return;
		}

		HookManager::addFilter(
			$this->getName(),
			$callback,
			$this->getPriority(),
			$method->getNumberOfParameters(),
		);
	}

	/**
	 * @throws ValueError
	 * @throws ReflectionException
	 */
	private function createClosure(Module $module, ReflectionMethod $method): Closure {
		if ($method->isStatic()) {
			return $method->getClosure();
		}

		return $method->getClosure($module);
	}
}
