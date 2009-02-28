<?php
/*
	HOW TO USE THIS FILE FOR TESTING
	================================

	1. Create a tests directory inside your Habari root
	(inside the htdocs directory, not below the htdocs directory
	where this file is usually found)
	2. Checkout the tests directory from the Habari repo to the new
	tests directory you created, or copy the contents of an already
	checked out tests directory to the new one.
	3. Add the PHPUnit source to the include_path in your php.ini
	4. Execute this file with your php executable, like so:
	      php -f AllTests.php

	If your php executable isn't in your system path, you may need
	to specify the full path to the php executable.  Also, you may
	need to specify the full path to the AllTests.php file.

	This test may require an active database, and may render that
	database subsequently inoperable.
*/


// File automatically generated by PHPEdit
// PHPEdit's unit tests extension might not work as expected if you modify this file
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter( __FILE__ );

if ( !defined( 'PHPUnit_MAIN_METHOD' ) ) {
	define( 'PHPUnit_MAIN_METHOD', 'AllTests::main' );
	chdir( dirname( dirname( __FILE__ ) ) );
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
// PHPEdit Inclusions -- dot not remove this comment
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'system/AllTests.php';
// /PHPEdit Inclusions -- dot not remove this comment
class AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run( self::suite() );
	}

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite( 'PHPUnit_Framework' );
		// PHPEdit Tests suites -- dot not remove this comment
		$suite->addTest( system_AllTests::suite() );
		// /PHPEdit Tests suites -- dot not remove this comment
		return $suite;
	}
}
// This is used to find the starting point of Habari
if ( !defined( 'HABARI_PATH' ) ) {
	define( 'HABARI_PATH', dirname( dirname( __FILE__ ) ) );
}
// Specify a directory for test data
if ( ! defined( 'TEST_DATA_DIR' ) ) {
	define( 'TEST_DATA_DIR', dirname( __FILE__) );
}
// This constant prevents the regular handling of URL-based requests
// during testing
if ( !defined( 'UNIT_TEST' ) ) {
	define( 'UNIT_TEST', true );
}
// Debugging is usually not required when simply running tests
if ( !defined( 'DEBUG' ) ) {
	// define('DEBUG', true);
}
// Initialize the Habari structures and setup class autoload
require_once HABARI_PATH . '/index.php';

if ( PHPUnit_MAIN_METHOD == 'AllTests::main' ) {
	AllTests::main();
}

?>