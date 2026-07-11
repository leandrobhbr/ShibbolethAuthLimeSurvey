# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## Open

### To Add

### To Change

## Unreleased

### Added in unreleased

* Added missing contributors from the source branches of [atlet](https://github.com/atlet/LimeSurvey-ShibbolethAuth) and [leandrobhbr](https://github.com/leandrobhbr/ShibbolethAuthLimeSurvey)

### Changed in unreleased

## [1.1.0] - 20260710

### Added in 1.1.0

* Added LimeSurvey 7 to the compatibility list in [config.xml](config.xml) (compatibility with 5 and 6 is kept)

### Changed in 1.1.0

* Fixed PHP 8 warnings in [ShibbolethAuthLime.php](ShibbolethAuthLime.php):
  * `beforeLogin()` now returns early when the Shibboleth user id attribute is missing (`&&` changed to `||`), instead of proceeding with an undefined `$_SERVER` key
  * Removed usage of the undefined variable `$autocreateuser` (the setting key `'autocreateuser'` is now used directly)
  * Guarded `$_SERVER` attribute reads with the null coalescing operator (`?? ''`)
* Synced the plugin version in [config.xml](config.xml) (was still 1.0.2 while the changelog was at 1.0.3)
* Tested against LimeSurvey 7.0.4 and 6.x via Docker (`martialblog/limesurvey`); validation against a production Shibboleth IdP is still pending

## [1.0.3](https://github.com/stevleibelt/ShibbolethAuthLimeSurvey/tree/1.0.3) - 20240725

### Added in 1.0.3

* Added [php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
* Removed `composer.lock` from repository

### Changed in 1.0.2

* Changed [ShibbolethAuthLime.php](ShibbolethAuthLime.php) by running `./vendor/bin/php-cs-fixer fix ShibbolethAuthLime.php`

## [1.0.2](https://github.com/stevleibelt/ShibbolethAuthLimeSurvey/tree/1.0.2) - 20240725

### Added in 1.0.2

* [.gitignore](.gitignore)
* [composer.json](composer.json)
* Added and configured [php rector](https://getrector.com/)

### Changed in 1.0.2

* Changed [ShibbolethAuthLime.php](ShibbolethAuthLime.php) by running `./vendor/bin/rector process`
* Fix invalid version number in [config.xml](config.xml)

## [1.0.1](https://github.com/stevleibelt/ShibbolethAuthLimeSurvey/tree/1.0.1) - 20240725

This is an initial tag. It is started from 1.0.1 to be in line with the version from the [config.xml](config.xml).

### Added in 1.0.1

* Added this changelog

### Changed in 1.0.1

* Fixed PHP Notice and restored compatibility with LimeSurvey 5 and 6
