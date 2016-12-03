<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/w3.css',
    ];
    public $js = [
        'js/jquery.cookie.js',
        'js/easeljs-0.8.2.min.js',
        'js/createjs-2015.11.26.min.js',
        // 'js/jquery.min.js',
        'js/jquery-1.9.1.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
