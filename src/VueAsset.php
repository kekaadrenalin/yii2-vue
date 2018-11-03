<?php

namespace kekaadrenalin\vue;

use yii\web\AssetBundle;

/**
 * Class VueAsset
 *
 * Registers Vue.js
 *
 * @link    https://github.com/kekaadrenalin/yii2-vue
 * @author  kekaadrenalin <kekapor@outlook.com>
 * @license BSD-3-Clause
 * @package kekaadrenalin\vue
 */
class VueAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/vue/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        YII_ENV_DEV ? 'vue.js' : 'vue.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}