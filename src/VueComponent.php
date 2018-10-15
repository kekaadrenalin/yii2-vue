<?php

namespace kekaadrenalin\vue;

use Yii;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is vue-component widget
 *
 * Class VueComponent
 *
 * @package kekaadrenalin\vue
 */
class VueComponent extends BaseObject
{
    /**
     * @var array The props object for the Vue component instance
     */
    public $props;

    /**
     * @var array The watch object for the Vue component instance
     */
    public $watch;

    /**
     * @var array The computed object for the Vue component instance
     */
    public $computed;

    /**
     * @var array The methods object for the Vue component instance
     */
    public $methods;

    /**
     * @var array The created object for the Vue component instance
     */
    public $created;

    /**
     * @var array The data object for the Vue component instance
     */
    public $data;

    /**
     * @var string The ID for the Vue component instance
     */
    public $id;

    /**
     * @var bool Is view?
     */
    public $isView = false;

    /**
     * @var array The options for component
     */
    protected $_options = [];

    /**
     * Render component's template
     *
     * @param string $viewFile
     * @param array  $params
     * @param null   $context
     */
    public function render($viewFile = '', $params = [], $context = null)
    {
        if ($this->isView) {
            return;
        }

        $content = Yii::$app->view->render($viewFile, $params, $context);

        $this->renderTemplate($content);
    }

    /**
     * Render template
     *
     * @param $content
     */
    protected function renderTemplate($content)
    {
        $view = Yii::$app->view;
        $options = [
            'id'   => $this->id,
            'type' => 'text/x-template',
        ];

        $this->isView = true;
        $template = Html::tag('script', $content, $options);

        $view->on(View::EVENT_END_BODY, function () use ($template) {
            echo $template;
        });
    }

    /**
     * Begin component's template
     */
    public function begin()
    {
        ob_start();
    }

    /**
     * End component's template
     */
    public function end()
    {
        $content = ob_get_clean();

        if ($this->isView) {
            return;
        }

        $this->renderTemplate($content);
    }

    /**
     * Initializes the Vue.js
     */
    public function init()
    {
        parent::init();

        $this->initOptions();
        $this->registerJs();

        return $this;
    }

    /**
     * Initializes the HTML tag attributes for the widget container tag
     */
    protected function initOptions()
    {
        if (!$this->id) {
            $this->id = 'vm-component-' . count(Yii::$app->vue->component);
        }

        $this->_options['template'] = '#' . $this->id;

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

        $this->_options['data'] = new JsExpression('function(){ return ' . $data . '; }');
    }

    /**
     * Initializes props to be mixed into the Vue instance.
     */
    protected function initProps()
    {
        if (empty($this->props)) {
            return;
        }

        foreach ($this->props as $propName => $handler) {
            $prop = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
            $this->_options['props'][$propName] = $prop;
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
            $this->_options['watch'][$watchName] = $function;
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
                    $this->_options['computed'][$key]['get'] = $function;
                }
                if (isset($callback['set'])) {
                    $function = $callback['set'] instanceof JsExpression ? $callback['set'] : new JsExpression($callback['set']);
                    $this->_options['computed'][$key]['set'] = $function;
                }
            } else {
                $function = $callback instanceof JsExpression ? $callback : new JsExpression($callback);
                $this->_options['computed'][$key] = $function;
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
            $this->_options['methods'][$methodName] = $function;
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
            $this->_options['created'] = $this->created;
        } elseif (is_string($this->created)) {
            $this->_options['created'] = new JsExpression($this->created);
        } else {
            throw new InvalidConfigException('The "created" option can only be a string or instanceof JsExpression');
        }
    }

    /**
     * Registers a specific asset bundles
     */
    protected function registerJs()
    {
        $options = Json::encode($this->_options);

        $js = "Vue.component('{$this->id}', {$options});";

        Yii::$app->view->registerJs($js, View::POS_END);
    }
}

