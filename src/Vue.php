<?php

namespace kekaadrenalin\vue;

use Yii;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is vue-root class
 *
 * Class Vue
 * @package kekaadrenalin\vue
 */
class Vue extends BaseObject
{
    /**
     * @var string ID Vue root
     */
    public $id = '_vm-global-root';

    /**
     * @var array The options global
     */
    public $options = [];

    /**
     * @var array The template tag options global
     */
    public $elOptions = [];

    /**
     * @var array Vue options object
     */
    protected $vueOptions = [];

    /**
     * @var array Vue data object
     */
    protected $data = [];

    /**
     * @var string The Var name for Vue
     */
    protected $name = '_vm';

    /**
     * @var string The Var name for Vuex store
     */
    protected $storeName = '_vmStore';

    /**
     * Vue constructor.
     *
     * @param string $id
     * @param array  $options
     * @param array  $elOptions
     * @param array  $config
     */
    public function __construct($id = '_vm-global-root', $options = [], $elOptions = [], array $config = [])
    {
        $this->id = $id;
        $this->options = $options;
        $this->elOptions = $elOptions;

        parent::__construct($config);
    }

    /**
     * Initializes the Vue.js
     */
    public function init()
    {
        parent::init();

        $this->initOptions();
    }

    /**
     * Begin root's template
     */
    public function begin()
    {
        ob_start();
    }

    /**
     * End root's template
     */
    public function end()
    {
        $content = ob_get_clean();

        echo Html::tag('div', $content, $this->elOptions);
    }

    /**
     * Initializes the options for the Vue object
     */
    protected function initOptions()
    {
        if (isset($this->options['data'])) {
            $this->data = $this->options['data'];
        }

        $this->vueOptions['el'] = "#{$this->id}";

        $this->initVarName();
        $this->initData();
        $this->initStore();
    }

    /**
     * Initializes the Var name for Vue object
     */
    protected function initVarName()
    {
        $name = str_replace(['-', '_'], ' ', $this->id);
        $name = StringHelper::mb_ucwords($name);
        $name = str_replace([' '], '', $name);

        $this->name = "_vm{$name}";
    }

    /**
     * Initializes the data object for the Vue instance
     */
    protected function initData()
    {
        if (is_bool($this->data) && !$this->data) {
            return;
        }

        if (is_array($this->data) || $this->data instanceof JsExpression) {
            $this->vueOptions['data'] = $this->data;
        } elseif (is_string($this->data)) {
            $this->vueOptions['data'] = new JsExpression($this->data);
        } else {
            throw new InvalidConfigException('The "data" option can only be a string or an array');
        }
    }

    /**
     * Initializes the store object for the Vue instance
     */
    protected function initStore()
    {
        if (count(Yii::$app->vue->store)) {
            $this->storeName = Yii::$app->vue->storeName;
            $this->vueOptions['store'] = $this->storeName;
        }
    }

    /**
     * Registers a specific asset bundles and Vue init code
     */
    public function renderJS()
    {
        $options = '{}';
        if ($this->vueOptions) {
            $options = Json::htmlEncode($this->vueOptions);
        }

        $options = str_replace("\"store\":\"{$this->storeName}\"", "\"store\":{$this->storeName}", $options);

        $js = "var {$this->name} = new Vue({$options});";
        Yii::$app->view->registerJs($js, View::POS_END);
    }
}
