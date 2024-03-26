<?php
/**
 * Craft Sentry plugin for Craft CMS 3.x
 *
 * Sentry.io integration for Craft CMS
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2020 Statik.be
 */

/**
 * Craft Sentry config.php
 *
 * This file exists only as a template for the Craft Sentry settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'craft-sentry.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
    'enabled' => true,
    'anonymous' => true,
    'clientDsn' => getenv('SENTRY_DSN') ?: 'https://example@sentry.io/123456789',
    'excludedCodes' => ['400', '404', '429'],
    'release' => getenv('SENTRY_RELEASE') ?: null,
];
