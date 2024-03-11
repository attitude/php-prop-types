<?php

namespace PropTypes;

require_once 'vendor/autoload.php';

describe('PropTypes', function() {
  it('should register a new type', function() {
    PropTypes::register('arrayType', function () {
      return function($value) {
        invariantPrimitive(is_array($value), 'array', $value);
      };
    });

    $array = PropTypes::arrayType();
    expect($array)->toBeInstanceOf(Type::class);
  });

  it('should register a new type with multiple names', function() {
    PropTypes::register(['arrayType2', 'arr'], function () {
      return function($value) {
        invariantPrimitive(is_array($value), 'array', $value);
      };
    });

    $array = PropTypes::arr();
    expect($array)->toBeInstanceOf(Type::class);
  });

  it('should validate bool', function() {
    expect(PropTypes::bool()->assert(true))->toBeNull();
    expect(PropTypes::bool()->assert(false))->toBeNull();
    expect(function() {
      PropTypes::bool()->assert('true');
    })->toThrow('expecting boolean but string was given instead for type `bool`');
  });

  it('should validate boolean', function() {
    expect(PropTypes::boolean()->assert(true))->toBeNull();
    expect(PropTypes::boolean()->assert(false))->toBeNull();
    expect(function() {
      PropTypes::boolean()->assert('true');
    })->toThrow('expecting boolean but string was given instead for type `boolean`');
  });

  it('should validate function', function() {
    expect(PropTypes::func()->assert(function() {}))->toBeNull();
    expect(function() {
      PropTypes::func()->assert('function');
    })->toThrow('expecting callable but string was given instead for type `func`');
  });

  it('should validate int', function() {
    expect(PropTypes::int()->assert(1))->toBeNull();
    expect(function() {
      PropTypes::int()->assert('1');
    })->toThrow('expecting int but string was given instead for type `int`');
  });

  it('should validate float', function() {
    expect(PropTypes::float()->assert(1.1))->toBeNull();
    expect(function() {
      PropTypes::float()->assert('1.1');
    })->toThrow('expecting float but string was given instead for type `float`');
  });

  it('should validate number', function() {
    expect(PropTypes::number()->assert(1))->toBeNull();
    expect(PropTypes::number()->assert(1.1))->toBeNull();
    expect(function() {
      PropTypes::number()->assert('1');
    })->toThrow('expecting number but string was given instead for type `number`');
  });

  it('should validate object', function() {
    expect(PropTypes::object()->assert((object) []))->toBeNull();
    expect(function() {
      PropTypes::object()->assert('object');
    })->toThrow('expecting object but string was given instead for type `object`');
  });

  it('should validate string', function() {
    expect(PropTypes::string()->assert('string'))->toBeNull();
    expect(function() {
      PropTypes::string()->assert(1);
    })->toThrow('expecting string but integer was given instead for type `string`');
  });

  it('should validate instanceOf', function() {
    expect(PropTypes::instanceOf('StdClass')->assert(new \StdClass()))->toBeNull();
    expect(function() {
      PropTypes::instanceOf('StdClass')->assert(new \Exception());
    })->toThrow('expecting instance of `StdClass` but object was given instead for type `instanceOf`');
  });

  it('should validate literal', function() {
    expect(PropTypes::literal('literal')->assert('literal'))->toBeNull();
    expect(function() {
      PropTypes::literal('literal')->assert('not literal');
    })->toThrow('expecting literal `literal`, but "not literal" was given');
  });

  it('should validate oneOf', function() {
    expect(PropTypes::oneOf(['1', 2, 'three'])->assert(2))->toBeNull();
    expect(function() {
      PropTypes::oneOf(['1', 2, 'three'])->assert('2');
    })->toThrow('expecting one of "1", 2, "three", but "2" was given instead for type `oneOf`');
  });

  it('should validate oneOfType', function() {
    expect(PropTypes::oneOfType([
      PropTypes::array(),
      PropTypes::string(),
      PropTypes::bool(),
    ])->assert('string'))->toBeNull();
    expect(PropTypes::oneOfType([
      PropTypes::array(),
      PropTypes::string(),
      PropTypes::bool(),
    ])->assert(true))->toBeNull();
    expect(PropTypes::oneOfType([
      PropTypes::array(),
      PropTypes::string(),
      PropTypes::bool(),
    ])->assert(['array']))->toBeNull();
    expect(function() {
      PropTypes::oneOfType([
        PropTypes::array(),
        PropTypes::string(),
        PropTypes::bool(),
      ])->assert(1);
    })->toThrow('expecting one of array, string, bool, but integer was given instead for type `oneOfType`');
  });

  it('should validate nested shape', function() {
    $shape = PropTypes::shape([
      'name' => PropTypes::string(),
      'age' => PropTypes::number(),
      'address' => PropTypes::shape([
        'street' => PropTypes::string(),
        'city' => PropTypes::string(),
        'zip' => PropTypes::number(),
      ])
    ]);

    // Should not throw
    expect($shape->assert([
      'name' => 'John Doe',
      'age' => 30,
      'address' => [
        'street' => '123 Main St',
        'city' => 'Any Town',
        'zip' => 12345,
      ]
    ]))->toBeNull();
  });

  it('should throw when validating nested shape', function() {
    $shape = PropTypes::shape([
      'name' => PropTypes::string(),
      'age' => PropTypes::number(),
      'address' => PropTypes::shape([
        'street' => PropTypes::string(),
        'city' => PropTypes::string(),
        'zip' => PropTypes::number(),
      ])
    ]);

    expect(function() use ($shape) {
      $shape->assert([
        'name' => 'John Doe',
        'age' => 30,
        'address' => [
          'street' => '123 Main St',
          'city' => 'Any Town',
          'zip' => '12345',
        ]
      ]);
    })->toThrow('`address.zip`: expecting number but string was given instead for type `number`');
  });

  it('should throw when validating nested shape with missing property', function() {
    $shape = PropTypes::shape([
      'name' => PropTypes::string(),
      'age' => PropTypes::number(),
      'address' => PropTypes::shape([
        'street' => PropTypes::string(),
        'city' => PropTypes::string(),
        'zip' => PropTypes::number()->isRequired,
      ])
    ]);

    expect(function() use ($shape) {
      $shape->assert([
        'name' => 'John Doe',
        'age' => 30,
        'address' => [
          'street' => '123 Main St',
          'city' => 'Any Town',
        ]
      ]);
    })->toThrow('`address`: zip is required');
  });

  it('should throw when validating nested shape with invalid property', function() {
    $shape = PropTypes::shape([
      'name' => PropTypes::string(),
      'age' => PropTypes::number(),
      'address' => PropTypes::shape([
        'street' => PropTypes::string(),
        'city' => PropTypes::string(),
        'zip' => PropTypes::number(),
      ])
    ]);

    expect(function() use ($shape) {
      $shape->assert([
        'name' => 'John Doe',
        'age' => 30,
        'address' => [
          'street' => '123 Main St',
          'city' => 'Any Town',
          'zip' => '12345',
        ]
      ]);
    })->toThrow('`address.zip`: expecting number but string was given instead for type `number`');
  });

  it('should validate nested props exact shape', function() {
    $shape = PropTypes::exact([
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
    ]);

    expect(function() use ($shape) {
      $shape->assert([
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
        'extraKey' => '123',
      ]);
    })->toThrow('Unexpected extra keys [extraKey] were given for exact |exact| shape');

    expect($shape->parse([
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
    ]))->toEqual([
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
    ]);
  });
});
