<?php

namespace Drupal\highcharts_render\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element for rendering highcharts.
 *
 * Usage example:
 * @code
 * $form['chart'] = [
 *   '#type' => 'highchart',
 *   '#value' => $highchart, // a Highchart class element
 * ];
 * @endcode
 *
 * @RenderElement("highchart")
 */
class HighchartElement extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#pre_render' => [
        [$class, 'preRenderHighChart'],
      ],
      '#value' => NULL,
      '#debug' => FALSE,
      '#theme' => 'highchart',
      '#theme_wrappers' => ['form_element'],
      '#attached' => [
        'library' => ['highcharts_render/highchart'],
      ],
    ];
  }

  /**
   * Prepares a #type 'highchart' render element for highchart.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *
   * @return array
   *   The $element with prepared variables ready for highchart.html.twig.
   */
  public static function preRenderHighChart($element) {
    $id = Html::getUniqueId('highchart');
    $element['#attributes']['type'] = 'highchart';
    $element['#attributes']['class'][] = 'highchart';
    $element['#attributes']['id'] = $id;
    //$element['#attributes']['style'] = 'width:200px; height:150px';

    /** @var \Drupal\highcharts_render\Highcharts\Highchart $chart */
    if ($chart = $element['#value']) {
      $data = $chart->serialize($element['#debug']);
      if ($element['#debug']) print($data);
      $element['#attached']['drupalSettings']['highchartsRender'][$chart->getChartType()]["#$id"] = $data;
      $element['#attached']['library'][] = 'highcharts_render/highchart.' . strtolower($chart->getChartType());
      foreach ($chart->getExtraScripts() as $key) {
        $element['#attached']['library'][] = 'highcharts_render/highchart.' . $key;
      }
    }
    return $element;
  }
}
