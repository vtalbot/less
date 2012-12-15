# LESS Compiler for Laravel 4 (Illuminate)

[![Build Status](https://travis-ci.org/Ellicom/less.png)](https://travis-ci.org/Ellicom/less)

### Installation

Run `php artisan config:publish ellicom/less`

Then edit `config.php` in `app/packages/ellicom/less` to your needs.

Add `'Ellicom\Less\LessServiceProvider',` to `providers` in `app/config/app.php`
and `'Less' => 'Ellicom\Less\Facades\Less',` to `aliases` in `app/config/app.php`

### Usage

`<link rel="stylesheet" href="css/test.css">`

If `css/test.css` doesn't exists in the `public` directory, it will search for `test.less` in `app/less` directory.
If found, compile it if needed and return the result.

**Note that if you use `@import` and change imported files, they won't mark the requested file has changed and won't be recompile.**

### Todo

Add tests.