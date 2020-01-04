<?php

namespace sorokinmedia\rollbar;

use Error;
use Exception;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

/**
 * Trait ErrorHandlerTrait
 * @package sorokinmedia\rollbar
 *
 * @property string $rollbarComponentName
 * @property array $payloadDataCallBack
 */
trait ErrorHandlerTrait
{
    public $rollbarComponentName = 'rollbar';

    /**
     * @var callable Callback returning a payload data associative array or null
     * Example:
     * function (ErrorHandler $errorHandler) {
     *     return [
     *         'foo' => 'bar',
     *         'xyz' => getSomeData(),
     *     ];
     * }
     */
    public $payloadDataCallback;

    /**
     * @param $exception
     * @throws Exception
     */
    public function logException($exception): void
    {
        $this->logExceptionRollbar($exception);
        parent::logException($exception);
    }

    /**
     * @param $exception
     * @throws Exception
     */
    protected function logExceptionRollbar($exception): void
    {
        foreach (Yii::$app->get($this->rollbarComponentName)->ignoreExceptions as $ignoreRecord) {
            if ($exception instanceof $ignoreRecord[0]) {
                $ignoreException = true;
                foreach (array_slice($ignoreRecord, 1) as $property => $range) {
                    if (!in_array($exception->$property, $range, true)) {
                        $ignoreException = false;
                        break;
                    }
                }
                if ($ignoreException) {
                    return;
                }
            }
        }
        // Check if an error coming from handleError() should be ignored.
        if ($exception instanceof ErrorException && Rollbar::logger()->shouldIgnoreError($exception->getCode())) {
            return;
        }
        $extra = $this->getPayloadData($exception);
        if ($extra === null) {
            $extra = [];
        }
        $level = $this->isFatal($exception) ? Level::CRITICAL : Level::ERROR;
        Rollbar::log($level, $exception, $extra, true);
    }

    /**
     * @param $exception
     * @return array|mixed|null
     * @throws Exception
     */
    private function getPayloadData($exception)
    {
        $payloadData = $this->payloadCallback();

        if ($exception instanceof WithPayload) {
            $exceptionData = $exception->rollbarPayload();
            if (is_array($exceptionData)) {
                if ($payloadData === null) {
                    $payloadData = $exceptionData;
                } else {
                    $payloadData = ArrayHelper::merge($exceptionData, $payloadData);
                }
            } elseif ($exceptionData !== null) {
                throw new Exception(get_class($exception) . '::rollbarPayload() returns an incorrect result');
            }
        }
        return $payloadData;
    }

    /**
     * @return mixed|null
     * @throws Exception
     */
    private function payloadCallback()
    {
        if (!isset($this->payloadDataCallback)) {
            return null;
        }
        if (!is_callable($this->payloadDataCallback)) {
            throw new Exception('Incorrect callback provided');
        }
        $payloadData = call_user_func($this->payloadDataCallback, $this);
        if (!is_array($payloadData) && $payloadData !== null) {
            throw new Exception('Callback returns an incorrect result');
        }
        return $payloadData;
    }

    /**
     * @param $exception
     * @return bool
     */
    protected function isFatal($exception): bool
    {
        return $exception instanceof Error
            || ($exception instanceof ErrorException
                && ErrorException::isFatalError(['type' => $exception->getSeverity()]));
    }
}
