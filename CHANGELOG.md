# Craft Sentry Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 5.1.1 - 2025-08-05
### Fixed
- Use a safer way to determine Craft's environment ([#9](https://github.com/statikbe/craft-sentry/issues/9))

## 5.1.0 - 2024-10-02
### Added
- Added support for excluding specific exceptions from being sent to Sentry.

## 5.0.0 - 2024-03-26
### Added
- Craft 5 support 🚀

## 5.0.0-alpha.1 - 2024-01-02
### Added
- Craft 5 support

## 2.0.1 - 2022-06-19
### Updated
- CRAFT_ENVIRONMENT to App::env('ENVIRONMENT')

## 2.0.0 - 2022-06-19
### Added
- Craft 4

## 2.0.0-beta.1 - 2022-03-15
### Added
- Craft 4 compatiblity

## 1.1.0 - 2022-03-14
### Added
- Add the ability to pass custom options to the Sentry SDK by listening to the 
  `SentryService::EVENT_DEFINE_SENTRY_SDK_CONFIGURATION` event
- Honor the Craft [`httpProxy` general setting](https://craftcms.com/docs/3.x/config/config-settings.html#httpproxy)
- Added console function to send a test exception

## 1.0.3 - 2020-10-29
### Fixed
- Only set up Sentry after we've made sure we need to process the exception

## 1.0.2 - 2020-10-16
### Fixed
- Don't ignore the enabled setting

## 1.0.1 - 2020-10-12
### Fixed
- Removed double event listener [#1](https://github.com/statikbe/craft-sentry/issues/1)


## 1.0.0 - 2020-10-07
### Added
- Initial release
