<?php

namespace statikbe\sentry\services;

use Craft;
use craft\base\Component;
use Sentry as SentrySdk;
use Sentry\State\Scope;
use statikbe\sentry\Sentry;

class SentryService extends Component
{
    public function handleException($exception)
    {
        $plugin = Sentry::$plugin;
        $settings = $plugin->getSettings();

        // If this is a Twig Runtime exception, use the previous one instead
        if ($exception instanceof \Twig_Error_Runtime && ($previousException = $exception->getPrevious()) !== null) {
            $exception = $previousException;
        }

        $statusCode = $exception->statusCode ?? null;
        Craft::info("Exception with status $statusCode received", $plugin->handle);

        if (in_array($statusCode, $settings->excludedCodes)) {
            Craft::info('Exception status code excluded from being reported to Sentry.', $plugin->handle);
            return;
        }

        $this->setupSentry();

        Craft::info('Send exception to Sentry.', $plugin->handle);
        SentrySdk\configureScope(function (SentrySdk\State\Scope $scope) use ($statusCode) {
            $scope->setExtra('Status Code', $statusCode);
        });

        $status = SentrySdk\captureException($exception);

    }

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
