<?php
/**
* Habari Modified Preorder Tree Traversal (MPTT) Class
* Creates an object that acts as the parent of all MPTT nodes for a specified table.
* 
* @package Habari
*/
class MPTTNode {

	/* Chaining. */

	public function end()
	{
		
	}

	/* Attributes. */

	public function depth()
	{
		
	}

	/* Traversal methods. */

	public function parent()
	{
		// SELECT * FROM $this->table WHERE id = $this->mptt_parent;
		// Returns MPTTNode
	}

	public function children()
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->id ORDER BY mptt_left ASC;
		// Returns MPTTSet
	}

	public function child( $n )
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->id ORDER BY mptt_left LIMIT $n, 1;
		// Returns MPTTNode
	}

	public function prev()
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->mptt_parent AND mptt_left = $this->mptt_left - 2;
		// Returns MPTTNode
	}

	public function next()
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->mptt_parent AND mptt_left = $this->mptt_left + 2;
		// Returns MPTTNode
	}

	public function prevAll( $self = false )
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->mptt_parent AND mptt_left < $this->mptt_left ORDER BY mptt_left;
		// Returns MPTTSet
	}

	public function nextAll( $self = false )
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->mptt_parent AND mptt_left > $this->mptt_left ORDER BY mptt_left;
		// Returns MPTTSet
	}

	public function siblings( $self = false )
	{
		// SELECT * FROM $this->table WHERE mptt_parent = $this->mptt_parent ORDER BY mptt_left;
		// Returns MPTTSet
	}

	public function ancestors( $self = false )
	{
		// SELECT parent.* FROM $this->table AS node, $this->table AS parent WHERE node.mptt_left BETWEEN parent.mptt_left AND parent.mptt_right AND node.id = $this->id ORDER BY parent.mptt_left ASC;
		// Returns MPTTSet
	}

	public function descendants( $self = false )
	{
		// TODO
		// Returns MPTTSet
	}

	/* Modification. */

	public function append( $node, $sort = false )
	{
		
	}

	public function append_to( $node, $sort = false )
	{
		
	}

	public function insert_before( $node )
	{
		
	}

	public function insert_after( $node )
	{
		
	}

	public function remove( $nodeID )
	{
		
	}
}
?>
