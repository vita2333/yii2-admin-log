yii2-admin-log
============
yii2 admin log extention

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vita2333/yii2-admin-log "*"
```

or add

```
"vita2333/yii2-admin-log": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php

class BaseController extends \yii\web\Controller {

  /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors              = parent::behaviors();
        $behaviors['admin-log'] = [
            'class' => AdminLogBehavior::class,
        ];

        return $behaviors;
    }
 }

```
