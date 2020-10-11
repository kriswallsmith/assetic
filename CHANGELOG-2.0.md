v2.0.0 (2020-10-11)
------------------

### Project update

The project `kriswallsmith/assetic` is currently unmaintained, and has not been
updated in 4 years. You can replace `kriswallsmith/assetic` by swapping it out for
`"assetic/framework": "~2.0.0"` via composer.

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
* Support for `symfony/process` `v3.4.x` | `v4.x` | `v5.x`

### BC breaks

- Minimum PHP version required is now PHP 7.2
- Switching from `leafo/lessphp` to `wikimedia/less.php`
    - Due to this switch support has been dropped for `setPreserveComments` & `registerFunction` by the `LessphpFilter`.
- `twig/twig` support is optional now, `twig/extensions` must be required in your requirements if you need it.

# Filters Removed:
- apc cache (apc is no longer supported in php7.2)
- autoprefixer (autoprefixer cli is deprecated)
- cleancss (code is incompatible with the current API)
- CssEmbed
- Compass
- Dart
- ember precompile (the npm package no longer compiles)
- Gemfile (Ruby is no longer in use in the project)
- GSS
- Packager (throws deprecation notices in php7.4)
- pngout (npm package is abandoned)
- Roole (Roole was a language that compiles to CSS, the project is now dead and has been for at least 6 years, use LESS \ SCSS instead)
- SassFilter/ScssFilter/SassphpFilter (replaced by ScssphpFilter) 
- Sprockets (Assetic no longer integrates with Ruby packages)
- UglifyJS version 1 (No longer supported. Use the `Assetic\Filter\UglifyJs2Filter` for version 2 or `UglifyJs3Filter` for version 3 instead)
- Yui compressors
