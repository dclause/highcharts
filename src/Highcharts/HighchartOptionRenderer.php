<?php

namespace Drupal\highcharts_render\Highcharts;

use Drupal\Core\StringTranslation\TranslatableMarkup;

class HighchartOptionRenderer {

  /**
   * Render the options and returns the javascript that
   * represents them
   *
   * @return string The javascript code
   */
  public static function render($options, $debug = FALSE) {
    $jsExpressions = [];
    //Replace any js expression with random strings so we can switch
    //them back after json_encode the options
    $options = static::_replaceJsExpr($options, $jsExpressions);

    //TODO: Check for encoding errors
    $result = $debug ? json_encode($options, JSON_PRETTY_PRINT) : json_encode($options);

    //Replace any js expression on the json_encoded string
    foreach ($jsExpressions as $key => $expr) {
      $result = str_replace('"' . $key . '"', $expr, $result);
    }
    return $result;
  }

  /**
   * Replaces any HighchartJsExpr for an id, and save the
   * js expression on the jsExpressions array
   * Based on Zend_Json
   *
   * @param mixed $data The data to analyze
   * @param array &$jsExpressions The array that will hold
   *                              information about the replaced
   *                              js expressions
   */
  private static function _replaceJsExpr($data, &$jsExpressions) {
    if (!is_array($data) && !is_object($data)) {
      return $data;
    }

    if (is_object($data)) {
      if ($data instanceof \stdClass) {
        return $data;
      }
      elseif ($data instanceof TranslatableMarkup) {
        $data = $data->render();
        return $data;
      }
      elseif (!$data instanceof HighchartJsExpr) {
        $data = $data->getValue();
      }
    }

    if ($data instanceof HighchartJsExpr) {
      $magicKey = "____" . count($jsExpressions) . "_" . count($jsExpressions);
      $jsExpressions[$magicKey] = $data->getExpression();
      return $magicKey;
    }

    foreach ($data as $key => $value) {
      $data[$key] = static::_replaceJsExpr($value, $jsExpressions);
    }
    return $data;
  }
}
