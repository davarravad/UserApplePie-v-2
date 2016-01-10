<?php
/**
 * Config - an example for setting up system settings.
 * When you are done editing, rename this file to 'Config.php'.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Core;

use Helpers\Session;

/**
 * Configuration constants and options.
 */
class Config
{
    /**
     * Executed as soon as the framework runs.
     */
    public function __construct()
    {
        /**
         * Turn on output buffering.
         */
        ob_start();

        /**
         * Define relative base path.
         */
        define('DIR', 'http://www.userapplepie.com/'); // URL to root site WITH trailing slash

        /**
         * Set default controller and method for legacy calls.
         */
        define('DEFAULT_CONTROLLER', 'welcome');
        define('DEFAULT_METHOD', 'index');

        /**
         * Set the default template.
         */
        define('TEMPLATE', 'default');

        /**
         * Set a default language.
         */
        define('LANGUAGE_CODE', 'en');
		    define("LOC", "en"); // Language of Auth Class output : en / fr /es / de

        //database details ONLY NEEDED IF USING A DATABASE

        /**
         * Database engine default is mysql.
         */
        define('DB_TYPE', 'mysql');

        /**
         * Database host default is localhost.
         */
        define('DB_HOST', 'localhost');

        /**
         * Database name.
         */
        define('DB_NAME', 'DatabaseName');

        /**
         * Database username.
         */
        define('DB_USER', 'UserName');

        /**
         * Database password.
         */
        define('DB_PASS', 'Password');

        /**
         * PREFER to be used in database calls default is smvc_
         */
        define('PREFIX', 'uap_');

        /**
         * Set prefix for sessions.
         */
        define('SESSION_PREFIX', 'uap_');
        /**
         * Set prefix for sessions.
         */
        define('COOKIE_PREFIX', 'uap_');

        /**
         * Optional create a constant for the name of the site.
         */
        define('SITETITLE', 'UAP V2.0 Demo');
		    define("SITE_NAME", "UAP V2.0 Demo"); // Name of website to appear in emails

        /**
         * User Registration Settings.
         */
		    define('NEW_USER_ACTIVATION', 'true'); // Define if new member has to Activate their account or not true=activate by email, false account is active

        /**
         * Optional set a site email address.
         */
        define('SITEEMAIL', 'admin@userapplepie.com');
		    define("EMAIL_FROM", "noreply@userapplepie.com"); // Email FROM address for Auth emails (Activation, password reset...)

    		/**
    		 * Setups for Auth Goodies
    		 */
    		define("ACTIVATION_ROUTE", 'Activate'); //  for activating an account should be implemented by you on any controller you can name it whatever you want
        define("RESET_PASSWORD_ROUTE", 'ResetPassword'); // for resetting a password and should be implemented  by you on any controller you can name it whatever you want

    		/**
    		 * reCAPTCHA settings
    		 */
    		define("RECAP_PUBLIC_KEY", ''); // reCAPCHA site key  
    		define("RECAP_PRIVATE_KEY", ''); // reCAPCHA secret key

        /**
         * Turn on custom error handling.
         */
        set_exception_handler('Core\Logger::ExceptionHandler');
        set_error_handler('Core\Logger::ErrorHandler');

        /**
         * Set timezone.
         */
        date_default_timezone_set('America/Chicago');

        /**
         * Start sessions.
         */
        Session::init();
    }
}
