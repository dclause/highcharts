(function ($) {
  'use strict';

  Drupal.behaviors.highcharts_token = {
    attach: function (context, settings) {
      var charts = drupalSettings.highchartsRender;

      // Loop over all charts.
      for(var type in charts) {

        if (charts.hasOwnProperty(type)) {

          // Loop over all chart types.
          var highcharts = charts[type];
          for (var selector in highcharts) {

            if (highcharts.hasOwnProperty(selector)) {
              var chart = JSON.parse(highcharts[selector]);
              $(selector).once('highchart').highcharts(type, chart);
            }
          }
        }
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
