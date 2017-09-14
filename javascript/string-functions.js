/**
 * @file string-functions.js
 */
(function ($) {
  'use strict';

  /**
   * Convert a string to sentence case
   * @method
   * @param  {string} stc sentence to convert (stc)
   * @return {string}     converted string
   */
  var convertStringToSentenceCase = function (stc) {
    // Variable declaration
    var returnString = '';

    // Lowercase the entire string.
    stc = stc.toLowerCase();

    // Split the sentances into arrays.
    stc = stc.split('.');

    // Loop through the broken up sentances and upper case the first letter.
    if (stc.length > 0) {
      for (var x in stc) {
        stc[x] = stc[x].trim();
        if (stc[x] !== '') {
          var firstLetter = stc[x][0];
          var newString = firstLetter.toUpperCase() + stc[x].substring(1);
          returnString = (returnString === '') ? newString + '.' : ' ' + newString + '.';
        }
      }
    }

    return returnString;
  };

  let generateToMachineName = function (name) {
    name = name.trim();
    name = name.toLowerCase();
    name = name.replace(/[^a-zA-Z\s]/g, '');
    name = name.replace(/[\s]/g, '-');
    name = name.replace(/-{2,}/g, '-');

    return name;
  };

})(jQuery);
