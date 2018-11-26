<?php

namespace Drupal\highcharts_render\Highcharts;

class Highchart implements \ArrayAccess {

  //The chart type.
  //A regullar higchart
  const HIGHCHART = 0;

  //A highstock chart
  const HIGHSTOCK = 1;

  // A Highchart map
  const HIGHMAPS = 2;

  //The js engine to use
  const ENGINE_JQUERY = 10;

  const ENGINE_MOOTOOLS = 11;

  const ENGINE_PROTOTYPE = 12;

  /**
   * The chart options
   *
   * @var array
   */
  protected $_options = [];

  /**
   * The chart type.
   * Either self::HIGHCHART or self::HIGHSTOCK
   *
   * @var int
   */
  protected $_chartType;

  /**
   * The javascript library to use.
   * One of ENGINE_JQUERY, ENGINE_MOOTOOLS or ENGINE_PROTOTYPE
   *
   * @var int
   */
  protected $_jsEngine;

  /**
   * Array with keys from extra scripts to be included
   *
   * @var array
   */
  protected $_extraScripts = [];

  /**
   * Clone Highchart object
   */
  public function __clone() {
    foreach ($this->_options as $key => $value) {
      $this->_options[$key] = clone $value;
    }
  }

  /**
   * The Highchart constructor
   *
   * @param int $chartType The chart type (Either self::HIGHCHART or
   *   self::HIGHSTOCK)
   * @param int $jsEngine The javascript library to use
   *                       (One of ENGINE_JQUERY, ENGINE_MOOTOOLS or
   *   ENGINE_PROTOTYPE)
   */
  public function __construct($chartType = self::HIGHCHART, $jsEngine = self::ENGINE_JQUERY) {
    $this->_chartType = is_null($chartType) ? self::HIGHCHART : $chartType;
    $this->_jsEngine = is_null($jsEngine) ? self::ENGINE_JQUERY : $jsEngine;
  }

  /**
   * Serialize the chart object passed to HighChart API into a javascript
   * string to be #attachable to Drupal render object.
   *
   * @return string The hightchart object serialized.
   */
  public function serialize($debug = FALSE) {
    return HighchartOptionRenderer::render($this->_options, $debug);
  }

  /**
   * Returns the Chart type as per required in javascript HighChart API.
   * @return string
   */
  public function getChartType() {
    if ($this->_chartType === self::HIGHCHART) {
      return 'Chart';
    }
    elseif ($this->_chartType === self::HIGHMAPS) {
      return 'Map';
    }
    else {
      return 'Stock';
    }
  }

  /**
   * Manualy add an extra script key.
   *
   * @param array $keys extra scripts keys to be included
   */
  public function addExtraScript($key) {
    if ($key) {
      $this->addExtraScripts([$key]);
    }
  }

  /**
   * Manualy add extra scripts.
   *
   * @param array $keys extra scripts keys to be included
   */
  public function addExtraScripts(array $keys = []) {
    $this->_extraScripts = array_merge($this->_extraScripts, $keys);
  }


  /**
   * Get extra scripts keys.
   *
   * @param array $keys extra scripts keys to be included
   */
  public function getExtraScripts(array $keys = []) {
    return $this->_extraScripts ? $this->_extraScripts : [];
  }

  /**
   * Global options that don't apply to each chart like lang and global
   * must be set using the Highcharts.setOptions javascript method.
   * This method receives a set of HighchartOption and returns the
   * javascript string needed to set those options globally
   *
   * @param HighchartOption The options to create
   *
   * @return string The javascript needed to set the global options
   */
  public static function setOptions($options) {
    //TODO: Check encoding errors
    $option = json_encode($options->getValue());
    return "Highcharts.setOptions($option);";
  }

  public function __set($offset, $value) {
    $this->offsetSet($offset, $value);
  }

  public function __get($offset) {
    return $this->offsetGet($offset);
  }

  public function offsetSet($offset, $value) {
    $this->_options[$offset] = new HighchartOption($value);
  }

  public function offsetExists($offset) {
    return isset($this->_options[$offset]);
  }

  public function offsetUnset($offset) {
    unset($this->_options[$offset]);
  }

  public function offsetGet($offset) {
    if (!isset($this->_options[$offset])) {
      $this->_options[$offset] = new HighchartOption();
    }
    return $this->_options[$offset];
  }

  // Helper methods.

  /**
   * Activate 3D chart.
   */
  public function activate3D() {
    $this->addExtraScript('3d');
    $this->chart->options3d = [
      'enabled' => true,
      'alpha' => 45,
      'beta' => 0
    ];
    $this->plotOptions->pie->allowPointSelect = true;
    $this->plotOptions->pie->cursor = 'pointer';
    $this->plotOptions->pie->depth = 35;
  }

  /**
   * Set a centered title.
   */
  public function setTitle($title) {
    $this->title->text = $title;
    $this->title->align = 'center';
    $this->title->verticalAlign = 'middle';
  }

  /**
   * Add another serie in the chart.
   * @param $data
   * @param null $title
   * @param array $options
   */
  public function setSerie($data, $title = NULL, $options =[]) {
    $serie = [
      'type' => $this->chart->type->getValue(),
      'data' => $data,
    ];
    if ($title) {
      $serie['name'] = $title;
    }
    foreach ($options as $key => $value) {
      $serie[$key] = $value;
    }
    $this->series[] = $serie;
  }

  /**
   * Add another serie in the chart.
   * @param $data
   * @param null $title
   * @param array $options
   */
  public function setDrilldown($data, $id, $title = NULL, $options =[]) {
    $this->addExtraScript('drilldown');
    $serie = [
      'type' => $this->chart->type->getValue(),
      'data' => $data,
    ];
    if ($title) {
      $serie['name'] = $title;
      $serie['id'] = $id;
    }
    foreach ($options as $key => $value) {
      $serie[$key] = $value;
    }
    $this->drilldown->series[] = $serie;
  }
}
