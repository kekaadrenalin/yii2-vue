Vue.js Extension for Yii2
=========================
This is the Vue.js extension for Yii2.

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


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?php kekaadrenalin\vue\VueComponent::begin([
    'id'       => "vue-app",
    'data'     => [
        'message' => "hello",
    ],
    'props'    => [
        'lar' => '{type: Number, default: 200}',
    ],
    'watch'    => [
        'message' => 'function(val) {console.log(val)}',
    ],
    'computed' => [
        'length' => 'function() {return this.message.length}',
    ],
    'methods'  => [
        'reverseMessage' => "function() {this.message = this.message.split('').reverse().join('')}",
    ],
    'created'  => "function() {console.log('create')}",
]); ?>
    <div>
        <p>{{ length * lar }}</p>
        <p>{{ message }}</p>

        <button @click="reverseMessage">Reverse Message</button>

        <input v-model="message">
    </div>
<?php kekaadrenalin\vue\VueComponent::end(); ?>```

<vue-app :lar="2"></vue-app>