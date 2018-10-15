<?php

namespace kekaadrenalin\vue;

use Yii;

use yii\helpers\ArrayHelper;
use yii\web\View;

use yii\base\InvalidConfigException;
use yii\base\Component;

/**
 * This is Vue global component
 *
 * Class VueComponent
 *
 * @package kekaadrenalin\vue
 */
class GlobalComponent extends Component
{
    /**
     * @var Vue[] The Vue objects
     */
    public $vue = [];

    /**
     * @var array The Vue Components objects
     */
    public $component = [];

    /**
     * @var array The Vuex object
     */
    public $store = [];

    /**
     * @var string The Var name for Vuex store
     */
    public $storeName = '_vmStore';

    /**
     * VueComponent init function.
     */
    public function init()
    {
        parent::init();

        $_view = Yii::$app->view;

        $_view->on(View::EVENT_END_BODY, [$this, 'renderVuex']);
        $_view->on(View::EVENT_END_BODY, [$this, 'renderRoot']);
    }

    /**
     * @param array $options
     *
     * @return Vue
     * @throws InvalidConfigException
     */
    public function root(array $options = [])
    {
        $elOptions = [
            'id'    => '_vm-el-' . count($this->vue),
            'class' => '_vm-root',
        ];

        if (isset($options['el'])) {
            $elOptions = ArrayHelper::merge($elOptions, $options['el']);
        }

        if (!isset($options['options'])) {
            throw new InvalidConfigException('Parameter "options" is missing.');
        }

        if (isset($options['store'])) {
            $this->store = ArrayHelper::merge($this->store, $options['store']);
        }

        $id = $elOptions['id'];

        $vue = new Vue($id, $options['options'], $elOptions);
        $this->vue[$id] = $vue;

        return $vue;
    }

    /**
     * @param array $options
     *
     * @return VueComponent
     */
    public function component(array $options = [])
    {
        if (isset($this->component[$options['id']]) && $this->component[$options['id']] instanceof VueComponent) {
            return $this->component[$options['id']];
        }

        $component = new VueComponent($options);
        $this->component[$component->id] = $component;

        return $component;
    }

    /**
     * Render JS for Vuex
     */
    protected function renderVuex()
    {
        if (count($this->store)) {
            VuexAsset::register(Yii::$app->view);
        }

        $store = new Vuex($this->store);
        $store->renderJS();
    }

    /**
     * Render JS for Root Vue
     */
    protected function renderRoot()
    {
        if (count($this->vue)) {
            VueAsset::register(Yii::$app->view);
        }

        /** @var Vue $vue */
        foreach ($this->vue as $id => $vue) {
            $vue->renderJS();
        }
    }
}

