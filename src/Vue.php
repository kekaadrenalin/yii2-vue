<?php

namespace kekaadrenalin\vue;

use yii\base\Widget;
use yii\base\InvalidConfigException;

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is vue-app widget
 * 
 * Class Vue
 * @package kekaadrenalin\vue
 */
class Vue extends Widget
{
    /**
     * @var array The data object for the Vue instance
     */
    public $data;

    /**
     * @var array The options for the Vue.js
     */
    public $clientOptions = [];

    /**
     * @var array The HTML tag attributes for the widget container tag
     */
    public $options = [];

    /**
     * Initializes the Vue.js
     */
    public function init()
    {
        parent::init();

        $this->initOptions();
        $this->initClientOptions();
        $this->registerJs();
    }

    /**
     * Initializes the HTML tag attributes for the widget container tag
     */
    protected function initOptions()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * Initializes the options for the Vue object
     */
    protected function initClientOptions()
    {
        if (!isset($this->clientOptions['el'])) {
            $this->clientOptions['el'] = "#{$this->getId()}";
        }
        $this->initData();
    }

    /**
     * Initializes the data object for the Vue instance
     */
    protected function initData()
    {
        if (empty($this->data)) {
            return;
        }

        if (is_array($this->data) || $this->data instanceof JsExpression) {
            $this->clientOptions['data'] = $this->data;
        } elseif (is_string($this->data)) {
            $this->clientOptions['data'] = new JsExpression($this->data);
        } else {
            throw new InvalidConfigException('The "data" option can only be a string or an array');
        }
    }

    /**
     * Registers a specific asset bundles
     */
    protected function registerJs()
    {
        VueAsset::register($this->getView());

        $options = Json::htmlEncode($this->clientOptions);
        $js = "var vm = new Vue({$options})";
        $this->getView()->registerJs($js, View::POS_END);
    }

    /**
     * @inheritdoc
     */
    public static function begin($config = [])
    {
        $object = parent::begin($config);

        echo Html::beginTag('div', $object->options);

        return $object;
    }

    /**
     * @inheritdoc
     */
    public static function end()
    {
        echo Html::endTag('div');

        return parent::end();
    }
}