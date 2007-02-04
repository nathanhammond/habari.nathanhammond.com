<?php
/**
 * Habari Utility Class
 *
 * Requires PHP 5.0.4 or later
 * @package Habari
 */
 
class Utils
{
	/**
	 * Utils constructor
	 * This class should not be instantiated.
	 **/	 	 	
	private function __construct()
	{
	}

	/**
	 * function get_params
	 * Returns an associative array of parameters, whether the input value is 
	 * a querystring or an associative array.
	 * @param mixed An associative array or querystring parameter list
	 * @return array An associative array of parameters
	 **/	 	 	 	 	
	static function get_params( $params )
	{
		if( is_array( $params ) ) return $params;
		parse_str( $params, $paramarray );
		return $paramarray;
	}

	/**
	 * function end_in_slash
	 * Forces a string to end in a single slash
	 * @param string A string, usually a path
	 * @return string The string with the slash added or extra slashes removed, but with one slash only
	 **/
	static function end_in_slash( $value )
	{
		return rtrim($value, '/') . '/';
	}
	
	/**
	 * function redirect
	 * Redirects the request to a new URL
	 * @param string The URL to redirect to
	 **/	 
	static function redirect( $url )
	{
		header('Location: ' . $url, true, 302);
	}
	
	/**
	 * function atomtime
	 * Returns RFC-3339 time from a time string or integer timestamp
	 * @param mixed A string of time or integer timestamp
	 * @return string An FRC-3339 formatted time	 	 
	 **/
	static function atomtime($t)
	{
		if ( ! is_numeric( $t ) ) {
			$t = strtotime( $t );
		}
		$vdate = date( DATE_ATOM, $t );
		// If the date format used for timezone was O instead of P... 
		if ( substr( $vdate, -3, 1 ) != ':' ) {
			$vdate = substr( $vdate, 0, -2) . ':' . substr( $vdate, -2, 2 );
		}
		return $vdate;
	}
	
	/**
	 * function nonce
	 * Returns a random 12-digit hex number
	 **/
	static function nonce()
	{
		return sprintf('%06x', rand(0, 16776960)) . sprintf('%06x', rand(0, 16776960));
	}

	/**
	 * function stripslashes
	 * Removes slashes from escaped strings, including strings in arrays
	 **/
	static function stripslashes($value)
	{
		if ( is_array($value) ) {
			$value = array_map( array('Utils', 'stripslashes') , $value );
		}	elseif ( !empty($value) && is_string($value) ) {
			$value = stripslashes($value);
		}
		return $value;
	}
	
	/**
	 * function de_amp
	 * Returns &amp; entities in a URL querystring to their previous & glory, for use in redirects
	 * @param string $value A URL, maybe with a querystring	 
	 **/
	static function de_amp($value)
	{
		$url = parse_url( $value );
		$url['query'] = str_replace('&amp;', '&', $url['query']);
		return Utils::glue_url($url);
	}

	/**
	 * Restore a URL separated by a parse_url() call.
	 * @param $parsed array An array as returned by parse_url()
	 **/	 	 	 
	static function glue_url($parsed)
	{
		if ( ! is_array( $parsed ) ) {
			return false;
		}
		$uri= isset( $parsed['scheme'] )
			? $parsed['scheme'] . ':' . ( ( strtolower( $parsed['scheme'] ) == 'mailto' ) ? '' : '//' )
			: '';
		$uri.= isset( $parsed['user'] )
			? $parsed['user'].( $parsed['pass'] ? ':' . $parsed['pass'] : '' ) . '@'
			: '';
		$uri.= isset( $parsed['host'] ) ? $parsed['host'] : '';
		$uri.= isset( $parsed['port'] ) ? ':'.$parsed['port'] : '';
		$uri.= isset( $parsed['path'] ) ? $parsed['path'] : '';
		$uri.= isset( $parsed['query'] ) ? '?'.$parsed['query'] : '';
		$uri.= isset( $parsed['fragment'] ) ? '#'.$parsed['fragment'] : '';
		
		return $uri;
	}	
	
	/**
	 * function revert_magic_quotes_gpc
	 * Reverts magicquotes_gpc behavior
	 **/
	static function revert_magic_quotes_gpc()
	{
		if ( get_magic_quotes_gpc() ) {
			$_GET = self::stripslashes($_GET);
			$_POST = self::stripslashes($_POST);
			$_COOKIE = self::stripslashes($_COOKIE);
		}
	}

	/**
	 * function quote_spaced
	 * Adds quotes around values that have spaces in them
	 * @param string A string value that might have spaces
	 * @return string The string value, quoted if it has spaces
	 */
	static function quote_spaced( $value )
	{
		return (strpos($value, ' ') === false) ? $value : '"' . $value . '"';
	}	 	 	 	 	
	
	/**
	 * function implode_quoted
	 * Behaves like the implode() function, except it quotes values that contain spaces
	 * @param string A separator between each value
	 * @param	array An array of values to separate
	 * @return string The concatenated string
	 */	 	 
	static function implode_quoted( $separator, $values )
	{
		if ( ! is_array( $values ) )
		{
			$values = array();
		}
		$values = array_map(array('Utils', 'quote_spaced'), $values);
		return implode( $separator, $values );
	}
	
	/**
	 * function archive_pages
	 * Returns the number of pages in an archive using the number of items per page set in options
	 * @param integer Number of items in the archive
	 * @returns integer Number of pages based on pagination option.	
	 **/	  	 	 	
	static function archive_pages($item_total)
	{
		return ceil($item_total / Options::get('pagination'));
	}
	
	/**
	 * function page_selector
	 * Returns a simple linked page selector
	 * @param	integer Current page
	 * @param integer Total pages
	 * @param string The URL token for producing a link	 
	 * @param array Settings for the URLs output	 
	 **/
	static function page_selector($current, $total, $token, $settings = array())
	{
		$p= array(0,null,null,null,null);
		$p[0] = 1;
		if(1 != $total) {
			$p[4] = $total;
		}
		if($current != 1 && $current != $total) {
			$p[2] = $current;
		}
		if($current - 1 > 1) $p[1] = $current - 1;
		if($current + 1 < $total) $p[3] = $current + 1;
		$lastpage = 0;
		$out = '';
		for($z = 0; $z <= 4; $z++) {
			if( $p[$z] == null ) {
				continue;
			}
			if( ($p[$z] - $lastpage) > 1 ) $out .= '&hellip;'; 
			if(isset($p[$z])) {
				$caption = ($p[$z]==$current) ? '[' . $current . ']' : $p[$z];
				$url = URL::get($token, array_merge($settings, array('page'=>$p[$z])), false);
				$out .= '<a href="' . $url . '" ' . (($p[$z]==$current) ? 'class="current-page"' : '' ) . '>' . $caption . '</a>';
			}
			$lastpage = $p[$z];
		}
		return trim($out);
		
	}
	 	 	 
	
	/**
	 * function debug_reveal
	 * Helper function used by debug()
	 * Not for external use.	 
	 **/	 	 	
	static function debug_reveal($show, $hide, $debugid) 
	{
		return "<a href=\"#\" id=\"debugshow-{$debugid}\" onclick=\"debugtoggle('debugshow-{$debugid}');debugtoggle('debughide-{$debugid}');return false;\">$show</a><span style=\"display:none;\" id=\"debughide-{$debugid}\">$hide</span>";
	}
	
	/**
	 * function debug
	 * Outputs a call stack with parameters, and a dump of the parameters passed.
	 * @params mixed Any number of parameters to output in the debug box.
	 **/	 	 	 	 	
	static function debug()
	{
		$debugid= md5(microtime());
		$tracect= 0;

		$fooargs = func_get_args();
		echo "<div style=\"background-color:#ffeeee;border:1px solid red;text-align:left;\">";
		if(function_exists('debug_backtrace')) {
			$output = "<script type=\"text/javascript\">
			debuggebi = function(id) {return document.getElementById(id);}
			debugtoggle = function(id) {debuggebi(id).style.display = debuggebi(id).style.display=='none'?'':'none';}
			</script>
			<table style=\"background-color:#fff8f8;\">";
			$backtrace = array_reverse(debug_backtrace(), true);
			foreach($backtrace as $trace) {
				$file = $line = $class = $type = $function = '';
				extract($trace);
				if(isset($class))	$fname = $class . $type . $function; else	$fname = $function;
				if(!isset($file) || $file=='') $file = '[Internal PHP]'; else $file = basename($file);
					
				$output .= "<tr><td style=\"padding-left: 10px;\">{$file} ({$line}):</td><td style=\"padding-left: 20px;white-space: pre;font-family:Courier New,Courier,monospace\">{$fname}(";
				$comma = '';
				foreach((array)$args as $arg) {
					$tracect++; 
					$output .= $comma . Utils::debug_reveal( gettype($arg), htmlentities(print_r($arg,1)), $debugid . $tracect ); 
					$comma = ', '; 
				}
				$output .= ");</td></tr>"; 
			}
			$output .= "</table>";
			echo Utils::debug_reveal('[Show Call Stack]', $output, $debugid);
		}
		echo "<pre>";
		foreach( $fooargs as $arg1 ) {
			echo '<em>' . gettype($arg1) . '</em> ';
			echo htmlentities( var_export( $arg1 ) ) . "<br/>";
		}
		echo "</pre></div>";
	}
	
	/**
	 * Crypt a given password, or verify a given password against a given hash.
	 * 
	 * @todo Enable best algo selection after DB schema change.
	 * 
	 * @param string $password the password to crypt or verify
	 * @param string $hash (optional) if given, verify $password against $hash
	 * @return crypted password, or boolean for verification 
	 */
	public static function crypt( $password, $hash= NULL )
	{
		if ( $hash == NULL ) {
			// encrypt
			/*
			if ( function_exists( 'hash' ) ) { // PHP >= 5.1.2
				return self::ssha512( $password, $hash );
			}
			else {
				return self::ssha( $password, $hash );
			}
			*/ // uncomment the above block after db schema changes
			return self::ssha( $password, $hash );
		}
		elseif ( strlen( $hash ) > 3 ) { // need at least {, } and a char :p
			// verify
			if ( $hash{0} == '{' ) {
				// new hash from the block
				$algo= strtolower( substr( $hash, 1, strpos( $hash, '}', 1 ) - 1 ) );
				switch ( $algo ) {
					case 'sha1':
					case 'ssha':
					case 'ssha512':
						return self::$algo( $password, $hash );
					default:
						Error::raise( 'Unsupported digest algorithm "' . $algo . '"' );
						return FALSE;
				}
			}
			else {
				// legacy sha1
				return ( sha1( $password ) == $hash );
			}
		}
		else {
			Error::raise( 'Invalid hash' );
		}
	}
	
	/**
	 * Crypt or verify a given password using SHA.
	 * 
	 * @deprecated Use any of the salted methods instead.
	 */
	public static function sha1( $password, $hash= NULL ) {
		$marker= '{SHA1}';
		if ( $hash == NULL ) {
			return $marker . sha1( $password );
		}
		else {
			return ( sha1( $password ) == substr( $hash, strlen( $marker ) ) );
		}
	}
	
	/**
	 * Crypt or verify a given password using SSHA.
	 * Implements the {Seeded,Salted}-SHA algorithm as per RfC 2307.
	 * 
	 * @param string $password the password to crypt or verify
	 * @param string $hash (optional) if given, verify $password against $hash
	 * @return crypted password, or boolean for verification 
	 */
	public static function ssha( $password, $hash= NULL )
	{
		$marker= '{SSHA}';
		if ( $hash == NULL ) { // encrypt
			// create salt (4 byte)
			$salt= '';
			for ( $i= 0; $i < 4; $i++ ) {
				$salt.= chr( mt_rand( 0, 255 ) );
			}
			// get digest
			$digest= sha1( $password . $salt, TRUE );
			// b64 for storage
			return $marker . base64_encode( $digest . $salt );
		}
		else { // verify
			// is this a SSHA hash?
			if ( ! substr( $hash, 0, strlen( $marker ) ) == $marker ) {
				Error::raise( 'Invalid hash' );
				return FALSE;
			}
			// cut off {SSHA} marker
			$hash= substr( $hash, strlen( $marker ) );
			// b64 decode
			$hash= base64_decode( $hash );
			// split up
			$digest= substr( $hash, 0, 20 );
			$salt= substr( $hash, 20 );
			// compare
			return ( sha1( $password . $salt, TRUE ) == $digest );
		}
	}

	/**
	 * Crypt or verify a given password using SSHA512.
	 * Implements a modified version of the {Seeded,Salted}-SHA algorithm
	 * from RfC 2307, using SHA-512 instead of SHA-1.
	 * 
	 * Requires the new hash*() functions.
	 * 
	 * @param string $password the password to crypt or verify
	 * @param string $hash (optional) if given, verify $password against $hash
	 * @return crypted password, or boolean for verification
	 */
	public static function ssha512( $password, $hash= NULL )
	{
		$marker= '{SSHA512}';
		if ( $hash == NULL ) { // encrypt
			$salt= '';
			for ( $i= 0; $i < 4; $i++ ) {
				$salt.= chr( mt_rand( 0, 255 ) );
			}
			$digest= hash( 'sha512', $password . $salt, TRUE );
			return $marker . base64_encode( $digest . $salt );
		}
		else { // verify
			if ( ! substr( $hash, 0, strlen( $marker ) ) == $marker ) {
				Error::raise( 'Invalid hash' );
				return FALSE;
			}
			$hash= substr( $hash, strlen( $marker ) );
			$hash= base64_decode( $hash );
			$digest= substr( $hash, 0, 64 );
			$salt= substr( $hash, 64 );
			return ( hash( 'sha512', $password . $salt, TRUE ) == $digest );
		}
	}

}

?>
