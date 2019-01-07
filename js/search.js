(function ($, Drupal, window, document) {
  'use strict';

  // Example of Drupal behavior loaded.
  Drupal.behaviors.search = {
    attach: function (context, settings) {
      if (typeof context['location'] !== 'undefined') { // Only fire on document load.

        $('#cci-search-form .search-icon').on("click", function(){
          $('#cci-search-form').submit()
        });

      }
    }
  };

})(jQuery, Drupal, this, this.document);