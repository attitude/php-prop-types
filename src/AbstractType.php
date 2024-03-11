<?php

namespace PropTypes;

abstract class AbstractType implements TypeInterface {
  const SWITCH_PROPERTY_REGEX = '/^is[A-Z]\w+/';

  public function __get($property) {
    switch ($property) {
      case 'null':
      case 'nullable':
      case 'isNullable':
        return new NullableType($this);

      case 'require':
      case 'required':
      case 'isRequired':
        return new RequiredType($this);

      case 'exact':
      case 'isExact':
        return new ExactType($this);

      default:
        if (preg_match(self::SWITCH_PROPERTY_REGEX, $property)) {
          throw new \Exception("`{$property}` switch is not implemented", 500);
        }

        // if (property_exists($this, $property)
        throw new \Exception("Undefined property: `{$property}`", 500);
    }
  }

  public function isExact(): bool { return false; }

  public function isNullable(): bool { return false; }

  public function isRequired(): bool { return false; }

  public function isShape(): bool { return false; }
}
