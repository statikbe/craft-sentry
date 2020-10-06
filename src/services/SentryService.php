<?php

namespace statikbe\sentry\services;

use craft\base\Component;
use Sentry as SentrySdk;
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

        if (in_array($statusCode, $settings->excludedCodes)) {
            Craft::info('Exception status code excluded from being reported to Sentry.', $plugin->handle);
            return;
        }

        SentrySdk\configureScope(function (SentrySdk\State\Scope $scope) use ($statusCode) {
            $scope->setExtra('Status Code', $statusCode);
        });

        SentrySdk\captureException($exception);
    }
}
