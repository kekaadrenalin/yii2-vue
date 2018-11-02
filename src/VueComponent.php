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
     * @var string The ID for the Vue component instance
     */
    public $id;

    public $js;

    /**
     * @var bool Is view?
     */
    public $isView = false;

    /**
     * @var array The options for component
     */
    protected $_options = [];


    public function script($js = '{}')
    {
        $this->js = $js;

        $this->registerJs();
    }

    /**
     * Registers a specific asset bundles
     */
    protected function registerJs()
    {
        $options = "template: '#_vm-{$this->id}',";
        $scriptCode = new JsExpression($this->js);

        $options = substr_replace($scriptCode, $options, 1, 0);

        $js = "Vue.component('{$this->id}', {$options});";

        Yii::$app->view->registerJs($js, View::POS_END);
    }

    /**
     * Render component's template
     *
     * @param string $viewFile
     * @param array  $params
     * @param null   $context
     */
    public function template($viewFile = '', $params = [], $context = null)
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
            'id'   => '_vm-' . $this->id,
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

        return $this;
    }

    /**
     * Initializes the HTML tag attributes for the widget container tag
     */
    protected function initOptions()
    {
        if (!$this->id) {
            $this->id = 'component-' . count(Yii::$app->vue->component);
        }

        $this->_options['template'] = '#_vm-' . $this->id;
    }
}

