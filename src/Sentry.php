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

        Event::on(
            ErrorHandler::className(),
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) {
                $this->sentry->handleException($event->exception);
            }
        );

        $this->components = [
            'sentry' => SentryService::class
        ];

        $this->setupSentry();

        Event::on(
            ErrorHandler::className(),
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) {
                $this->sentry->handleException($event->exception);
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
    protected function createSettingsModel()
    {
        return new Settings();
    }

    // Private Methods
    // =========================================================================
    private function setupSentry()
    {
        $app = Craft::$app;
        $info = $app->getInfo();
        $settings = $this->getSettings();

        SentrySdk\init([
                'dsn' => $settings->clientDsn,
                'environment' => CRAFT_ENVIRONMENT,
                'release' => $settings->release,
            ]
        );

        $user = $app->getUser()->getIdentity();
        SentrySdk\configureScope(function (Scope $scope) use ($app, $info, $settings, $user) {
            if ($user && !$settings->anonymous) {
                $scope->setUser([
                    'id' => $user->email,
                    'Username' => $user->username,
                    'Email' => $user->email,
                    'Admin' => $user->admin ? 'Yes' : 'No',
                ]);
            }

            $scope->setExtra('App Type', 'Craft CMS');
            $scope->setExtra('App Name', $info->name);
            $scope->setExtra('App Edition', $app->getEditionName());
            $scope->setExtra('App Version', $info->version);
            $scope->setExtra('App Version (schema)', $info->schemaVersion);
            $scope->setExtra('PHP Version', phpversion());
            $scope->setExtra('URL', $app->getRequest()->isSiteRequest ? $app->getRequest()->getUrl() : "Console");
        });
    }
}
