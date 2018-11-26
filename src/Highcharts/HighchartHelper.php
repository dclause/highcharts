<?php

namespace Drupal\highcharts_render\Highcharts;

class HighchartHelper {

  static function generateHighChart($type, $title = "", $options = []) {
    $highchart = new Highchart();
    $highchart->setTitle($title);
    $highchart->credits->enabled = FALSE;
    $highchart->chart->type = $type;
    $highchart->plotOptions->$type = [
      //'slicedOffset' => 25,
      'allowPointSelect' => true,
      'cursor' => 'pointer',
      'dataLabels' => [
        'enabled' => TRUE,
        'distance' => 10,
        'style' => [
          'fontWeight' => 'bold',
        ],
      ],
    ];
    return $highchart;
  }

  static function generateHighChartPie($title = NULL, $options = []) {
    $highchart = HighchartHelper::generateHighChart('pie', $title, $options);
    return $highchart;
  }
}
