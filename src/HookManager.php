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

/**
 * @phpstan-type HookCallback array{'accepted_args': int, 'function': callable}
 * @phpstan-type HookPriorities array<int, HookCallback[]>
 * @phpstan-type PreinitializedHooks array<string, HookPriorities>
 */
class HookManager {
	private const DEFAULT_PRIORITY = 10;
	private const DEFAULT_ACCEPTED_ARGS = 1;

	public static function addFilter(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY, int $acceptedArgs = self::DEFAULT_ACCEPTED_ARGS): bool {
		if (function_exists('add_filter')) {
			return add_filter(
				$hookName,
				$callback,
				$priority,
				$acceptedArgs,
			);
		}

		return self::addPreinitializeHook(
			$hookName,
			$callback,
			$priority,
			$acceptedArgs,
		);
	}

	public static function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed {
		if (function_exists('apply_filters')) {
			return apply_filters(
				$hookName,
				$value,
				...$args,
			);
		}

		// wordpress is not initialized yet, return value directly
		return $value;
	}

	/**
	 * @param mixed[] $args
	 */
	public static function applyFiltersRefArray(string $hookName, array $args): mixed {
		if (function_exists('apply_filters_ref_array')) {
			return apply_filters_ref_array(
				$hookName,
				$args,
			);
		}

		// wordpress is not initialized yet, return value directly
		if (count($args) > 0) {
			return $args[0];
		}

		return null;
	}

	public static function hasFilter(string $hookName, callable|false $callback = false): bool|int {
		if (function_exists('has_filter')) {
			return has_filter(
				$hookName,
				$callback,
			);
		}

		return self::hasPreinitializeHook(
			$hookName,
			$callback,
		);
	}

	public static function removeFilter(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY): bool {
		if (function_exists('remove_filter')) {
			return remove_filter(
				$hookName,
				$callback,
				$priority,
			);
		}

		return self::removePreinitializeHook(
			$hookName,
			$callback,
			$priority,
		);
	}

	public static function removeAllFilters(string $hookName, int|false $priority = false): bool {
		if (function_exists('remove_all_filters')) {
			return remove_all_filters(
				$hookName,
				$priority,
			);
		}

		return self::removeAllPreinitializeHooks(
			$hookName,
			$priority,
		);
	}

	public static function currentFilter(): string|false {
		if (function_exists('current_filter')) {
			return current_filter();
		}

		return false;
	}

	public static function doingFilter(?string $hookName = null): bool {
		if (function_exists('doing_filter')) {
			return doing_filter(
				$hookName,
			);
		}

		return false;
	}

	public static function addAction(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY, int $acceptedArgs = self::DEFAULT_ACCEPTED_ARGS): bool {
		if (function_exists('add_action')) {
			return add_action(
				$hookName,
				$callback,
				$priority,
				$acceptedArgs,
			);
		}

		return self::addPreinitializeHook(
			$hookName,
			$callback,
			$priority,
			$acceptedArgs,
		);
	}

	public static function doAction(string $hookName, mixed ...$args): void {
		if (function_exists('do_action')) {
			do_action(
				$hookName,
				...$args,
			);

			return;
		}

		// wordpress is not initialized yet, do nothing
		return;
	}

	/**
	 * @param mixed[] $args
	 */
	public static function doActionRefArray(string $hookName, array $args): void {
		if (function_exists('do_action_ref_array')) {
			do_action_ref_array(
				$hookName,
				$args,
			);

			return;
		}

		// wordpress is not initialized yet, do nothing
		return;
	}

	public static function hasAction(string $hookName, callable|false $callback = false): bool|int {
		if (function_exists('has_action')) {
			return has_action(
				$hookName,
				$callback,
			);
		}

		return self::hasPreinitializeHook(
			$hookName,
			$callback,
		);
	}

	public static function removeAction(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY): bool {
		if (function_exists('remove_action')) {
			return remove_action(
				$hookName,
				$callback,
				$priority,
			);
		}

		return self::removePreinitializeHook(
			$hookName,
			$callback,
			$priority,
		);
	}

	public static function removeAllActions(string $hookName, int|false $priority = false): bool {
		if (function_exists('remove_all_actions')) {
			return remove_all_actions(
				$hookName,
				$priority,
			);
		}

		return self::removeAllPreinitializeHooks(
			$hookName,
			$priority,
		);
	}

	public static function currentAction(): string|false {
		if (function_exists('current_action')) {
			return current_action();
		}

		return false;
	}

	public static function doingAction(?string $hookName = null): bool {
		if (function_exists('doing_action')) {
			return doing_action(
				$hookName,
			);
		}

		return false;
	}

	public static function didAction(string $hookName): int {
		if (function_exists('did_action')) {
			return did_action(
				$hookName,
			);
		}

		return 0;
	}

	/*
	private static function getPreinitializedHooks(): array {
		if (!array_key_exists('wp_filter', $GLOBALS) || !is_array($GLOBALS['wp_filter'])) {
			return [];
		}

		$output = [];
		foreach ($GLOBALS['wp_filter'] as $hookName => $priorities) {
			if (!is_string($hookName)) {
				continue;
			}

			if (!is_array($priorities)) {
				continue;
			}

			$output[$hookName] = [];

			foreach ($priorities as $priority => $callbacks) {
				if (!is_int($priority)) {
					continue;
				}

				if (!is_array($callbacks)) {
					continue;
				}

				$output[$hookName][$priority] = [];

				foreach ($callbacks as $callback) {
					if (!is_array($callback)) {
						continue;
					}

					if (!isset($callback['accepted_args']) || !is_int($callback['accepted_args'])) {
						continue;
					}

					if (!isset($callback['function']) || !is_callable($callback['function'])) {
						continue;
					}

					$output[$hookName][$priority][] = [
						'accepted_args' => $callback['accepted_args'],
						'function' => $callback['function'],
					];
				}
			}
		}

		return $output;
	}
	*/

	/*
	private static function setPreinitializedHooks(array $filters = []): void {
		$GLOBALS['wp_filter'] = $filters;
	}


	/*
	private static function addPreinitializeHook(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY, int $acceptedArgs = self::DEFAULT_ACCEPTED_ARGS): bool {
		$hooks = self::getPreinitializedHooks();

		$hookExists = array_key_exists($hookName, $hooks);
		if (!$hookExists) {
			// create new hook
			$hooks[$hookName] = [];
		}

		$priorityExists = array_key_exists($priority, $hooks[$hookName]);
		if (!$priorityExists) {
			// create new priority
			$hooks[$hookName][$priority] = [];
		}

		// add hook
		$hooks[$hookName][$priority][] = [
			'accepted_args' => $acceptedArgs,
			'function'      => $callback,
		];

		if (!$priorityExists && count($hooks[$hookName]) > 1) {
			// sort hook priorities
			ksort($hooks[$hookName], SORT_NUMERIC);
		}

		self::setPreinitializedHooks($hooks);

		return true;
	}
	*/

	private static function addPreinitializeHook(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY, int $acceptedArgs = self::DEFAULT_ACCEPTED_ARGS): bool {
		global $wp_filter;

		if (!is_array($wp_filter)) {
			$wp_filter = [];
		}

		if (!array_key_exists($hookName, $wp_filter) || !is_array($wp_filter[$hookName])) {
			// ensure hook data is an array
			$wp_filter[$hookName] = [];
		}

		$sort = false;
		if (!array_key_exists($priority, $wp_filter[$hookName])) {
			if (!empty($wp_filter[$hookName])) {
				// sorting only needs to happen if a new priority was added to a hook with pre-existing priorities
				$sort = true;
			}

			// create new priority
			$wp_filter[$hookName][$priority] = [];
		} elseif (!is_array($wp_filter[$hookName][$priority])) {
			// ensure priority data is an array
			$wp_filter[$hookName][$priority] = [];
		}

		// add hook
		$wp_filter[$hookName][$priority][] = [
			'accepted_args' => $acceptedArgs,
			'function' => $callback,
		];

		if ($sort) {
			// sort hook priorities
			ksort($wp_filter[$hookName], SORT_NUMERIC);
		}

		return true;
	}

	private static function hasPreinitializeHook(string $hookName, callable|false $callback = false): bool|int {
		global $wp_filter;

		if (!is_array($wp_filter)) {
			// $wp_filter is not an array
			return false;
		}

		if (!array_key_exists($hookName, $wp_filter)) {
			// hook name does not exist
			return false;
		}

		if ($callback === false) {
			// hook name exists but callback is false
			return true;
		}

		if (!is_array($wp_filter[$hookName])) {
			// hook data is not an array, unable to located specified callback
			return false;
		}

		foreach ($wp_filter[$hookName] as $priority => $hooks) {
			if (!is_array($hooks)) {
				// can't match callbacks if they don't exist
				continue;
			}

			foreach ($hooks as $hook) {
				if (!is_array($hook)) {
					// hook must be an array
					continue;
				}

				if (!isset($hook['function'])) {
					// undefined hook function cannot be compared
					continue;
				}

				if (!is_callable($hook['function'])) {
					// non-callable hook function cannot be compared
					continue;
				}

				if (self::compareCallbacks($callback, $hook['function'])) {
					return $priority;
				}
			}
		}

		// there is no matching callback defined for the specified hook name
		return false;
	}

	private static function removePreinitializeHook(string $hookName, callable $callback, int $priority = self::DEFAULT_PRIORITY): bool {
		global $wp_filter;

		if (!is_array($wp_filter)) {
			// $wp_filter is not an array
			return false;
		}

		if (!array_key_exists($hookName, $wp_filter)) {
			// hook name does not exist
			return false;
		}

		if (!is_array($wp_filter[$hookName])) {
			// hook data is not an array, unable to locate specified priority
			return false;
		}

		if (!array_key_exists($priority, $wp_filter[$hookName])) {
			// priority does not exist
			return false;
		}

		if (!is_array($wp_filter[$hookName][$priority])) {
			// priority data is not an array, unable to locate specified callback
			return false;
		}

		$found = false;
		$remaining = [];

		foreach ($wp_filter[$hookName][$priority] as $hook) {
			if (!is_array($hook)) {
				// hook must be an array
				continue;
			}

			if (!isset($hook['function'])) {
				// undefined hook function cannot be compared
				continue;
			}

			if (!is_callable($hook['function'])) {
				// non-callable hook function cannot be compared
				continue;
			}

			if (self::compareCallbacks($callback, $hook['function'])) {
				$found = true;

				continue;
			}
			$remaining[] = $hook;
		}

		if ($found) {
			$wp_filter[$hookName][$priority] = $remaining;

			if (empty($wp_filter[$hookName][$priority])) {
				unset($wp_filter[$hookName][$priority]);
			}

			if (empty($wp_filter[$hookName])) {
				unset($wp_filter[$hookName]);
			}
		}

		return $found;
	}

	private static function removeAllPreinitializeHooks(string $hookName, int|false $priority = false): bool {
		global $wp_filter;

		if (!is_array($wp_filter)) {
			// $wp_filter is not an array
			return true;
		}

		if (!array_key_exists($hookName, $wp_filter)) {
			// hook name does not exist, nothing to remove
			return true;
		}

		if (!is_int($priority)) {
			// $priority is false, remove all hooks for specified name
			unset($wp_filter[$hookName]);

			return true;
		}

		if (!is_array($wp_filter[$hookName])) {
			// hook data is not an array, nothing to remove
			return true;
		}

		if (!array_key_exists($priority, $wp_filter[$hookName])) {
			// priority does not exist
			return true;
		}

		unset($wp_filter[$hookName][$priority]);

		if (empty($wp_filter[$hookName])) {
			unset($wp_filter[$hookName]);
		}

		return true;
	}

	private static function compareCallbacks(callable $callback1, callable $callback2): bool {
		if (is_string($callback1)) {
			if (!is_string($callback2)) {
				return false;
			}

			// function names are case-insensitive
			if (strtolower($callback1) !== strtolower($callback2)) {
				return false;
			}

			return true;
		}

		if (is_object($callback1)) {
			// works for both invokable objects and closures
			if ($callback1 !== $callback2) {
				return false;
			}

			return true;
		}

		if (is_array($callback1) && count($callback1) === 2) {
			if (!is_array($callback2) || count($callback2) !== 2) {
				return false;
			}

			if (is_object($callback1[0]) && $callback1[0] !== $callback2[0]) {
				return false;
			}

			// class names are case-insensitive
			if (is_string($callback2[0]) && strtolower($callback1[0]) !== strtolower($callback2[0])) {
				return false;
			}

			// method names are case-insensitive
			if (strtolower($callback1[1]) !== strtolower($callback2[1])) {
				return false;
			}

			return true;
		}

		// unknown callback type
		return false;
	}
}
