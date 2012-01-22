<?php
/* SVN FILE: $Id: core.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 *  0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 *  1: Errors and warnings shown, model caches refreshed, flash messages halted.
 *  2: As in 1, but also with full debug messages and SQL output.
 *  3: As in 2, but also with full controller dump.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */

    require_once 'debug_mode.php';


/**
 * Application wide charset encoding
 */
/**
 * To configure CakePHP *not* to use mod_rewrite and to
 * use CakePHP pretty URLs, remove these .htaccess
 * files:
 *
 * /.htaccess
 * /app/.htaccess
 * /app/webroot/.htaccess
 *
 * And uncomment the App.baseUrl below:
 */
    //Configure::write('App.baseUrl', env('SCRIPT_NAME'));
/**
 * Uncomment the define below to use CakePHP admin routes.
 *
 * The value of the define determines the name of the route
 * and its associated controller actions:
 *
 * 'admin'      -> admin_index() and /admin/controller/index
 * 'superuser' -> superuser_index() and /superuser/controller/index
 */
    Configure::write('Routing.admin', 'admi');

/**
 * Turn off all caching application-wide.
 *
 */
    //Configure::write('Cache.disable', true);
/**
 * Enable cache checking.
 *
 * If set to true, for view caching you must still use the controller
 * var $cacheAction inside your controllers to define caching settings.
 * You can either set it controller-wide by setting var $cacheAction = true,
 * or in each action using $this->cacheAction = true.
 *
 */
    //Configure::write('Cache.check', true);
/**
 * Defines the default error type when using the log() function. Used for
 * differentiating error logging and debugging. Currently PHP supports LOG_DEBUG.
 */
    define('LOG_ERROR', 2);
/**
 * The preferred session handling method. Valid values:
 *
 * 'php'            Uses settings defined in your php.ini.
 * 'cake'       Saves session files in CakePHP's /tmp directory.
 * 'database'   Uses CakePHP's database sessions.
 *
 * To define a custom session handler, save it at /app/config/<name>.php.
 * Set the value of 'Session.save' to <name> to utilize it in CakePHP.
 *
 * To use database sessions, execute the SQL file found at /app/config/sql/sessions.sql.
 *
 */
    Configure::write('Session.save', 'php');
/**
 * The name of the table used to store CakePHP database sessions.
 *
 * 'Session.save' must be set to 'database' in order to utilize this constant.
 *
 * The table name set here should *not* include any table prefix defined elsewhere.
 */
    //Configure::write('Session.table', 'cake_sessions');
/**
 * The DATABASE_CONFIG::$var to use for database session handling.
 *
 * 'Session.save' must be set to 'database' in order to utilize this constant.
 */
    //Configure::write('Session.database', 'default');
/**
 * The name of CakePHP's session cookie.
 */
    Configure::write('Session.cookie', 'CAKEPHP');
/**
 * Session time out time (in seconds).
 * Actual value depends on 'Security.level' setting.
 */
    Configure::write('Session.timeout', '1200');
/**
 * If set to false, sessions are not automatically started.
 */
    Configure::write('Session.start', true);
/**
 * When set to false, HTTP_USER_AGENT will not be checked
 * in the session
 */
    Configure::write('Session.checkAgent', true);
/**
 * The level of CakePHP security. The session timeout time defined
 * in 'Session.timeout' is multiplied according to the settings here.
 * Valid values:
 *
 * 'high'   Session timeout in 'Session.timeout' x 10
 * 'medium' Session timeout in 'Session.timeout' x 100
 * 'low'        Session timeout in 'Session.timeout' x 300
 *
 * CakePHP session IDs are also regenerated between requests if
 * 'Security.level' is set to 'high'.
 */
    Configure::write('Security.level', 'low');
/**
 * A random string used in security hashing methods.
 */
    Configure::write('Security.salt', 'ironman');
/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
    //Configure::write('Asset.filter.css', 'css.php');
/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JavaScriptHelper::link().
 */
    //Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');
/**
 * The classname and database used in CakePHP's
 * access control lists.
 */
    Configure::write('Acl.classname', 'DbAcl');
    Configure::write('Acl.database', 'default');
/**
 *
 * Cache Engine Configuration
 * Default settings provided below
 *
 * File storage engine.
 *
 *   Cache::config('default', array(
 *      'engine' => 'File', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
 *      'prefix' => 'cake_', //[optional]  prefix every cache file with this string
 *      'lock' => false, //[optional]  use file locking
 *      'serialize' => true, [optional]
 *  ));
 *
 *
 * APC (http://pecl.php.net/package/APC)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Apc', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *  ));
 *
 * Xcache (http://xcache.lighttpd.net/)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Xcache', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
 *      'user' => 'user', //user from xcache.admin.user settings
 *      'password' => 'password', //plaintext password (xcache.admin.pass)
 *  ));
 *
 *
 * Memcache (http://www.danga.com/memcached/)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Memcache', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *      'servers' => array(
 *          '127.0.0.1:11211' // localhost, default port 11211
 *      ), //[optional]
 *      'compress' => false, // [optional] compress data in Memcache (slower, but uses less memory)
 *  ));
 *
 */
// File engine caching

// Only use APC on production.
$engine = (Configure::read('debug') == 0 && Configure::read('test') == 0) ? 'Apc' : 'File';

// Core Caching

if (Configure::read() >= 1) {
    $duration = '+10 seconds';
} else {
    $duration = '+999 days';
}

$path = CACHE;
Cache::config('_cake_core_', array(
    'engine' => $engine,
    'prefix' => 'cake_core_', 'path' => $path . DS . 'persistent' . DS,
    'serialize' => true, 'duration' => $duration
));

Cache::config('_cake_model_', array(
    'engine' => $engine,
    'prefix' => 'cake_model_', 'path' => $path . DS . 'models' . DS,
    'serialize' => true, 'duration' => $duration
));


Cache::config('file', array(
    'engine' => 'File',
    'duration'=> '+1 day',
    'probability'=> 100,
    'path' => CACHE,
    'prefix' => 'cake_',
    'lock' => false,
    'serialize' => true)
);
Cache::config('fifteen_min', array(
    'engine' => $engine,
    'duration'=> '+15 minutes',
    'probability'=> 100,
    'path' => CACHE,
    'prefix' => 'cake_',
    'lock' => false,
    'serialize' => true)
);
Cache::config('short', array(
    'engine' => $engine,
    'duration'=> '+4 hours',
    'probability'=> 100,
    'path' => CACHE,
    'prefix' => 'cake_',
    'lock' => false,
    'serialize' => true)
);
// long
Cache::config('long', array(
    'engine' => $engine,
    'duration'=> '+12 hours',
    'probability'=> 100,
    'path' => CACHE,
    'prefix' => 'cake_',
    'lock' => false,
    'serialize' => true)
);

Cache::config('default', array('engine' => 'File'));
?>