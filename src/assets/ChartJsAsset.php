<?php

namespace davidxu\chartjs\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class ChartJsAsset extends AssetBundle
{
    public $sourcePath = "@npm/chart.js/dist/";

    public $js = [
        'chart.umd.js',
    ];

    public $css = [
    ];

    public $depends = [
        YiiAsset::class,
    ];
}