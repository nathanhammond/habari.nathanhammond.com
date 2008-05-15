<?php include('header.php');?>

<div class="container"><!-- TODO: implement the tags loupe -->
	<span class="older pct10"><a href="#">&laquo; Older</a></span>
	<span class="currentposition pct15 minor">0-20 of 480</span>
	<span class="search pct50"><input type="search" placeholder="Type and wait to search tags" autosave="habaricontent" results="10"></input></span>
	<span class="nothing pct15">&nbsp;</span>
	<span class="newer pct10"><a href="#">Newer &raquo;</a></span>
</div>
<?php
	$tags= $available_tags;
	//what's the max count?
	//ugly! and probably needs to be a Tags method or something
	$max=0;
	foreach ($tags as $tag){if ($max < $tag->count) $max=$tag->count;}
?>
<div class="container tags">
<?php foreach ($tags as $tag) { ?>
	<a href="#" id="<?php echo 'tag_' . $tag->id ?>" class="tag wt<?php echo round(($tag->count * 10)/$max); ?>"><span><?php echo $tag->tag; ?></span><sup><?php echo $tag->count; ?></sup></a>
<?php } ?>
		<ul class="dropbutton">
			<li><a href="#">Select Visible</a></li>
			<li><a href="#">Select All</a></li>
			<li><a href="#">Deselect All</a></li>
		</ul>
</div>

<div class="container tags transparent">

	<div class="tags controls">
		<span class="checkboxandselected pct15">
			<span class="selectedtext minor none">None selected</span>
		</span>
		<span><input type="button" value="Delete" class="deletebutton"></input></span>
		<span class="or pct5">or</span>
		<span class="renamecontrols">
			<input type="text" class="renametext"></input>
			<input type="button" value="Rename" class="renamebutton"></input>
		</span>
		<input type="hidden" id="nonce" name="nonce" value="<?php echo $wsse['nonce']; ?>">
		<input type="hidden" id="timestamp" name="timestamp" value="<?php echo $wsse['timestamp']; ?>">
		<input type="hidden" id="PasswordDigest" name="PasswordDigest" value="<?php echo $wsse['digest']; ?>">
	</div>
</div>

<script type="text/javascript">
tagManage.delete= function() {
	spinner.start();

	selected= $('.tags .tag.selected');
	if ( selected.length == 0 ) {
		humanMsg.displayMsg('You need to select some tags before you can delete them.');
		return;
	}
	var query= {}

	selected.each(function() {
		query[$(this).attr('id')]= 1;
	});

	query['action']= 'delete';
	query['timestamp']= $('input#timestamp').attr('value');
	query['nonce']= $('input#nonce').attr('value');
	query['digest']= $('input#PasswordDigest').attr('value');
	$.post(
		"<?php echo URL::get('admin_ajax', array('context' => 'tags')); ?>",
		query,
		function(msg) {
			spinner.stop();
			//TODO When there's a loupe, update it
			//timelineHandle.updateLoupeInfo();
			selected.remove();
			humanMsg.displayMsg(msg);
		},
		'json'
	);
};
tagManage.rename= function() {
	//spinner.start();

	selected = $('.tags .tag.selected');
	master = $('.tags.controls input.renametext').val().trim();
	//TODO This isn't implemented yet
	//TODO if master is selected, unselect it
	//TODO update the number of posts the master has

	if ( selected.length == 0 ) {
		humanMsg.displayMsg('You need to select some tags before you can rename them.');
		return;
	}
	else if ( master == '' ) {
		humanMsg.displayMsg('You need to enter a new tag to rename tags.');
		return;
	}
	var query= {}

	selected.each(function() {
		query[$(this).attr('id')]= 1;
	});

	query['action']= 'rename';
	query['timestamp']= $('input#timestamp').attr('value');
	query['nonce']= $('input#nonce').attr('value');
	query['digest']= $('input#PasswordDigest').attr('value');
	//$.post(
	//	"<?php echo URL::get('admin_ajax', array('context' => 'tags')); ?>",
	//	query,
	//	function(msg) {
	//		spinner.stop();
	//		//TODO When there's a loupe, update it
	//		//timelineHandle.updateLoupeInfo();
	//		selected.remove();
	//		humanMsg.displayMsg(msg);
	//	},
	//	'json'
	//);
};
</script>

<?php include('footer.php');?>
