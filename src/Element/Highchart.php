<?php

namespace Drupal\highcharts\Element;

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
class Highchart extends RenderElement {

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
      '#theme' => 'highchart',
      '#theme_wrappers' => ['form_element'],
      '#attached' => [
        'library' => ['highcharts/highchart'],
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
    $element['#attributes']['type'] = 'highchart';
    $element['#attributes']['class'][] = 'highchart';
    $element['#attributes']['class'][] = Html::getUniqueId('highchart');

    if ($element['#value']) {
      $chart = $element['#value'];
      dsm($chart->render("chart"));
    }
    return $element;
  }
}
