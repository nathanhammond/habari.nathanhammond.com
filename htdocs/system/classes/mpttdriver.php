<?php
if( ! defined( 'HABARI_PATH' ) ) {
	define( 'HABARI_PATH', '../../' );
}
if ( !defined( 'DEBUG' ) ) define( 'DEBUG', false );

require_once('../../config.php');
require_once('urlproperties.php');
require_once('queryrecord.php');
require_once('plugins.php');
require_once('utils.php');

require_once('databaseconnection.php');
require_once('singleton.php');
require_once('db.php');

require_once('mptt.php');
require_once('mpttset.php');
require_once('mpttnode.php');

DB::connect();

$mptt = new MPTT('terms');
?>