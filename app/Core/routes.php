<?php
/**
 * Routes - all standard routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date updated Sept 19, 2015
 */

/** Create alias for Router. */
use Core\Router;
use Helpers\Hooks;

/** Define routes. */

/** Main Pages */
Router::any('', 'Controllers\Welcome@index');
Router::any('subpage', 'Controllers\Welcome@subPage');
Router::any('About', 'Controllers\Welcome@About');

/** Auth Pages */
Router::any('Login', 'Controllers\Auth@Login');
Router::any('Logout', 'Controllers\Auth@Logout');
Router::any('Register', 'Controllers\Auth@Register');
Router::any('Activate', 'Controllers\Auth@Activate');
Router::any('ChangePassword', 'Controllers\Auth@ChangePassword');
Router::any('ChangeEmail', 'Controllers\Auth@ChangeEmail');

/** Live Checks */
Router::any('LiveCheckEmail', 'Controllers\LiveCheck@emailCheck');
Router::any('LiveCheckUserName', 'Controllers\LiveCheck@userNameCheck');

/** Module routes. */
$hooks = Hooks::get();
$hooks->run('routes');

/** If no route found. */
Router::error('Core\Error@index');

/** Turn on old style routing. */
Router::$fallback = false;

/** Execute matched routes. */
Router::dispatch();
