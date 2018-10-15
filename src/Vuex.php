<?php

namespace kekaadrenalin\vue;

use Yii;

use yii\web\View;
use yii\helpers\Json;
use yii\base\BaseObject;

/**
 * This is vuex-app class
 *
 * Class Vuex
 * @package kekaadrenalin\vue
 */
class Vuex extends BaseObject
{
    /**
     * @var array Vuex modules
     */
    public $modules = [];

    /**
     * Vuex constructor.
     *
     * @param array $modules
     * @param array $config
     */
    public function __construct($modules = [], array $config = [])
    {
        $this->modules = $modules;

        parent::__construct($config);
    }

    /**
     * Registers a specific JS code
     */
    public function renderJS()
    {
        $name = Yii::$app->vue->storeName;
        $options = [
            'namespaced' => true,
            'modules'    => $this->modules,
        ];

        $options = Json::htmlEncode($options);

        $js = "var {$name} = new Vuex.Store({$options});";
        Yii::$app->view->registerJs($js, View::POS_END);
    }
}
