<?php

namespace PropTypes;

interface TypeInterface {
  public function __toString(): string;
  public function assert ($value): void;
  public function isExact(): bool;
  public function isNullable(): bool;
  public function isRequired(): bool;
  public function isShape(): bool;
  public function unpack();
}
