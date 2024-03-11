<?php

use PropTypes\ExactType;
use PropTypes\RequiredType;
use PropTypes\TypeInterface;

function assertDefaultPropsFromProps (array $defaultProps, TypeInterface $type) {
  $shape = method_exists($type, 'shape') ? call_user_func([$type, 'shape']) : $type->unpack();

  // Default props should not include props that are required to be present:
  $shape = array_filter($shape, function(TypeInterface $type) {
    return !($type instanceof RequiredType);
  });

  $defaultPropsType = new ExactType("defaultProps({$type})", $shape);
  $defaultPropsType->assert($defaultProps);

  return;
}
