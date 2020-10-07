<?php

namespace PropTypes;

define('SWITCH_PROPERTY_REGEX', '/^is[A-Z]\w+/');

class Type extends AbstractType {
  protected $isShape = false;
  protected $validation = null;

  public function __construct(string $name, $___2) {
    $this->name = $name;

    if ($___2 instanceof \Closure) {
      $this->validation = $___2;
    } elseif (is_array($___2)) {
      $this->__fromShape($___2);
    } else {
      throw new \Exception("Expecting \$validation to be instance of \Closure or array of items that implement TypeInterface", 599);
    }
  }

  private function __fromShape(array $shape) {
    foreach ($shape as $validation) {
      if (!$validation instanceof TypeInterface) {
        throw new \Exception("Expecting every item of \$shape array be instance of TypeInterface.", 599);
      }
    }

    $this->isShape = true;
    $this->validation = $shape;
  }

  public function __toString(): string {
    return $this->name;
  }

  public function assert($value):void {
    if ($this->validation instanceof \Closure) {
      try {
        $validation = $this->validation;
        $validation($value);
      } catch (\Throwable $th) {
        throw new \Exception($th->getMessage()." for `{$this->name}`", $th->getCode());
      }
    } else {
      $this->assertShape($value);
    }
  }

  private function assertShape($props): void {
    if (PROP_TYPES_STRICT_OBJECTS_SHAPE && !is_object($props)) {
      if (is_array($props)) {
        throw new \Exception("Expecting props to be an object, array given. Consider setting `PROP_TYPES_STRICT_OBJECTS_SHAPE` constant to `false`", 500);
      }

      throw new \Exception("Expecting props to be an object, got ".gettype($props)." given", 500);
    }

    if (!PROP_TYPES_STRICT_OBJECTS_SHAPE && !(is_array($props) || is_object($props))) {
      throw new \Exception("Expecting props to be an array, got ".gettype($props)." with value ".json_encode($props)." given", 500);
    }

    $props = (array) $props;

    foreach ($this->validation as $name => $validation) {
      if ($validation instanceof RequiredType && !array_key_exists($name, $props)) {
        throw new \Exception("${name} is required", 400);
      }

      if (array_key_exists($name, $props)) {
        $value = $props[$name];

        try {
          $validation->assert($value);
        } catch (\Throwable $th) {
          $message = lcfirst($th->getMessage());

          if ($message[0] === '`') {
            throw new \Exception("`${name}.".substr($message, 1), 400);
          } else {
            throw new \Exception("`${name}` ${message}", 400);
          }
        }
      }
    }
  }

  public function isShape(): bool {
    return $this->isShape;
  }

  public function unpack() {
    return $this->validation;
  }
}
