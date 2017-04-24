<?php

/**
 *
 * Notes:
 * I currently found a need to break apart a HEX value, modify the lumosity
 * value and then put it back together again.  I wrote it this way mostly
 * because I wanted to return an array in the following format
 *
 * array(
 *   'hex' => 'ffffff',
 *   'r' => '255',
 *   'g' => '255',
 *   'b' => '255',
 * );
 *
 * Feel free to convert it, pull it apart, use as-is.  I got the math from
 * http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/
 *
 * Call the modify_luminosity function like so:
 *   modify_luminosity('523583', 'lighten', '20')
 *   or
 *   modify_luminosity('523583', 'lighten', '.2')
 *
 */

/**
 * Modify the lumosity value of a given hex value
 * @method modify_luminosity
 * @param  string                          $hex         HEX value to convert
 * @param  string                          $type        'lighten' or 'darken'
 * @param  number (int or float)           $percentage  Percentage (20) or float (.2)
 * @return string                                       finalized hex value
 */
function modify_luminosity($hex, $type, $percentage) {
  // Converts the hex value to rgb
  $rgb = convert_hex_to_rgb($hex);

  // Convert the rgb values to hsl
  $hsl = convert_rgb_to_hsl($rgb);

  // Convert the percentage to a float
  if ($percentage > 0) {
    $percentage /= 100;
  }

  // Calculate the new luminosity based off of 'lighten' or 'darker'
  $hsl['l'] = ($type == 'lighten') ? $hsl['l'] + ($hsl['l'] * $percentage) : $hsl['l'] - ($hsl['l'] * $percentage);

  // Convert the HSL values back to RGB
  $rgb = convert_hsl_to_rgb($hsl);

  // Convert the RGB values to HEX
  $hex = convert_rgb_to_hex($rgb);

  // Return the new HEX value
  return $hex;
}

/**
 * This function takes a hex value and converts it to the rgb values
 * @method convert_hex_to_rgb
 * @param  string                           $hex hex value to process
 * @return array                                array of hex, r, g, b.
 */
function convert_hex_to_rgb($hex) {

  // Make sure the hex value comparing is a string
  $hex = (string) $hex;

  // If the hex value is null, or if it's not either 3 or 6 characters
  if ($hex == '' || $hex == NULL || !(strlen($hex) === 3 || strlen($hex) === 6)) {
    return array();
  }

  // Else, convert the hex string.
  else {

    // Double the hex string if it's only 3.
    if (strlen($hex) === 3) {
      $hex = $hex . $hex;
    }

    // Return the prepared array
    return array(
      'hex' => $hex,
      'r' => hexdec(substr($hex,0,2)),
      'g' => hexdec(substr($hex,2,2)),
      'b' => hexdec(substr($hex,4,2)),
    );
  }

  return array();
}

/**
 * Convert RGB values to HSL.
 * @method convert_rgb_to_hsl
 * @param  array                           $rgb array with rgb color values
 * @return array                                array containing the hsl values
 */
function convert_rgb_to_hsl($rgb) {

  $rgb['r'] = (float) $rgb['r'] / 255;
  $rgb['g'] = (float) $rgb['g'] / 255;
  $rgb['b'] = (float) $rgb['b'] / 255;

  // Calculate the max value
  $cmax = max($rgb['r'], $rgb['g'], $rgb['b']);

  // Calculate the min value
  $cmin = min($rgb['r'], $rgb['g'], $rgb['b']);

  // Calculate the luminosity.
  $luminosity = round((($cmax + $cmin) / 2) * 100);

  // Determine if we need to calculate Hue and Saturation
  if ($cmin === $cmax) {
    return array(
      'h' => 0,
      's' => 0,
      'l' => $luminosity,
    );
  }

  else {

    // If Luminance is smaller then 0.5, then saturation = (max - min) / (max + min)
    if ($luminosity <= 50) {
      $saturation = ($cmax - $cmin) / ($cmax + $cmin);
    }

    // Else, if Luminance is bigger then 0.5. then saturation = (max - min) / (2.0 - max - min)
    else {
      $saturation = ($cmax - $cmin) / (2.0 - $cmax - $cmin);
    }

    // Convert to a percentage.
    $saturation = $saturation * 100;

    // Calculate the hue if red is the largest color channel
    // Hue = (G - B) / (max - min)
    if ($rgb['r'] > $rgb['g'] && $rgb['r'] > $rgb['b']) {
      $hue = ($rgb['g'] - $rgb['b']) / ($cmax - $cmin);
    }

    // Calculate the hue if green is the largest color channel
    // Hue = 2.0 + (B - R) / (max - min)
    else if ($rgb['g'] > $rgb['r'] && $rgb['g'] > $rgb['b']) {
      $hue = 2.0 + ($rgb['b'] - $rgb['r']) / ($cmax - $cmin);
    }

    // Calculate the hue if blue is the largest color channel
    // Hue = 4.0 + (R - G) / (max - min)
    else {
      $hue = 4.0 + ($rgb['r'] - $rgb['g']) / ($cmax - $cmin);
    }

    // Times the hue by 60 to convert it to degrees.
    $hue = round($hue * 60);

    // If hue becomes negative, add 360 to bring it back into the degree scale.
    if ($hue < 0) {
      $hue += 360;
    }

    // Return the prepared array
    return array(
      'h' => $hue,
      's' => $saturation,
      'l' => $luminosity,
    );
  }

  return array();
}

/**
 * Converts HSL values to RGB
 * @method convert_hsl_to_rgb
 * @param  array                           $hsl HSL Values
 * @return array                                array containing RGB values
 */
function convert_hsl_to_rgb($hsl) {
  // Convert the luminosity and saturation to the percentage that we can process.
  if ($hsl['l'] > 0) {
    $hsl['l'] /= 100;
    $hsl['s'] /= 100;
  }

  // If there is no saturation, convert based off of the luminosity
  if ($hsl['s'] === 0) {

    return array(
      'r' => $hsl['l'] * 255,
      'g' => $hsl['l'] * 255,
      'b' => $hsl['l'] * 255,
    );
  }

  // Else, continue.
  else {

    // Temp color value #1
    $tmp = ($hsl['l'] <= 50) ? $hsl['l'] * (1.0 + $hsl['s']) : $hsl['l'] + $hsl['s'] - $hsl['l'] * $hsl['s'];

    // Temp color value #2
    $tmp2 = 2 * $hsl['l'] - $tmp;

    // Convert the Hue value to convert the 360 degrees into a circle
    $hsl['h'] = $hsl['h'] / 360;

    // Run the color channel tests on the RED value
    $rgb['r'] = $hsl['h'] + 0.333;
    $rgb['r'] = round(convert_tmp_rgb_color_test($rgb['r'], $tmp, $tmp2) * 255);

    // Run the color channel tests on the GREEN value
    $rgb['g'] = $hsl['h'];
    $rgb['g'] = round(convert_tmp_rgb_color_test($rgb['g'], $tmp, $tmp2) * 255);

    // Run the color channel tests on the BLUE value
    $rgb['b'] = $hsl['h'] - 0.333;
    $rgb['b'] = round(convert_tmp_rgb_color_test($rgb['b'], $tmp, $tmp2) * 255);

    return $rgb;
  }
}

/**
 * RGB conversion color tests
 * @method convert_tmp_rgb_color_test
 * @param  float                                   $rgbv [description]
 * @param  float                                   $t1   temp variable 1
 * @param  float                                   $t2   temp variable 2
 * @return float                                         returns the tested value.
 */
function convert_tmp_rgb_color_test($rgbv, $t1, $t2) {

  // If the rgbv value is negative, add 1 to get it between 0 - 1
  if ($rgbv < 0) {
    $rgbv += 1;
  }

  // if the rgbv value is over 1, then subtract 1 to get it between 0 - 1
  else if ($rgbv > 1) {
    $rgbv -= 1;
  }

  // Test #1: If 6 x temporary_R is smaller then 1
  if ((6 * $rgbv) <= 1) {
    return $t2 + ($t1 - $t2) * 6 * $rgbv;
  }

  else {

    // Test #2: If 2 x temporary_R is smaller then 1
    if ((2 * $rgbv <= 1)) {
      return $t1;
    }

    else {

      // Test #3: If 3 x temporary_R is smaller then 2
      if ((3 * $rgbv) <= 2) {
        return $t2 + ($t1 - $t2) * (0.66 - $rgbv) * 6;
      }

      else {
        // If you make it here, assign the color channel to the second temp value.
        return $t2;
      }
    }
  }
}

/**
 * Convert the RGB values to HEX format
 * @method convert_rgb_to_hex
 * @param  array                           $rgb array containing rgb values
 * @return string                               string containing the final
 *                                              hex value
 */
function convert_rgb_to_hex($rgb) {
  return sprintf("%02x%02x%02x", $rgb['r'], $rgb['g'], $rgb['b']);
}
