<?php
/**
 * Craft Sentry plugin for Craft CMS 3.x
 *
 * Sentry.io integration for Craft CMS
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2020 Statik.be
 */

namespace statikbe\sentry;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use Sentry as SentrySdk;
use Sentry\State\Scope;
use statikbe\sentry\models\Settings;
use statikbe\sentry\services\SentryService;
use yii\base\Event;

/**
 * Class CraftSentry
 *
 * @author    Statik.be
 * @package   Sentry
 * @since     1.0.0
 *
 * @property SentryService $sentry
 */
class Sentry extends Plugin
{
    /**
     * @var Sentry
     */
    public static $plugin;

    // Public Methods
    // =========================================================================
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $request = Craft::$app->getRequest();
        if ($request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'statikbe\sentry\console\controllers';
        }

        $this->components = [
            'sentry' => SentryService::class
        ];

        Event::on(
            ErrorHandler::className(),
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) {
                if($this->getSettings()->enabled) {
                    $this->sentry->handleException($event->exception);
                }
            }
        );
    }

    // Static Methods
    // =========================================================================
    public static function handleException($exception)
    {
        Sentry::$plugin->sentry->handleException($exception);
    }

    // Protected Methods
    // =========================================================================
    protected function createSettingsModel(): Model
    {
        return new Settings();
    }

}
