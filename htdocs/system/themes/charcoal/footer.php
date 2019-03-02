	</div>
	<div id="bottom-secondary">
		<div id="tags"><?php if (Plugins::is_loaded('tagcloud')) $theme->tag_cloud(); else $theme->show_tags();?></div>
	</div>
	<div id="footer">
		<p>
			<?php printf( _t('%1$s is powered by %2$s'), Options::get('title'),' <a
			href="http://www.habariproject.org/" title="Habari">Habari ' . Version::HABARI_VERSION  . '</a>' ); ?> - 
			<a href="<?php URL::out( 'atom_feed', array( 'index' => '1' ) ); ?>"><?php _e( 'Atom Entries' ); ?></a><?php _e( ' and ' ); ?>
			<a href="<?php URL::out( 'atom_feed_comments' ); ?>"><?php _e( 'Atom Comments' ); ?></a>
		</p>
		<style>.footerlinks li { display: inline; } .footerlinks li + li:before {content:"- "}</style>
		<ul class="footerlinks" style="list-style: none;">
			<li><a href="https://github.com/nathanhammond" rel="me">GitHub</a></li>
			<li><a href="http://twitter.com/nathanhammond" rel="me">Twitter</a></li>
			<li><a href="http://www.linkedin.com/in/nathanhammond" rel="me">LinkedIn</a></li>
			<li><a href="http://news.ycombinator.com/user?id=nathanhammond" rel="me">Hacker News</a></li>
			<li><a href="https://plus.google.com/102233935792815682741" rel="me">Google+</a></li>
			<li><a href="http://www.facebook.com/nathanhammond" rel="me">Facebook</a></li>
			<li><a href="https://foursquare.com/nathanhammond" rel="me">Foursquare</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>
</div>
<?php $theme->footer(); ?>
</body>
</html>
