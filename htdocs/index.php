<?php
/**
 * Habari Index
 *
 * This is where all the magic happens:
 * 1. Validate the installation
 * 2. Set the locale
 * 3. Load the active plugins
 * 4. Parse and handle the incoming request
 * 5. Run the cron jobs
 * 6. Dispatch the request to the found handler
 *
 * @package Habari
 */

define('DEBUG', true);

// Compares PHP version against our requirement.
if ( ! version_compare( PHP_VERSION, '5.2.0', '>=' ) ) {
	die ( 'Habari needs PHP 5.2.x or higher to run. You are currently running PHP ' . PHP_VERSION . '.' );
}

/**
 * Define the constant HABARI_PATH.
 * The path to the root of this Habari installation.
 */
define( 'HABARI_PATH', dirname( __FILE__ ) );

// We start up output buffering in order to take advantage of output compression,
// as well as the ability to dynamically change HTTP headers after output has started.
ob_start();

/**
 * Attempt to load the class before PHP fails with an error.
 * This method is called automatically in case you are trying to use a class which hasn't been defined yet.
 *
 * We look for the undefined class in the following folders:
 * - /system/classes/*.php
 * - /user/classes/*.php
 * - /user/sites/x.y.z/classes/*.php
 *
 * @param string $class_name Class called by the user
 */
function __autoload($class_name) {
	static $files= null;

	$success= false;
	$class_file = strtolower($class_name) . '.php';

	if( empty($files) ) {
		$files = array();
		$dirs= array( HABARI_PATH . '/system', HABARI_PATH . '/user' );

		// For each directory, save the available files in the $files array.
		foreach ($dirs as $dir) {
			$glob = glob( $dir . '/classes/*.php' );
			if ( $glob !== false && empty( $glob ) ) continue;
			$fnames = array_map(create_function('$a', 'return strtolower(basename($a));'), $glob);
			$files = array_merge($files, array_combine($fnames, $glob));
		}

		// Load the Site class, a requirement to get files from a multisite directory.
		if(isset($files['site.php'])) {
			require_once $files['site.php'];
		}

		// Verify if this Habari instance is a multisite.
		if ( ($site_user_dir = Site::get_dir('user')) != HABARI_PATH . '/user' ) {
			// We are dealing with a site defined in /user/sites/x.y.z
			// Add the available files in that directory in the $files array.
			$glob = glob( $site_user_dir . '/classes/*.php' );
			$fnames = array_map(create_function('$a', 'return strtolower(basename($a));'), $glob);
			if ( $glob !== false && ! empty( $glob ) && ! empty( $fnames ) ) {
				$files = array_merge($files, array_combine($fnames, $glob));
			}
		}
	}

	// Search in the available files for the undefined class file.
	if(isset($files[$class_file])) {
		require_once $files[$class_file];
		// If the class has a static method named __static(), execute it now, on initial load.
		if(class_exists($class_name, false) && method_exists($class_name, '__static') ) {
			call_user_func(array($class_name, '__static'));
		}
		$success= true;
	}

	if ( ! $success ) {
		die( 'Could not include class file ' . $class_file );
	}
}

// Increase the error reporting level, E_NOTICE will not be displayed.
error_reporting( E_ALL );

// Use our own error reporting class.
Error::handle_errors();

/* Initiate install verifications */

// Retrieve the configuration file's path.
$config = Site::get_dir( 'config_file' );

// Set the locale.
Locale::set( 'en-us' );

/**
 * We make sure the configuration file exist.
 * If it does, we load it and check it's validity.
 *
 * @todo Call the installer from the database classes.
 */
if ( file_exists( $config ) ) {
	require_once $config;
	if ( !defined( 'DEBUG' ) ) define( 'DEBUG', false );

	// Make sure we have a DSN string and database credentials.
	// $db_connection is an array with necessary informations to connect to the database.
	if ( ! isset($db_connection) ) {
		$installer= new InstallHandler();
		$installer->begin_install();
	}

	// Try to connect to the database.
	if (DB::connect()) {
		// Make sure Habari is installed properly.
		// If the 'installed' option is missing, we assume the database tables are missing or corrupted.
		// @todo Find a decent solution, we have to compare tables and restore or upgrade them.
		if (! @ Options::get('installed')) {
			$installer= new InstallHandler();
			$installer->begin_install();
		}
	}
	else {
		$installer= new InstallHandler();
		$installer->begin_install();
	}
}
else
{
	if ( !defined( 'DEBUG' ) ) define( 'DEBUG', false );
	// The configuration file does not exist.
	// Therefore we load the installer to create the configuration file and install a base database.
	$installer= new InstallHandler();
	$installer->begin_install();
}

/* Habari is installed and we established a connection with the database */

// Verify if the database has to be upgraded.
if ( Version::requires_upgrade() ) {
	$installer= new InstallHandler();
	$installer->upgrade_db();
}

// Send the Content-Type HTTP header.
// @todo Find a better place to put this.
header( 'Content-Type: text/html;charset=utf-8' );

/**
 * Include all the active plugins.
 * By loading them here they'll have global scope.
 */
foreach ( Plugins::list_active() as $file )
{
	include_once( $file );
	// Call the plugin's load procedure.
	Plugins::load( $file );
}

// All plugins loaded, tell the plugins.
Plugins::act('plugins_loaded');

// Start the session.
Session::init();

// Initiating request handling, tell the plugins.
Plugins::act('init');

// Parse and handle the request.
Controller::parse_request();

// Run the cron jobs.
CronTab::run_cron();

// Dispatch the request (action) to the matched handler.
Controller::dispatch_request();

// Flush (send) the output buffer.
ob_flush();
?>
