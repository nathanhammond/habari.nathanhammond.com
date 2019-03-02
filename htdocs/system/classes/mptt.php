<?php
/**
* Habari Modified Preorder Tree Traversal (MPTT) Class
* Creates an object that acts as the parent of all MPTT nodes for a specified table.
* 
* @package Habari
*/
class MPTT
{
	/**
	 * Static function to return the predefined defaults for MPTT database column names.
	 *
	 * @return array Array of default column names required for MPTT
   */
	public static function default_fields()
	{
		return array(
			'mptt_parent' => 0,
			'mptt_left' => 0,
			'mptt_right' => 0
		);
	}

	/**
	 * Constructor for the MPTT class.
	 *
	 * @param string $table a string that identifies the table 
	 **/
	public function __construct( $table )
	{
		// TODO
		// I need to know the table name.
		// I need to know what fields in that table represent the MPTT fields.
		// I need a database connection.

		// Identify which fields provide the MPTT values.
		$this->fields = self::default_fields();
		
		$result = DB::get_results('SELECT * FROM '.$table.';');
		var_dump($result);

	}

	/**
	 * Selects a node from the database and returns it as a new MPTTNode object.
	 *
	 * @param int $nodeID an id of a node present in the MPTT table.
	 * @return MPTTNode object with the specified field values.
	 **/
	public function get_node( $nodeID )
	{
		// SELECT * FROM $this->table WHERE id = $nodeID;
	}

	/**
	 * Creates and returns a new MPTTNode object specific to the current MPTT object.
	 *
	 * @param array $paramarray an associative array of field values
	 * @return MPTTNode an object with the specified field values
	 **/
	public function create_node( $paramarray )
	{
		
	}
	
	/* Macro level functions. */

	/**
	 * Gets all of the leaf nodes of the current MPTT object.
	 *
	 * @return MPTTSet
	 **/
	public function get_leaf_nodes()
	{
		// SELECT * FROM $this->table WHERE mptt_right = mptt_left + 1;
	}

	/**
	 * Sort the tree level where that Gets all of the leaf nodes of the current MPTT object. Returns an array of MPTTNode objects.
	 *
	 * @param int $nodeID an id of an MPTTNode to identify which branch of the tree to sort.
	 * @param mixed $sortBy TODO
	 **/
	public function sort_level( $nodeID, $sortBy = false )
	{
		
	}

	public function replace_node( $oldNodeID, $newNode )
	{
		
	}

	public function move_node( $method, $relatedNodeID, $targetNodeID )
	{
		
	}
}
?>
