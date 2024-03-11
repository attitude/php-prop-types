<?php

namespace PropTypes;

if (!defined('PROP_TYPES_STRICT_OBJECTS_SHAPE')) {
  define('PROP_TYPES_STRICT_OBJECTS_SHAPE', false);
}

if (!defined('PROP_TYPES_WARNINGS_ENABLED')) {
  define('PROP_TYPES_WARNINGS_ENABLED', true);
}

if (!defined('DEFAULT_PROPS_WARNINGS_ENABLED')) {
  define('DEFAULT_PROPS_WARNINGS_ENABLED', true);
}

if (!defined('PROP_TYPES_DEBUG')) {
  define('PROP_TYPES_DEBUG', false);
}

function invariantPrimitive(bool $test, $typeExpected = '', $value) {
  if (!$test) {
    throw new \Exception("expecting {$typeExpected} but ".gettype($value)." was given", 500);
  }
}

function invariant(bool $test, $message = '') {
  if (!$test) {
    throw new \Exception($message, 400);
  }
}

PropTypes::register('array', function () {
  return function($value) {
    invariantPrimitive(is_array($value), 'array', $value);
  };
});

PropTypes::register('null', function () {
  return function($value) {
    invariantPrimitive(is_null($value), 'null', $value);
  };
});

PropTypes::register(['boolean', 'bool'], function () {
  return function($value) {
    invariantPrimitive(is_bool($value), 'boolean', $value);
  };
});

PropTypes::register(['function', 'func'], function () {
  return function($value) {
    invariantPrimitive(is_callable($value), 'callable', $value);
  };
});

PropTypes::register('int', function () {
  return function($value) {
    invariantPrimitive(is_int($value), 'int', $value);
  };
});

PropTypes::register('float', function () {
  return function($value) {
    invariantPrimitive(is_float($value), 'float', $value);
  };
});

PropTypes::register('number', function () {
  return function($value) {
    invariantPrimitive(is_int($value) || is_float($value), 'number', $value);
  };
});

PropTypes::register('object', function () {
  return function($value) {
    invariantPrimitive(is_object($value), 'object', $value);
  };
});

PropTypes::register('string', function () {
  return function($value) {
    invariantPrimitive(is_string($value), 'string', $value);
  };
});

PropTypes::register('instanceOf', function ($class) {
  return function($value) use ($class) {
    invariantPrimitive(is_a($value, $class), "instance of `{$class}`", $value);
  };
});

PropTypes::register('oneOf', function (array $enumValues = []) {
  return function($value) use ($enumValues) {
    static $joinedValues;

    if (!$joinedValues) { $joinedValues = implode(', ', array_map('json_encode', $enumValues)); }

    invariant(in_array($value, $enumValues, true), "expecting one of {$joinedValues}, but ".json_encode($value)." was given");
  };
});

PropTypes::register('oneOfType', function (array $types = []) {
  return function($value) use ($types) {
    static $joinedValues;

    if (!$joinedValues) { $joinedValues = 'one of '.implode(', ', $types).''; }

    invariant(
      array_reduce(
        array_map(function(TypeInterface $type) use ($value) {
          try {
            $type->assert($value);
            return true;
          } catch(\Throwable $th) {
            return false;
          }
        }, $types),
        function ($previous, $current) {
          return $previous || $current;
        },
        false
      ),
      "expecting {$joinedValues}, but ".gettype($value)." was given"
    );
  };
});

PropTypes::register('arrayOf', function (TypeInterface $type) {
  return function($value) use ($type) {

    invariantPrimitive(is_array($value), 'array', $value);

    try {
      foreach ($value as $item) { $type->assert($item); }
    } catch (\Throwable $th) {
      $message = preg_replace('/expecting (.+) but/', 'expecting array of $1s but', $th->getMessage());
      throw new \Exception($message, 400);
    }
  };
});

PropTypes::register('objectOf', function (TypeInterface $type) {
  return function($value) use ($type) {
    invariantPrimitive(is_object($value), 'object', $value);

    $value = (array) $value;

    try {
      foreach ($value as $item) { $type->assert($item); }
    } catch (\Throwable $th) {
      $message = preg_replace('/expecting (.+) but/', 'expecting object of $1s but', $th->getMessage());
      throw new \Exception($message, 400);
    }
  };
});

PropTypes::register('shape', function (array $shape) {
  return new Type('shape', $shape);
});

PropTypes::register('exact', function (array $shape) {
  return (new Type('exact', $shape))->isExact;
});

PropTypes::register('any', function () {
  return function($value) {};
});
