Yii2-references
===============
Powerful key-value references module for Yii2. 

Installation
------------

Run

```
php composer.phar require pozitronik/yii2-references "dev-master"
```

or add

```
"pozitronik/yii2-references": "dev-master"
```

to the require section of your `composer.json` file.

Module Setup
------------

```php
	'modules' => [
		'references' => [
			'class' => pozitronik\references\ReferencesModule::class
		],
    ];
```

After setup main module action can be accessible as 

```
your-app/web/index.php?r=references
```

by default (may differ due to yours urlManager config).

Requirements
------------

Yii2,
PHP >= 7.4.0


Usage
-----
Not documented yet.
