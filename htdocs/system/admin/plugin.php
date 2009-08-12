<?php if ( $plugin['debug'] ): ?>

<div class="item plugin clear">
	<div class="head">
		<p><?php printf( _t('The plugin file %s had syntax errors and could not load.'), $plugin['file'] ); ?></p>
		<div style="display:none;" id="error_<?php echo $plugin['plugin_id']; ?>"><?php echo $plugin['error']; ?></div>
			<ul class="dropbutton">
				<li><a href="#" onclick="$('#error_<?php echo $plugin['plugin_id']; ?>').show();"><?php _e('Show Error'); ?></a></li>
			</ul>
	</div>
</div>

<?php elseif ( !isset($plugin['info']) ): ?>

<div class="item plugin clear">
	<div class="head">
		<p><?php printf( _t('The plugin file %s is a legacy plugin, and does not include an XML info file.'), $plugin['file'] ); ?></p>
	</div>
</div>

<?php else: ?>

<div class="item plugin clear" id="plugin_<?php echo $plugin['plugin_id']; ?>">
	<div class="head">
		<a href="<?php echo $plugin['info']->url; ?>" class="plugin"><?php echo $plugin['info']->name; ?> <span class="version"><?php echo $plugin['info']->version; ?></span></a>
		<span class="dim"><?php _e('by'); ?></span>

		<?php
		$authors = array();
		foreach ( $plugin['info']->author as $author ) {
			$authors[] = isset( $author['url'] ) ? '<a href="' . $author['url'] . '">' . $author . '</a>' : $author;
		}
		echo Format::and_list( $authors, '<span class="dim">, </span>', '<span class="dim">' . _t( ' and ' ) . '</span>');
		?>

		<?php if ( isset($plugin['help']) ): ?>
		<a class="help" href="<?php echo $plugin['help']['url']; ?>">?</a>
		<?php endif; ?>

		<ul class="dropbutton">
			<?php foreach ( $plugin['actions'] as $plugin_action => $action ) : ?>
			<li><a href="<?php echo htmlspecialchars( $action['url'], ENT_COMPAT, 'UTF-8' ); ?>"><?php echo $action['caption']; ?></a></li>
			<?php endforeach; ?>
		</ul>

		<?php if ( isset($plugin['update']) ): ?>
		<ul class="dropbutton alert">
			<li><a href="#"><?php _e('v1.1 Update Available Now'); ?></a></li>
		</ul>
		<?php endif; ?>

		<?php if ( $plugin['active'] != TRUE && isset($plugin['missing']) ): ?>
		<p><?php _e('This plugin cannot be activated because the following features were not present:'); ?></p>
		<ul>
			<?php foreach ( $plugin['missing'] as $feature => $url): ?>
			<li><?php echo ($url != '') ? sprintf('<a href="%s">%s</a>', $url, $feature) : $feature; ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

	</div>

	<p class="description"><?php echo $plugin['info']->description; ?></p>

	<div class="pluginhelp"<?php if ( $helpaction == '_help' ): ?> class="active"<?php endif; ?>>
		<?php Plugins::act( 'plugin_ui', $plugin['plugin_id'], '_help' ); ?>
	</div>

	<?php if ( isset($this->engine_vars['configure']) && ($configure == $plugin['plugin_id']) ): ?>
	<div id="pluginconfigure">
		<?php Plugins::act( 'plugin_ui', $configure, $configaction ); ?>
		<a class="link_as_button" href="<?php URL::out( 'admin', 'page=plugins' ); ?>"><?php _e('Close'); ?></a>
	</div>
	<?php endif; ?>

</div>

<?php endif; ?>
