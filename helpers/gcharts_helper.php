<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Converts array to string
 *
 * Takes an array of values and ouputs them as a string between
 * brackets and separated by a pipe.
 *
 * @param array $defaultValues
 * @return string contains array values in readable form
 */
function array_string($defaultValues)
{
    $tmp = '[ ';

    foreach($defaultValues as $k => $v)
    {
        $tmp .= $v . ' | ';
    }

    return substr_replace($tmp, "", -2) . ']';
}

function array_is_multi($arr)
{
    $rv = array_filter($arr,'is_array');

    if(count($rv) > 0)
    {
        return TRUE;
    } else {
        return FALSE;
    }
}

function valid_int($val)
{
    if(is_int($val) === TRUE)
    {
        return (int) $val;
    } else if(is_string($val) === TRUE) {
        if(ctype_digit($val) === TRUE)
        {
            return (int) $val;
        }
    } else {
        throw new Exception('"'.$val.'" is an invalid value, must be (int) or (string) representing an int');
    }
}