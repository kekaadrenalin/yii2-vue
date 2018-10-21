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
class VuexAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/vuex/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        'https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js',
        YII_ENV_DEV ? 'vuex.js' : 'vuex.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'kekaadrenalin\vue\VueAsset',
    ];
}