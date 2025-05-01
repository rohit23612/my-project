<?php

/**
 * Returns an object out of an array.
 * Note that only fields corresponding to the array keys will be returned unless you pass $add_not_existing_properties to true.
 * @param array $array
 * @param object|string $object
 * @param bool $add_not_existing_properties
 * @return object
 */
function array_to_object($array, $object, $add_not_existing_properties = false) {
    return json_to_object(json_encode($array), $object, $add_not_existing_properties);
}

/**
 * Returns an object out of a json string.
 * Note that only fields corresponding to the json keys will be returned  unless you pass $add_not_existing_properties to true.
 * @param string $json
 * @param object|string $object
 * @param bool $add_not_existing_properties
 * @return object
 */
function json_to_object($json, $object, $add_not_existing_properties = false) {
    $data = json_decode($json, true);

    $class = $object;

    if (is_object($object))
        $class = get_class($object);

    $object = new $class();

    foreach ($data as $key => $value) {
        if (!(property_exists($object, $key) || $add_not_existing_properties))
            continue;

        $object->{$key} = $value;
    }

    return $object;
}

/**
 * Build a new array out of the $options one with defaults values taken
 * from $defaults when nothing is found.
 * @param array $options
 * @param array $defaults
 * @return array
 */
function parse_args($options, $defaults) {
    if (is_null($options)) $options = [];
    elseif (is_object($options)) $options = (array)$options;
    elseif (!is_array($options)) $options = [];

    if (is_null($defaults)) $defaults = [];
    elseif (is_object($defaults)) $defaults = (array)$defaults;
    elseif (!is_array($defaults)) $defaults = [];

    foreach ($defaults as $k => $v)
        if (is_null($v))
            unset($defaults[$k]);

    foreach ($options as $k => $v)
        if (isset($defaults[$k]))
            if (is_null($v))
                unset($options[$k]);
            elseif (is_string($v) && ($v === '') && isset($defaults[$k]) && is_array($defaults[$k]))
                unset($options[$k]);
            else {
                if (is_array($v)) {
                    $recursiveDefaults = $defaults[$k];
                    $options[$k] = parse_args($v, $recursiveDefaults);
                }

                unset($defaults[$k]);
            }

    foreach ($defaults as $k => $v)
        $options[$k] = $v;

    return $options;
}

/**
 * Checks if the provided value is empty and, if it is, the default
 * value will be returned. The value itself will be returned otherwise.
 * @param mixed $value
 * @param mixed $default
 * @return mixed
 */
function get($value, $default = "") {
    return empty($value) ? $default : $value;
}

/**
 * Disposes any item.
 * @param mixed $item
 */
function dispose(&$item) {
    unset($item);
    $item = null;
}

/**
 * Returns true if the script is running in the cli or not.
 * @return bool
 */
function is_cli() {
    return php_sapi_name() === 'cli';
}
