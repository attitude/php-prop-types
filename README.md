# React PropTypes implemented in PHP

> <small style="font-weight:bold">DEPRECATED NODICE:</small> This project is predecessor of <a href="https://github.com/attitude/duck-types-php" style="color: inherit;text-decoration:underline;font-weight:bold">Duck Types for PHP</a> — your asserts turn into readable and short one-liners with Flow-flavoured syntax annotations.

A lean implementation inspired by the [React Prop Types](https://reactjs.org/docs/typechecking-with-proptypes.html) library and [Flow](https://flow.org) done for PHP. All the types are overridable (displays warnings you can turn off) and you can even define/register your own types for better reuse (inspired by the Flow types).

All registered types are accessible as static methods of the `PropTypes` class.

## Install using composer:

```json
{
    "name": "you/example-project",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/attitude/php-prop-types"
        }
    ],
    "require": {
        "attitude/php-prop-types": "dev-main"
    }
}
```

## Use in your code

```php
<?php

// Require autoloader
require_once('vendor/autoload.php');

// Use the PropTypes container
use \PropTypes\PropTypes;

// Register your own type
PropTypes::register('SomeComponentType', PropTypes::exact([
  'names' => PropTypes::array()->isRequired,
  'bool' => PropTypes::bool(),
  'structure' => PropTypes::shape([
    'deep' => PropTypes::array()->isRequired,
    'enum' => PropTypes::oneOf(['1', 2, 'three']),
    'oneOf' => PropTypes::oneOfType([
      PropTypes::array(),
      PropTypes::string(),
      PropTypes::bool(),
    ])->isRequired,
    'arrayOf' => PropTypes::arrayOf(PropTypes::string())->isRequired,
    'objectOf' => PropTypes::objectOf(PropTypes::string())->isRequired,
    'requiredAny' => PropTypes::any()->isRequired,
    'instanceOf' => PropTypes::instanceOf('StdClass')->isRequired,
  ]),
]));

function SomeComponent($props) {
  // Check the props
  PropTypes::SomeComponentType()->assert($props);

  // rest of the component code...
}

// Run your code:
SomeComponent([
  'names' => ['Martin'],
  'bool' => true,
  'structure' => [
    'deep' => ['Adamko'],
    'enum' => 2,
    'oneOf' => '',
    'arrayOf' => ['2'],
    'objectOf' => (object)['2'],
    'requiredAny' => '',
    'instanceOf' => (object) [],
  ],
  'extraKey' => '123', // <<< This should throw an error
]);

```

## Options

Constant                          | type    | default | Description
----------------------------------|---------|---------|------------
`PROP_TYPES_STRICT_OBJECTS_SHAPE` | boolean | false   | Whether Shape accepts both `StdClass` and `array` or strictly just `StdClass` instance
`PROP_TYPES_WARNINGS_ENABLED`     | boolean | true    | Show or hide warnings

## API

- #### static method `PropTypes::register()`
  Used for registering a new type. See `/src/bootstrap.php` for more examples.
  ##### Arguments:
  - *string* `$typeName` - name for the type, e.g. `'Props'`
  - *callable | TypeInterface* `$typeFactoryOrType` - a `TypeInterface` of a factory function that returns a `TypeInterface`
- #### static method `PropTypes::__callStatic()`
  Overloading method used to get any registered type Some of the types require arguments. Consult the [React Prop Types](https://reactjs.org/docs/typechecking-with-proptypes.html) documentation.

  ```php
  // Get registered type instance:
  $type = `PropTypes::PreviouslyRegisteredTypeName()`;
  // Validates props variable against the registered type:
  $type->assert ($props);
  ```

---

Not implemented types:

- symbol
- node
- element
- elementType
