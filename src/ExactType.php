<?php

namespace PropTypes;

class ExactType extends AbstractTypeWrapper {
  protected $keys;
  protected $type;

  public function __construct($__1, $__2 = null) {
    if ($__2 === null) {
      $type = &$__1;
      $this->__fromType($type);
    } else {
      $name = &$__1;
      $shape = &$__2;
      $this->__fromNameAndShape($name, $shape);
    }
  }

  private function __fromType($type) {
    $this->assertAbstractType($type);

    $this->type = $type;
    $shape = method_exists($type, 'shape') ? $type->shape() : $type->unpack();

    if (!is_array($shape)) {
      throw new \Exception("Cannot use non-shape type for exact shape", 500);
    }

    $this->keys = array_keys($shape);
  }

  private function __fromNameAndShape(string $name, array $shape) {
    $this->keys = array_keys($shape);
    $this->type = new Type($name, $shape);
  }

  public function __toString(): string {
    return '|'.$this->type->__toString().'|';
  }

  private function assertAbstractType(AbstractType $type) {}

  public function assert($value): void {
    $valueKeys = array_keys((array) $value);

    $extraKeys = array_diff($valueKeys, $this->keys);
    $missingKeys = array_diff($this->keys, $valueKeys);

    if (count($extraKeys) > 0) {
      throw new \Exception("Unexpected extra keys [".implode(', ', $extraKeys)."] were given for exact ".$this->__toString()." shape", 400);
    }

    if (count($missingKeys) > 0) {
      throw new \Exception("Missing keys [".implode(', ', $missingKeys)."] for exact ".$this->__toString()." shape", 400);
    }

    try {
      $this->type->assert($value);
    } catch (\Throwable $th) {
      throw new \Exception($th->getMessage()." for exact ".$this->__toString()." shape", $th->getCode());

    }
  }

  public function isExact(): bool {
    return true;
  }
}
