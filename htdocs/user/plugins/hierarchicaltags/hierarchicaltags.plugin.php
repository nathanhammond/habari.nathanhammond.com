<?php
class HierarchicalTags extends Plugin
{
	/**
	 * function info
	 * Returns information about this plugin
	 * @return array Plugin info array
	 **/
	function info()
	{
		return array (
			'name' => 'Hierarchical Tags',
			'url' => 'http://habariproject.org',
			'author' => 'Nathan Hammond',
			'authorurl' => 'http://nathanhammond.com',
			'version' => '0.9',
			'description' => 'Allows for tags to be entered and displayed in a hierarchical manner.',
			'license' => 'Apache License 2.0',
		);
	}

	/**
	 * Add update beacon support
	 **/
	public function action_update_check()
	{
	 	Update::add( 'Hierarchical Tags', 'BE87DD46-874D-11DD-BE4C-CA5A55D89593', $this->info->version );
	}

	/**
	 * Filters the tag list before it goes into the DB.
	 **/
	public function filter_post_update_tags( $tags )
	{
		$tags = trim($tags, '/, ');
		$tags = preg_replace_callback('/[^,]+\/[^,]*/',
				create_function('$matches',
					'$match = trim($matches[0], "/");' .
					'return !$match ? "" : $match . "," . str_replace("/",",",$match);'
				), $tags);
		return $tags;
	}
	
	/**
	 * filters display of tags for posts to hide any that begin with "@" from display
	 **/
	public function filter_post_tags_out( $tags )
	{
		return $tags;
		$hierarchicaltags = array_filter($tags, create_function('$a', 'return strpos($a, "/") !== false;'));
		$tags = array_filter($tags, create_function('$a', 'return strpos($a, "/") === false;'));

//		foreach ($hierarchicaltags as $tag) {
//			$tags = array_merge($tags, explode($tag));
//		}

		$tags = Format::tag_and_list($tags);
		return $tags;
	}

	/**
	 * Displays a list of all tags used on the site except those begining with "@" as a comma seperated linked list.
	 
	public function magic_site_tags()
	{
		$tagcount= 0;
		foreach(DB::get_results('SELECT * FROM ' . DB::table('tags'). ' ORDER BY tag_text ASC') as $tag) {
			if (substr($tag->tag_text, 0, 1)== "@") {continue;}
			if ($tagcount!= 0) {echo ", ";}
			echo "<a href=\"" . URL::get('display_posts_by_tag', 'tag=' . $tag->tag_slug) . "\">{$tag->tag_text}</a>";
			$tagcount++;
		}	
	}
	 **/
}

?>
