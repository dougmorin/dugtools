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
let getUrlVar = function (urlvar) {
  // Get the lowercase version of the URL search string
  let vars = window.location.search.substring(1).toLowerCase().split('&');

  // Loop through the broken up url variables.
  for (var i = 0; i < vars.length; i++) {
    // Split each into their own array
    let pair = vars[i].split('=');

    // Check for the desired variable.  Exits the array upon completion.
    if (pair[0] === urlvar) {
      return decodeURIComponent(pair[1]);
    }
  }

  // If it's not found, then exit with a false flag.
  return false;
};

let setUrlVar = function (urlvar, urlval) {
  let url = window.location.origin + window.location.pathname;
  let found = false;

  let querystring = window.location.search;

  if (querystring === '') {
    querystring = [];
  }
  else {
    querystring = querystring.replace('?', '');
    querystring = querystring.split('&');
  }

  for (let i in querystring) {
    let qs = querystring[i].split('=');
    if (qs[0] === urlvar) {
      found = true;
      // Remove the entry if urlval is false
      if (!urlval) {
        querystring.splice(i, 1);
      }
      else {
        qs[1] = urlval;
        querystring[i] = qs.join('=');
      }
      break;
    }
  }

  if (!found && urlval) {
    querystring.push(urlvar + '=' + urlval);
  }

  if (querystring.length > 0) {
    querystring = (querystring.length > 1) ? querystring.join('&') : querystring[0];
    url += ('?' + querystring);
  }

  window.history.pushState({}, '', url);
};

})(jQuery);
