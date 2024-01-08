<?php

namespace davidxu\chartjs;

use davidxu\chartjs\assets\ChartJsAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use Yii;

class Chart extends Widget
{
    public $mixed = false;
    public $title = '';

    /**
     * @var array the HTML attributes for the widget container tag.
     */
    public $options = [];
    /**
     * @var array the options for the underlying ChartJs JS plugin.
     *            Please refer to the corresponding ChartJs type plugin Web page for possible options.
     *            For example, [this page](http://www.chartjs.org/docs/#lineChart) shows
     *            how to use the "Line chart" plugin.
     */
    public $clientOptions = [];
    /**
     * @var array the datasets configuration options and data to display on the chart.
     *            See [its documentation](http://www.chartjs.org/docs/) for the different options.
     */
    public $renderData = [];
    public $labels = [];
    /**
     * @var string the type of chart to display. The possible options are:
     *             - "Line" : A line chart is a way of plotting data points on a line. Often, it is used to show trend data, and the
     *             comparison of two data sets.
     *             - "Bar" : A bar chart is a way of showing data as bars. It is sometimes used to show trend data, and the
     *             comparison of multiple data sets side by side.
     *             - "Radar" : A radar chart is a way of showing multiple data points and the variation between them. They are often
     *             useful for comparing the points of two or more different data sets
     *             - "PolarArea" : Polar area charts are similar to pie charts, but each segment has the same angle - the radius of
     *             the segment differs depending on the value. This type of chart is often useful when we want to show a comparison
     *             data similar to a pie chart, but also show a scale of values for context.
     *             - "Pie" : Pie charts are probably the most commonly used chart there are. They are divided into segments, the arc
     *             of each segment shows a the proportional value of each piece of data.
     *             - "Doughnut" : Doughnut charts are similar to pie charts, however they have the centre cut out, and are therefore
     *             shaped more like a doughnut than a pie!
     */
    public $type = 'line';
    /**
     * @var array the plugin objects allowing to assign custom callback functions to Chart events.
     *            See [plugins & events documentation]
     *            (http://www.chartjs.org/docs/latest/developers/plugins.html#plugin-core-api).
     */
    public $plugins = [];

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        $view = $this->getView();
        ChartJsAsset::register($view);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::tag('canvas', '', $this->options);
        $this->registerClientScript();
    }

    /**
     * Registers the required js files and script to initialize ChartJS plugin
     */
    protected function registerClientScript(): void
    {
        $id = $this->options['id'];
        $view = $this->getView();
        $clientOptions = $data = [];
        if ($this->title) {
            $clientOptions = [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $this->title,
                    ],
                ],
            ];
        }
        if ($this->mixed) {
            $data = [
                'labels' => $this->labels,
                'datasets' => $this->renderData,
            ];
        } else {
            $data = [
                'labels' => $this->labels,
                'datasets' => [
                    [
                        'data' => $this->renderData,
                        'borderWidth' => 1,
                    ],
                ],
            ];
        }

        $plugins = [
            'colors' => [
                'forceOverride' => true,
            ],
            'legend' => [
                'position' => 'top',
            ],
        ];
        $clientOptions = ArrayHelper::merge($this->clientOptions, $clientOptions);
        $config = Json::encode(
            [
                'type' => $this->type,
                'data' => $data ?: new JsExpression('{}'),
                'options' => $clientOptions ?: new JsExpression('{}'),
                'plugins' => ArrayHelper::merge($this->plugins, $plugins),
            ]
        );
        \Yii::info($config);
        $js = ";var chartJS_{$id} = new Chart($('#{$id}'),{$config});";
        $view->registerJs($js);
    }
}