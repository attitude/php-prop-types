<?php

use PropTypes\TypeInterface;

function assertShapeInterface (TypeInterface $type): void {
  if (!$type->isShape()) {
    throw new \Exception("Type `{$type}` is not a shape", 400);
  }
}
