<?php

namespace PropTypes;

abstract class AbstractTypeWrapper extends AbstractType {
  public function __toString(): string {
    return $this->type->__toString();
  }

  public function assert ($value): void {
    $this->type->assert($value);
  }

  public function isExact(): bool {
    return $this->type->isExact();
  }

  public function isNullable(): bool {
    return $this->type->isNullable();
  }

  public function isShape(): bool {
    return $this->type->isShape();
  }

  public function isRequired(): bool {
    return $this->type->isRequired();
  }

  public function shape() {
    if (method_exists($this->type, 'shape')) {
      return $this->type->shape();
    } else {
      return $this->type->unpack();
    }
  }

  public function unpack(): TypeInterface {
    return $this->type;
  }
}