<?php

namespace PropTypes;

class NullableType extends AbstractTypeWrapper {
  protected $type;

  public function __construct(TypeInterface $type) {
    if ($type instanceof NullableType) {
      $this->type = $type->unpack();
    } else {
      $this->type = $type;
    }
  }

  public function __toString(): string {
    return '?'.$this->type->__toString();
  }

  public function assert ($value): void {
    if (empty($value) && $value !== 0) {
      return;
    } else {
      $this->type->assert($value);
    }
  }

  public function isNullable(): bool {
    return true;
  }
}
