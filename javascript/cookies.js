/**
 * @file cookies.js
 */

(function ($) {

/**
 * Check to make sure that cookies are enabled.
 * @method cookiesEnabled
 * @return {boolean}       True/false based on if cookies are able to be
 *                         saved or not.
 */
function cookiesEnabled() {
  // Set the test cookie
  document.cookie = "testcookie";

  // Check to see if the cookie was set, return true/false
  var cookieEnabled = (document.cookie.indexOf("testcookie") != -1) ? true : false;

  // Clear the cookie
  document.cookie = "testcookie=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";

  // Return the results
  return cookieEnabled;
}

/**
 * Check to see if the cookie has been set or not
 * @method cookieIsSet
 * @param  {string}    ck cookie name
 * @return {boolean}      returns if the cookie is already set or not
 */
function cookieIsSet(ck) {
  return (document.cookie.indexOf(ck) != -1) ? true : false;
}

/**
 * Set the cookie with the passed variables
 * @method setCookie
 * @param  {string}  ck    cookie name
 * @param  {string}  ckval value to save under the cookie
 */
function setCookie(ck, ckval) {

  if (cookiesEnabled() && !cookieIsSet(ck)) {
    // Set the cookie specified
    document.cookie = ck + '=' + ckval + '; path=/';
  }
}

})(jQuery);
