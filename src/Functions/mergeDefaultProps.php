<?php

/**
 * Merges Props with default props recursively
 *
 * - every key that is missing in Props will be merged
 * - if property on the Props is a array then the defaults are merged recursively
 * - every empty `array` turns to null
 *
 * @param array $props Passed by reference
 * @param array $defaultProps
 * @return void
 */
function mergeDefaultProps(array &$props, array $defaultProps) {
  foreach ($defaultProps as $key => $value) {
    if (array_key_exists($key, $props)) {
      if (empty($props[$key]) && $props[$key] !== 0) {
        $props[$key] = null; // Clear to unify
      } elseif (is_array($props[$key]) && is_array($value)) {
        mergeDefaultProps($props[$key], $value);
      }
    } else /* `$key` does not exist on `$props` */ {
      if (is_array($value)) {
        $props[$key] = null;
      } else {
        $props[$key] = $value;
      }
    }
  }
}
