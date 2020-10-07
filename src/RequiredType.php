<?php

namespace PropTypes;

class RequiredType extends AbstractTypeWrapper {
  protected $type;

  public function __construct(TypeInterface $type) {
    if ($type instanceof RequiredType) {
      $this->type = $type->unpack();
    } else {
      $this->type = $type;
    }
  }

  public function isRequired(): bool {
    return true;
  }
}
