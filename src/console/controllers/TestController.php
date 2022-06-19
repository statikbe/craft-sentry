<?php

namespace statikbe\sentry\console\controllers;

use craft\console\Controller;
use statikbe\sentry\Sentry;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class TestController extends Controller
{
    public function actionIndex()
    {
        try {
            throw new InvalidConfigException("Testing Sentry.io integration");
        } catch (Exception $e) {
            Sentry::handleException($e);
        }
        return true;
    }
}
