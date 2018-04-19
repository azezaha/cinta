<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TempleteAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/layout.css',
        'css/reset.css',
        'css/style.css',
    ];
    public $js = [
        'js/cufon-replace.js',
        'js/cufon-yui.js',
        'js/html5.js',
        'js/ie6_script_other.js',
        'js/jquery-1.4.2.js',
        'js/Myriad_Pro_600.font.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
