<?php

namespace PropTypes;

abstract class AbstractType implements TypeInterface {
  public function __get($property) {
    switch ($property) {
      case 'null':
      case 'nullable':
      case 'isNullable':
        return new NullableType($this);
      break;

      case 'requre':
      case 'required':
      case 'isRequired':
        return new RequiredType($this);
      break;

      case 'exact':
      case 'isExact':
        return new ExactType($this);
      break;

      default:
        if (preg_match(SWITCH_PROPERTY_REGEX, $property)) {
          throw new \Exception("`${property}` switch is not implemented", 500);
        }
      break;
    }
  }

  public function isExact(): bool { return false; }

  public function isNullable(): bool { return false; }

  public function isRequired(): bool { return false; }

  public function isShape(): bool { return false; }
}