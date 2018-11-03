<?php

namespace kekaadrenalin\vue;

use yii\web\AssetBundle;

/**
 * Class VueAsset
 *
 * Registers Vuex.js
 *
 * @link    https://github.com/kekaadrenalin/yii2-vue
 * @author  kekaadrenalin <kekapor@outlook.com>
 * @license BSD-3-Clause
 * @package kekaadrenalin\vue
 */
class VuexAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower';

    /**
     * @inheritdoc
     */
    public $js = [
        'es6-promise/es6-promise.auto.min.js',
        YII_ENV_DEV ? 'vuex/dist/vuex.js' : 'vuex/dist/vuex.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'kekaadrenalin\vue\VueAsset',
    ];
}