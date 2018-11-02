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
     * @var string Vuex modules as string
     */
    protected $_modules = '';

    /**
     * Vuex constructor.
     *
     * @param array $modules
     * @param array $config
     */
    public function __construct($modules = [], array $config = [])
    {
        $this->_modules = implode(',', $modules);

        parent::__construct($config);
    }

    /**
     * Registers a specific JS code
     */
    public function renderJS()
    {
        $name = Yii::$app->vue->storeName;
        $options = "{modules: $this->_modules}";

        $js = "var {$name} = new Vuex.Store({$options});";
        Yii::$app->view->registerJs($js, View::POS_END);
    }
}
