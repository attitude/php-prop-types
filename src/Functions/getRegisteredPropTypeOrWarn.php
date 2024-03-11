<?php

use PropTypes\PropTypes;

function getRegisteredPropTypeOrWarn(string $name) {
  try {
    $type = PropTypes::__callStatic($name);
  } catch (\Throwable $th) {
    if ($th->getCode() === 404) {
      if (PROP_TYPES_DEBUG === true) {
        trigger_error($th->getMessage(), E_USER_NOTICE);
      }

      return;
    }
  }

  return $type;
}