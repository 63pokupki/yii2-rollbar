<?php

namespace sorokinmedia\rollbar\web;

use sorokinmedia\rollbar\ErrorHandlerTrait;

/**
 * Class ErrorHandler
 * @package sorokinmedia\rollbar\web
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    use ErrorHandlerTrait;
}
