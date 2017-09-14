<?php

/**
 * Collection of functions
 */

/**
 * Trims the last element of the array off if it's empty.
 * @method trimArray
 * @param  array    $array array to trim
 * @return array           returns the trimmed array
 */
function trimArray($array) {
  if (count($array) > 0 && $array[count($array)-1] == "") {
    array_pop($array);
  }
  return $array;
}
