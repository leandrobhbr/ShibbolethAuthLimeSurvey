# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## Open

### To Add

### To Change

## Unreleased

### Added in unreleased


### Changed in unreleased

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
