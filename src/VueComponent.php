<?php

namespace kekaadrenalin\vue;

use Yii;

use yii\base\Widget;
use yii\base\InvalidConfigException;

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is vue-component widget
 *
 * Class VueComponent
 * @package kekaadrenalin\vue
 */
class VueComponent extends Widget
{
    /**
     * @var array The props object for the Vue instance
     */
    public $props;

    /**
     * @var array The watch object for the Vue instance
     */
    public $watch;

    /**
     * @var array The computed object for the Vue instance
     */
    public $computed;

    /**
     * @var array The methods object for the Vue instance
     */
    public $methods;

    /**
     * @var array The created object for the Vue instance
     */
    public $created;

    /**
     * @var array The data object for the Vue instance
     */
    public $data;

    /**
     * @var array The options for the Vue.js
     */
    public $options = [];

    /**
     * @inheritdoc
     */
    public static function begin($config = [])
    {
        $widget = parent::begin($config);

        ob_start();

        return $widget;
    }

    /**
     * @inheritdoc
     */
    public static function end()
    {
        $content = ob_get_clean();
        $widget = self::$stack[count(self::$stack) - 1];
        $view = $widget->getView();

        $options = [
            'id'   => $widget->id,
            'type' => 'text/x-template',
        ];

        $view->beginBlock("vm-t-{$widget->id}");

        echo Html::beginTag('script', $options);
        echo $content;
        echo Html::endTag('script');

        $view->endBlock();

        $view->on(View::EVENT_END_BODY, function () use ($view, $widget) {
            echo $view->blocks["vm-t-{$widget->id}"];
        });

        return parent::end();
    }

    /**
     * Initializes the Vue.js
     */
    public function init()
    {
        parent::init();

        $this->initOptions();
        $this->registerJs();
    }

    /**
     * Initializes the HTML tag attributes for the widget container tag
     */
    protected function initOptions()
    {
        $this->options['template'] = '#' . $this->getId();

        $this->initData();
        $this->initProps();
        $this->initWatch();
        $this->initComputed();
        $this->initMethods();
        $this->initCreated();
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
            $data = Json::htmlEncode($this->data);
        } elseif (is_string($this->data)) {
            $data = $this->data;
        } else {
            throw new InvalidConfigException('The "data" option can only be a string or an array');
        }

        $this->options['data'] = new JsExpression('function(){ return ' . $data . '; }');
    }

    /**
     * Initializes props to be mixed into the Vue instance.
     *
     * @throws InvalidConfigException
     */
    protected function initProps()
    {
        if (empty($this->props)) {
            return;
        }

        foreach ($this->props as $propName => $handler) {
            $prop = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
            $this->options['props'][$propName] = $prop;
        }
    }

    /**
     * Initializes watch to be mixed into the Vue instance.
     *
     * @throws InvalidConfigException
     */
    protected function initWatch()
    {
        if (empty($this->watch)) {
            return;
        }

        if (!is_array($this->watch)) {
            throw new InvalidConfigException('The "watch" option are not an array');
        }

        foreach ($this->watch as $watchName => $handler) {
            $function = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
            $this->options['watch'][$watchName] = $function;
        }
    }

    /**
     * Initializes computed to be mixed into the Vue instance.
     *
     * @throws InvalidConfigException
     */
    protected function initComputed()
    {
        if (empty($this->computed)) {
            return;
        }

        if (!is_array($this->computed)) {
            throw new InvalidConfigException('The "computed" option are not an array');
        }

        foreach ($this->computed as $key => $callback) {
            if (is_array($callback)) {
                if (isset($callback['get'])) {
                    $function = $callback['get'] instanceof JsExpression ? $callback['get'] : new JsExpression($callback['get']);
                    $this->options['computed'][$key]['get'] = $function;
                }
                if (isset($callback['set'])) {
                    $function = $callback['set'] instanceof JsExpression ? $callback['set'] : new JsExpression($callback['set']);
                    $this->options['computed'][$key]['set'] = $function;
                }
            } else {
                $function = $callback instanceof JsExpression ? $callback : new JsExpression($callback);
                $this->options['computed'][$key] = $function;
            }
        }
    }

    /**
     * Initializes methods to be mixed into the Vue instance.
     *
     * @throws InvalidConfigException
     */
    protected function initMethods()
    {
        if (empty($this->methods)) {
            return;
        }

        if (!is_array($this->methods)) {
            throw new InvalidConfigException('The "methods" option are not an array');
        }

        foreach ($this->methods as $methodName => $handler) {
            $function = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
            $this->options['methods'][$methodName] = $function;
        }
    }

    /**
     * Initializes created to be mixed into the Vue instance.
     *
     * @throws InvalidConfigException
     */
    protected function initCreated()
    {
        if (empty($this->created)) {
            return;
        }

        if ($this->created instanceof JsExpression) {
            $this->options['created'] = $this->created;
        } elseif (is_string($this->created)) {
            $this->options['created'] = new JsExpression($this->created);
        } else {
            throw new InvalidConfigException('The "created" option can only be a string or instanceof JsExpression');
        }
    }

    /**
     * Registers a specific asset bundles
     */
    protected function registerJs()
    {
        VueAsset::register($this->getView());

        $options = Json::encode($this->options);
        $js = "Vue.component('{$this->getId()}', {$options});";
        $this->getView()->registerJs($js, View::POS_END);
    }
}

