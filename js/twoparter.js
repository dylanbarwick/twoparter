(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.twoparter = {
    attach: function (context, settings) {
      ////////////////////////////////////////

      $('.views-field-field-start-time-2 time').each(function() {
        let thisStartTime2 = $(this).text();
        // Javascript timestamp in seconds.
        let timestamp = Math.floor(new Date().getTime() / 1000);
        if (thisStartTime2 <= timestamp) {
          // Show the snippet2 etc.
          $(this).parents('.views-row').find('.views-field-field-snippet-2, .views-field-field-supporting-text-2').show();
        }
        else {
          // Show the snippet1 etc.
          $(this).parents('.views-row').find('.views-field-field-snippet-1, .views-field-field-supporting-text-1').show();
        }
      });

      ////////////////////////////////////////
    }
  };

}(jQuery, Drupal));
