<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MasterChart Object, Parent to all Charts
 *
 * Has common properties between all the different charts.
 *
 *
 * NOTICE OF LICENSE
 *
 * This file is part of CodeIgniter gCharts.
 * CodeIgniter gCharts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CodeIgniter gCharts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CodeIgniter gCharts.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts Github Page
 * @license http://www.gnu.org/licenses/gpl.html GPL-V3
 *
 */

class Chart
{
    var $chartType = NULL;
    var $chartLabel = NULL;
    var $dataTable = NULL;

    var $data = NULL;
    var $options = NULL;
    var $defaults = NULL;
    var $events = NULL;
    var $elementID = NULL;

    public function __construct($chartLabel)
    {
        $this->chartType = get_class($this);
        $this->chartLabel = $chartLabel;
        $this->options = array();
        $this->defaults = array(
            'backgroundColor',
            'chartArea',
            'colors',
            'events',
            'fontSize',
            'fontName',
            'height',
            'legend',
            'title',
            'titlePosition',
            'titleTextStyle',
            'tooltip',
            'width'
        );
    }

    /**
     * Sets configuration options from array of values
     *
     * You can set the options all at once instead of passing them individually
     * or chaining the functions from the chart objects.
     *
     * @param array $options
     * @return \Chart
     */
    public function setConfig($options = array())
    {
        if(is_array($options) && count($options) > 0)
        {
            foreach($options as $option => $value)
            {
                if(in_array($option, $this->defaults))
                {
                    if(method_exists($this, $option))
                    {
                        $this->$option($value);
                    } else {
                        $this->addOption($value);
                    }
                } else {
                    $this->error('Invalid config value, must be type (array) containing any key '.array_string($this->defaults));
                }
            }
        }

        return $this;
    }

    /**
     * Adds the error message to the error log in the gcharts master object.
     *
     * @param string $msg
     */
    private function error($msg)
    {
        Gcharts::_set_error($this->chartType, $msg);
    }

    /**
     * Sets a configuration option
     *
     * Takes either an array with option => value, or an object created by
     * one of the configOptions child objects.
     *
     * @param mixed $option
     * @return \Chart
     */
    public function addOption($option)
    {
        if(is_object($option))
        {
            $this->options = array_merge($this->options, $option->toArray());
        }

        if(is_array($option))
        {
            $this->options = array_merge($this->options, $option);
        }

        return $this;
    }

    /**
     * Assigns wich DataTable will be used for this LineChart. If a label is provided
     * then the defined DataTable will be used. If called with no argument, it will
     * attempt to use a DataTable with the same label as the LineChart
     *
     * @param mixed dataTableLabel String label or DataTable object
     * @return \configs\DataTable DataTable object
     */
    public function dataTable($data = NULL)
    {
        switch(gettype($data))
        {
            case 'object':
                if(get_class($data) == 'DataTable')
                {
                    $this->data = $data;
                    $this->dataTable = 'local';
                } else {
                    Gcharts::_set_error(get_class($this), 'Invalid dataTable object, must be type (DataTable).');
                }
            break;

            case 'string':
                if($data != '')
                {
                    $this->dataTable = $data;
                } else {
                    Gcharts::_set_error(get_class($this), 'Invalid dataTable label, must be type (string) non-empty.');
                }
            break;

            default:
                $this->dataTable = $this->chartLabel;
            break;
        }

        return $this;
    }

    /**
     * An object with members to configure the placement and size of the chart area
     * (where the chart itself is drawn, excluding axis and legends).
     * Two formats are supported: a number, or a number followed by %.
     * A simple number is a value in pixels; a number followed by % is a percentage.
     *
     * @param \configs\chartArea $chartArea
     * @return \Chart
     */
    public function chartArea(chartArea $chartArea)
    {
        if(is_a($chartArea, 'chartArea'))
        {
            $this->addOption($chartArea->toArray());
        } else {
            $this->error('Invalid chartArea, must be an object type (chartArea).');
        }

        return $this;
    }

    /**
     * The colors to use for the chart elements. An array of strings, where each
     * element is an HTML color string, for example: colors:['red','#004411'].
     *
     * @param array $colorArray
     * @return \Chart
     */
    public function colors($colorArray)
    {
        if(is_array($colorArray))
        {
            $this->addOption(array('colors' => $colorArray));
        } else {
            $this->error('Invalid colors, must be (array) with valid HTML colors');
        }

        return $this;
    }

    /**
     * Register javascript callbacks for specific events. Valid values include
     * [ animationfinish | error | onmouseover | onmouseout | ready | select ]
     * associated to a respective pre-defined javascript function as the callback.
     *
     * @param array $events Array of events associated to a callback
     * @return \Chart
     */
    public function events($events)
    {
        $values = array(
            'animationfinish',
            'error',
            'onmouseover',
            'onmouseout',
            'ready',
            'select'
        );

        if(is_array($events))
        {
            foreach($events as $event)
            {
                if(in_array($event, $values))
                {
                    $this->events[] = $event;
                } else {
                    $this->error('Invalid events array key value, must be (string) with any key '.array_string($values));
                }
            }
        } else {
            $this->error('Invalid events type, must be (array) containing any key '.array_string($values));
        }

        return $this;
    }

    /**
     * The default font size, in pixels, of all text in the chart. You can
     * override this using properties for specific chart elements.
     *
     * @param int $fontSize
     * @return \Chart
     */
    public function fontSize($fontSize)
    {
        if(is_int($fontSize))
        {
            $this->addOption(array('fontSize' => $fontSize));
        } else {
            $this->error('Invalid value for fontSize, must be type (int).');
        }

        return $this;
    }

    /**
     * The default font face for all text in the chart. You can override this
     * using properties for specific chart elements.
     *
     * @param string $fontName
     * @return \Chart
     */
    public function fontName($fontName)
    {
        if(is_string($fontName))
        {
            $this->addOption(array('fontSize' => $fontName));
        } else {
            $this->error('Invalid value for fontName, must be type (string).');
        }

        return $this;
    }

    /**
     * Height of the chart, in pixels.
     *
     * @param int $height
     * @return \Chart
     */
    public function height($height)
    {
        if(is_int($height))
        {
            $this->addOption(array('height' => $height));
        } else {
            $this->error('Invalid height, must be (int)');
        }

        return $this;
    }

    /**
     * An object with members to configure various aspects of the legend. To
     * specify properties of this object, create a new legend() object, set the
     * values then pass it to this function or to the constructor.
     *
     * @param legend $legendObj
     * @return \AreaChart
     */
    public function legend($legendObj)
    {
        if(is_a($legendObj, 'legend'))
        {
            $this->addOption($legendObj->toArray());
        } else {
            $this->error('Invalid value for legend, must be an object type (legend).');
        }

        return $this;
    }

    /**
     * Text to display above the chart.
     *
     * @param string $title
     * @return \Chart
     */
    public function title($title)
    {
        if(is_string($title))
        {
            $this->addOption(array('title' => (string) $title));
        } else {
            $this->error('Invalid title, must be type (string).');
        }

        return $this;
    }

    /**
     * Where to place the chart title, compared to the chart area. Supported values:
     * 'in' - Draw the title inside the chart area.
     * 'out' - Draw the title outside the chart area.
     * 'none' - Omit the title.
     *
     * @param string $position
     * @return \Chart
     */
    public function titlePosition($position)
    {
        $values = array(
            'in',
            'out',
            'none'
        );

        if(in_array($position, $values))
        {
            $this->addOption(array('titlePosition' => $position));
        } else {
            $this->error('Invalid axisTitlesPosition, must be type (string) with a value of '.array_string($values));
        }

        return $this;
    }

    /**
     * An object that specifies the title text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param \configs\textStyle $textStyleObj
     * @return \Chart
     */
    public function titleTextStyle(textStyle $textStyleObj)
    {
        if(is_a($textStyleObj, 'textStyle'))
        {
            $this->addOption(array('titleTextStyle' => $textStyleObj->values()));
        } else {
            $this->error('Invalid titleTextStyle, must be an object type (textStyle).');
        }

        return $this;
    }


    /**
     * An object with members to configure various tooltip elements. To specify
     * properties of this object, create a new tooltip() object, set the values
     * then pass it to this function or to the constructor.
     *
     * @param \configs\tooltip $tooltipObj
     * @return \Chart
     */
    public function tooltip($tooltipObj)
    {
        if(is_a($tooltipObj, 'tooltip'))
        {
            $this->addOption($tooltipObj->toArray());
        } else {
            $this->error('Invalid tooltip, must be an object type (tooltip).');
        }

        return $this;
    }

    /**
     * Width of the chart, in pixels.
     *
     * @param int $width
     * @return \Chart
     */
    public function width($width)
    {
        if(is_int($width))
        {
            $this->addOption(array('width' => $width));
        } else {
            $this->error('Invalid width, must be type (int).');
        }

        return $this;
    }

    /**
     * Outputs the chart javascript into the page
     *
     * Pass in a string of the html elementID that you want the chart to be
     * rendered into. Plus, if the dataTable function was never called on the
     * chart to assign a DataTable to use, it will automatically attempt to use
     * a DataTable with the same label as the chart.
     *
     * @param string $elementID
     * @return string Javscript code blocks
     */
    public function outputInto($elementID = NULL)
    {
        if($this->dataTable === NULL)
        {
            $this->dataTable = $this->chartLabel;
        }

        if(gettype($elementID) == 'string' && $elementID != NULL)
        {
            $this->elementID = $elementID;
        }

        return Gcharts::_build_script_block($this);
    }

}

/* End of file LineChart.php */
/* Location: ./gcharts/charts/LineChart.php */