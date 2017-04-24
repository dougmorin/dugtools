/**
 * @file url.js
 */

(function ($) {

/**
 * Get the URL variable
 * @method getUrlVar
 * @param  {string}             variable URL variable to search for
 * @return {string or boolean}           Either returns the found url var or
 *                                       false.
 */
function getUrlVar(variable) {
  // Get the lowercase version of the URL search string
  var vars = window.location.search.substring(1).toLowerCase().split("&");

  // Loop through the broken up url variables.
  for (var i = 0; i < vars.length; i++) {
    // Split each into their own array
    var pair = vars[i].split("=");

    // Check for the desired variable.  Exits the array upon completion.
    if (pair[0] == variable) return decodeURIComponent(pair[1]);
  }

  // If it's not found, then exit with a false flag.
  return false;
}

})(jQuery);
