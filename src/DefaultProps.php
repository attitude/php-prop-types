<?php

namespace PropTypes;

class DefaultProps {
  private static $defaultProps = [];

  public static function register(string $name, $value): void {
    $name = trim($name);
    assert(!empty($name), "\$name is non-empty string");

    if (DEFAULT_PROPS_WARNINGS_ENABLED && isset(static::$defaultProps[$name])) {
      trigger_error("Overriding already defined `{$name}` default props", E_USER_NOTICE);
    }

    $type = gettype($value);

    switch($type) {
      case 'resource':
      case 'unknown type':
        throw new \Exception("Unable to store default props of {$type} type", 400);

      default:
        static::$defaultProps[$name] = $value;
      break;
    }
  }

  /**
   * Alias for static::register()
   */
  public static function for(string $name, $value) {
    return static::register($name, $value);
  }

  public static function __callStatic(string $name, array $arguments__unused = []) {
    if (!isset(static::$defaultProps[$name])) {
      throw new \Exception("`{$name}` is not registered", 404);
    }

    return static::$defaultProps[$name];
  }
}
