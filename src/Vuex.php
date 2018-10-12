<?php

namespace kekaadrenalin\vue;

use yii\base\Widget;
use yii\base\InvalidConfigException;

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is vuex-app widget
 *
 * Class Vuex
 * @package kekaadrenalin\vue
 */
class Vuex extends Widget
{
    /**
     * @var array The vuex Store
     */
    public $store;

    /**
     * @var string The name of var for vuex Store
     */
    public $name = '_vmStore';

    /**
     * Initializes the Vuex.js
     */
    public function init()
    {
        parent::init();

        if (!$this->name) {
            $this->name = '_vmStore';
        }

        $this->registerJs();
    }

    /**
     * Registers a specific asset bundles
     */
    protected function registerJs()
    {
        VuexAsset::register($this->getView());

        $options = Json::htmlEncode($this->store);
        $js = "var {$this->name} = new Vuex.Store({$options});";
        $this->getView()->registerJs($js, View::POS_END);
    }
}
