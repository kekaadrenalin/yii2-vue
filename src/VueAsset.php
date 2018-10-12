<?php

namespace kekaadrenalin\vue;

use yii\web\AssetBundle;

/**
 * Class VueAsset
 *
 * Registers Vue.js
 *
 * @link    https://github.com/antkaz/yii2-vue
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
        YII_ENV_DEV ? 'vuex.js' : 'vuex.min.js',
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