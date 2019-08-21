<?php

namespace sorokinmedia\rollbar\console;

use sorokinmedia\rollbar\ErrorHandlerTrait;

/**
 * Class ErrorHandler
 * @package sorokinmedia\rollbar\console
 */
class ErrorHandler extends \yii\console\ErrorHandler
{
    use ErrorHandlerTrait;
}
