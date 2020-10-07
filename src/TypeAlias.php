<?php

namespace PropTypes;

class TypeAlias extends AbstractTypeWrapper {
  protected $name;
  protected $type;

  public function __construct(string $name, TypeInterface $type) {
    $this->name = $name;
    $this->type = $type instanceof TypeAlias ? $type->unpack() : $type;
  }

  public function __toString(): string {
    return $this->name;
  }

  public function assert($value): void {
    try {
      $this->type->assert($value);
    } catch (\Throwable $th) {
      throw new \Exception($th->getMessage()." for `{$this->name}`", $th->getCode());
    }
  }
}