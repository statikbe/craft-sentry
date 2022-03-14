<?php

namespace statikbe\sentry\events;

use yii\base\Event;

/**
 * @event The event that is triggered when defining the configuration of the Sentry SDK.
 */
class DefineSentrySdkConfigurationEvent extends Event
{
    /**
     * @var array An associative array of options that will be passed to the SentrySdk\init() method.
     * @link https://docs.sentry.io/platforms/php/configuration/options/
     */
    public $options;
}
