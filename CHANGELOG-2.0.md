2.0.0 (2019-08-09)
------------------

### Project update

The project `kriswallsmith/assetic` is currently unmaintained, and has not been
updated in 3 years.

This fork aims to bring Assetic up to date with modern libraries and allow for it
to be used in conjunction with modern frameworks.

This update makes the project more opionated, removing duplicate filters in favour of
purely PHP-based ones. New filters are to be added in external packages and the core
is to be kept as minimal as possible.

As part of this update old, abandoned or redundant utilities have been removed.
This is in part to simplify the offering of project as well as to migrate to
a simplified tool set of php and javascript based utilities.

### New features

* Support for `>=php 7.2`
* Support for `symfony/process` 4

### BC breaks

- Minimum PHP version required is now PHP 7.2
- Switching from `leafo/lessphp` to `wikimedia/less.php`
    - Due to this switch support has been dropped for `setPreserveComments` & `registerFunction` by the `LessphpFilter`.

# Filters Removed:
- Roole | Roole was a language that compiles to CSS, the project is now dead and has been for at least 6 years | Use LESS \ SCSS instead


* Removed autoprefixer filter as autoprefixer cli is deprecated
* Removed pngout filter as npm package is abandoned
* Removed apc cache as apc is no longer supported in php7.2
* Removed cleancss filter as code is incompatible with the current api
* Removed ember precompile as the npm package no longer compiles
* Removed java dependent filters
* Removed Gemfile as ruby is no longer in use
* Replaced SassFilter/ScssFilter with their php alternatives
* Removed Yui compressor
* Removed Packager filter as it throws deprecation notices in php7.4
