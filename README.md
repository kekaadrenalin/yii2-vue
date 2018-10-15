Vue.js Extension for Yii2
=========================
This is the Vue.js + Vuex extension for Yii2.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kekaadrenalin/yii2-vue "*"
```

or add

```
"kekaadrenalin/yii2-vue": "*"
```

to the require section of your `composer.json` file.

Add a new component to the components section of the application configuration file :
```
'components' => [
    'vue' => [
        'class' => 'kekaadrenalin\vue\GlobalComponent',
    ],
    // ...
],
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

@app/views/layouts/main.php
```php
<?php $vueRoot = Yii::$app->vue->root([
    'el' => [
        'id'    => 'global-vue-container',
        'class' => 'contents',
    ],

    'options' => [
        'data' => false,
    ],

    'store' => [
        'main' => [
            'state'     => [
                'count' => 0,
            ],
            'mutations' => [
                'increment' => new \yii\web\JsExpression('function(state) { state.count++ }'),
            ],
        ],
    ],
]) ?>

<?php $vueRoot->begin() ?>
    <?= $content ?>
<?php $vueRoot->end() ?>
```

@app/views/site/index.php
```php
<?php $component = Yii::$app->vue->component([
    'id'   => 'Test',
    'data' => [
        'message' => '',
    ],
]); ?>

<?php $component->begin(); ?>
<div>
    <p>{{ message }}</p>
    <input v-model="message">
</div>
<?php $component->end(); ?>
```