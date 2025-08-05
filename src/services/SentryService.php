<?php

namespace statikbe\sentry\services;

use Craft;
use craft\base\Component;
use Sentry as SentrySdk;
use Sentry\State\Scope;
use statikbe\sentry\events\DefineSentrySdkConfigurationEvent;
use statikbe\sentry\Sentry;
use Twig\Error\RuntimeError;

class SentryService extends Component
{
    /**
     * @event DefineSentrySdkConfigurationEvent The event that is triggered when defining the sentry SDK configuration.
     * @since 3.6.5
     */
    public const EVENT_DEFINE_SENTRY_SDK_CONFIGURATION = 'defineSentrySdkConfiguration';

    public function handleException($exception)
    {
        $plugin = Sentry::$plugin;
        $settings = $plugin->getSettings();

        // If this is a Twig Runtime exception, use the previous one instead
        if ($exception instanceof RuntimeError && ($previousException = $exception->getPrevious()) !== null) {
            $exception = $previousException;
        }

        $statusCode = $exception->statusCode ?? null;
        Craft::info("Exception with status $statusCode received", $plugin->handle);

        if (in_array($statusCode, $settings->excludedCodes)) {
            Craft::info('Exception status code excluded from being reported to Sentry.', $plugin->handle);
            return;
        }

        if(in_array(get_class($exception), $settings->excludedExceptions)) {
            Craft::info('Exception class excluded from being reported to Sentry.', $plugin->handle);
            return;
        }

        $this->setupSentry();

        Craft::info('Send exception to Sentry.', $plugin->handle);
        SentrySdk\configureScope(function(SentrySdk\State\Scope $scope) use ($statusCode) {
            $scope->setExtra('Status Code', $statusCode);
        });

        $status = SentrySdk\captureException($exception);
    }

    private function setupSentry()
    {
        $app = Craft::$app;
        $info = $app->getInfo();

        SentrySdk\init($this->getSentryInitOptions());


        $user = $app->getUser()->getIdentity();
        SentrySdk\configureScope(function(Scope $scope) use ($app, $info, $user) {
            $settings = Sentry::getInstance()->getSettings();

            if ($user && !$settings->anonymous) {
                $scope->setUser([
                    'id' => $user->email,
                    'Username' => $user->username,
                    'Email' => $user->email,
                    'Admin' => $user->admin ? 'Yes' : 'No',
                ]);
            }

            $scope->setExtra('App Type', 'Craft CMS');
            $scope->setExtra('App Name', $app->getSystemName());
            $scope->setExtra('App Edition', $app->getEditionName());
            $scope->setExtra('App Version', $info->version);
            $scope->setExtra('App Version (schema)', $info->schemaVersion);
            $scope->setExtra('PHP Version', phpversion());
            $scope->setExtra('URL', $app->getRequest()->isSiteRequest ? $app->getRequest()->getUrl() : "Console");
        });
    }

    protected function getSentryInitOptions()
    {
        $settings = Sentry::getInstance()->getSettings();

        $event = new DefineSentrySdkConfigurationEvent([
            'options' => [
                'dsn' => $settings->clientDsn,
                'environment' => Craft::$app->env,
                'release' => $settings->release,
                'http_proxy' => \Craft::$app->config->general->httpProxy,
            ],
        ]);
        $this->trigger(self::EVENT_DEFINE_SENTRY_SDK_CONFIGURATION, $event);

        return $event->options;
    }
}
