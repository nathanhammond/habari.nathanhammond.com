<?php
/**
 * Habari Options Class
 *
 * Requires PHP 5.0.4 or later
 * @package Habari
 */
 
class Options
{
	private $options;
	static $instance;
	
	/**
	 * constructor __construct
	 * This is private so that you can't construct it from outside this class.
	 * We might consider pre-loading a few options here to reduce single 
	 * database hits for options that are used on every page load.	 	 	 
	 **/	 	
	private function __construct() 
	{
		// Set some universal, un-editable defaults
		$this->options['hostname'] = $_SERVER['SERVER_NAME'];
	}

	/**
	* function instance
	* returns a singleton instance of the Options class. Use this to
	* retrieve values of options, like this:
	*
	* <code>
	* $foo = Options::instance()->foo;
	* </code>
	*
	* @param string an option name
	* @return object Singleton Options object
	*/
	public static function o()
	{
		if (!isset(self::$options))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}
	
	/**
	 * function get
	 * Shortcut to return the value of an option
	 * 
	 * <code>$foo = Options::get('foo');</code>
	 * 	 	 	 
	 * @param	string Name of the option to retrieve
	 **/	 
	public static function get( $option )
	{
		return self::o()->$option;
	}
	
	/**
	 * function out
	 * Shortcut to output the value of an option
	 * 
	 * <code>Options::out('foo');</code>
	 * 	 	 	 
	 * @param	string Name of the option to output
	 **/	 
	public static function out( $option )
	{
		echo self::o()->$option;
	}
	
	/**
	 * function set
	 * Shortcut to set the value of an option
	 * 
	 * <code>Options::set('foo', 'newvalue');</code>
	 * 	 	 	 
	 * @param	string Name of the option to set
	 * @param mixed New value of the option to store
	 **/	 
	public static function set( $option, $value )
	{
		self::o()->$option = $value;
	}

	/**
	 * function __get
	 * Allows retrieval of option values
	 * @param string Name of the option to get
	 * @return mixed Stored value for specified option
	 **/
	public function __get($name)
	{
		if(!isset($this->options[$name])) {
			$result = DB::get_row('SELECT value, type FROM ' . DB::table('options') . ' WHERE name = ?', array($name), 'QueryRecord');
			if ( Error::is_error( $result ) ) {
				$result->out();
				die();
			}
			else if ( is_object( $result ) ) {
				if($result->type == 1) {
					$this->options[$name] = unserialize($result->value);
				}
				else {
					$this->options[$name] = $result->value;
				}
			} else {
				// Return some default values here
				switch($name) {
				case 'pagination':
					return 10;
				case 'host_url':
					// If we're running on a port other than 80, add the port number
					// to the value returned from host_url
					$port= 80; // Default in case not set.
					if ( isset( $_SERVER['SERVER_PORT'] ) ) {
						$port= $_SERVER['SERVER_PORT'];
					}
					$portpart = "";
					if ( $port != 80 ) {
						$portpart= ":$port";
					}
					// use Utils::glue_url?
					return "http://" . $this->hostname . $portpart . $this->base_url;
				case 'comments_require_id':
					return FALSE;
				case 'pingback_send':
					return FALSE;
				}
				return NULL;
			}
		}
		
		return $this->options[$name];
	}
	
	/**
	 * function __set
	 * Applies the option value to the options table
	 * @param string Name of the option to set
	 * @param mixed Value to set
	 **/	 	 
	public function __set($name, $value) {
		$this->options[$name] = $value;
		
		if(is_array($value) || is_object($value)) {
			$result = DB::update( DB::table('options'), array('name'=>$name, 'value'=>serialize($value), 'type'=>1), array('name'=>$name) ); 
		}
		else {
			$result = DB::update( DB::table('options'), array('name'=>$name, 'value'=>$value, 'type'=>0), array('name'=>$name) ); 
		}
		if( Error::is_error($result) ) {
			$result->out();
			die();
		}
	}

}
 
?>
