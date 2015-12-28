# UAP Version 2.0.0 - Coming Soon
We are starting from scratch with UAP2.  This build is expected to take some time.  Check back soon for updates!

[![Software License](http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/simple-mvc-framework/v2/master/license.txt)

## What is UserApplePie v2.0.0?

Simple User Management System Based on MVC Framework. It's designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.

The base framework comes with a range of [helper classes](https://github.com/simple-mvc-framework/framework/tree/master/app/Helpers).

## Documentation

(Coming Soon!)Full docs & tutorials are available at [www.userapplepie.com](http://www.userapplepie.com).

## Requirements

The framework requirements are limited:

- [Apache Web Server](https://httpd.apache.org/) or equivalent with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) support
- [PHP 5.5 or greater](http://php.net/downloads.php) is required
- [MySQL database](http://www.mysql.com/). The framework can be changed to work with another database type.

## Installation

1. [Download](http://www.userapplepie.com/Downloads/) the framework.
2. Unzip the package to your web server.
3. Open `app/Core/Config.php` and set your base path (if the framework is installed in a folder, the base path should reflect the folder path `/path/to/folder/` otherwise a single `/` will do) and database credentials.
4. Edit `.htaccess` file and save the base path (if the framework is installed in a folder, the base path should reflect the folder path `/path/to/folder/` otherwise a single `/` will do).
5. Import the database.sql to your database (Updated table PREFIX if changed in Config.php).
6. Enjoy!

Note: Project is not complete, Stuff might be broken...

## Credit
Thank You to All Who Make UAP Possible!
- [Auth System](https://github.com/geomorillo/Auth) By: geomorillo

## Built on Simple MVC Framework

![Simple MVC Framework](http://simplemvcframework.com/app/templates/publicthemes/smvc/images/logo.png)