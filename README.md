Rollbar for Yii2
================
[![Packagist](https://img.shields.io/packagist/l/sorokinmedia/yii2-rollbar.svg)](https://github.com/sorokinmedia/yii2-rollbar/blob/master/LICENSE.md)
[![Packagist](https://img.shields.io/packagist/dt/sorokinmedia/yii2-rollbar.svg)](https://packagist.org/packages/sorokinmedia/yii2-rollbar)

This extension is the way to integrate [Rollbar](http://rollbar.com/) service with your Yii2 application.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/). 

 To install, either run
 ```
 $ php composer.phar require sorokinmedia/yii2-rollbar
 ```
 or add
 ```
 "sorokinmedia/yii2-rollbar": "dev-master"
 ```
 to the `require` section of your `composer.json` file.

If you want to use it with **Yii prior to 2.0.13**, you need yii2-rollbar of the version `1.6.*`.

Usage
-----
0. Add the component configuration in your *global* `main.php` config file:
 ```php
 'bootstrap' => ['rollbar'],
 'components' => [
     'rollbar' => [
         'class' => \sorokinmedia\rollbar\Rollbar::class,
         'accessToken' => 'YOUR_ACCESS_TOKEN',
         'environment' => 'local',
         'root' => '@root',
         // You can specify exceptions to be ignored by yii2-rollbar:
         'ignoreExceptions' => [
             ['yii\web\HttpException', 'statusCode' => [400, 403, 404]],
             ['yii\web\ForbiddenHttpException', 'statusCode' => [403]],
             ['yii\web\UnauthorizedHttpException', 'statusCode' => [401,403]],
         ],
     ],
 ],
 ```

0. Add the *web* error handler configuration in your *web* config file:
 ```php
 'components' => [
     'errorAction' => 'site/error',
     'class' => \sorokinmedia\rollbar\web\ErrorHandler::class,

     // You can include additional data in a payload:
     'payloadDataCallback' => function (\sorokinmedia\rollbar\web\ErrorHandler $errorHandler) {
         return [
             'exceptionCode' => $errorHandler->exception->getCode(),
             'rawRequestBody' => Yii::$app->request->getRawBody(),
         ];
     },
 ],
 ```

0. Add the *console* error handler configuration in your *console* config file:
 ```php
 'components' => [
     'errorHandler' => [
         'class' => \sorokinmedia\rollbar\console\ErrorHandler::class,
     ],
 ],
 ```


Log Target
----------
You may want to collect your logs produced by `Yii::error()`, `Yii::info()`, etc. in Rollbar.
Put the following code in your config:
 ```php
 'log' => [
     'targets' => [
         [
            'class' => \sorokinmedia\rollbar\log\Target::class,
            'levels' => ['error', 'warning', 'info'], // Log levels you want to appear in Rollbar

            // It is highly recommended that you specify certain categories.
            // Otherwise, the exceptions and errors handled by the error handlers will be duplicated.
            'categories' => ['application'],
         ],
     ],
 ],
 ```

The log target also appends `category` and `request_id` parameters to the log messages.
`request_id` is useful if you want to have a *yii2-debug*-like  grouping.
