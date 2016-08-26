/**
 * @file cookies.js
 */

(function ($) {

/**
 *  Get the URL variable
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

/**
 *  Check to make sure that cookies are enabled.
 */
function cookiesEnabled() {
  // Set the test cookie
  document.cookie = "testcookie";

  // Check to see if the cookie was set, return true/false
  var cookieEnabled = (document.cookie.indexOf("testcookie") != -1) ? true : false;

  // Clear the cookie
  document.cookie = "testcookie=; expires=Thu, 01 Jan 1970 00:00:00 UTC";

  // Return the results
  return (cookieEnabled);
}

/*
 *  Check to see if the cookie has been set or not
 */
function cookieIsSet(ck) {
  return (document.cookie.indexOf(ck) != -1) ? true : false;
}

/**
 *  Set the cookie with the passed variables
 */
function setCookie(ck, ckval) {

  if (cookiesEnabled() && !cookieIsSet(ck)) {
    // Set the cookie specified
    document.cookie(ck+'='+ckval);
  }
}

})(jQuery);
