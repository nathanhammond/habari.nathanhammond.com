<?php

/**
 * GoogleCodePrettify Class
 *
 **/

class GoogleCodePrettify extends Plugin
{
	private $config = array();
	private $class_name = '';
	private $default_options = array (
		'color_scheme' => 'google'
	);

	public function info()
	{
		return array(
			'name' => 'Google Code Prettify',
			'version' => '0.3-pre',
			'url' => 'http://blog.bcse.info/',
			'author' => 'Joel Lee',
			'authorurl' => 'http://blog.bcse.info/',
			'license' => 'Apache License 2.0',
			'description' => 'A Javascript module and CSS file that allows syntax highlighting of source code snippets in an html page.'
			);
	}

	/**
	 * Add update beacon support
	 **/
	public function action_update_check()
	{
	 	Update::add('Google Code Prettify', 'dc7d7984-ff24-46ea-b37a-1c26a6f17938', $this->info->version);
	}

	/**
	 * On plugin init, add the template included with this plugin to the available templates in the theme
	 */
	public function action_init()
	{
		$this->class_name = strtolower(get_class($this));
		foreach ($this->default_options as $name => $value) {
			$this->config[$name] = Options::get($this->class_name . '__' . $name);
		}
		$this->load_text_domain($this->class_name);
	}

	/**
	 * On plugin activation, set the default options
	 */
	public function action_plugin_activation($file)
	{
		if (realpath($file) === __FILE__) {
			$this->class_name = strtolower(get_class($this));
			foreach ($this->default_options as $name => $value) {
				$current_value = Options::get($this->class_name . '__' . $name);
				if (is_null($current_value)) {
					Options::set($this->class_name . '__' . $name, $value);
				}
			}
		}
	}

	/**
	 * Add actions to the plugin page for this plugin
	 * @param array $actions An array of actions that apply to this plugin
	 * @param string $plugin_id The string id of a plugin, generated by the system
	 * @return array The array of actions to attach to the specified $plugin_id
	 **/
	public function filter_plugin_config($actions, $plugin_id)
	{
		if ($plugin_id === $this->plugin_id()) {
			$actions[] = _t('Configure', $this->class_name);
		}

		return $actions;
	}

	/**
	 * Respond to the user selecting an action on the plugin page
	 * @param string $plugin_id The string id of the acted-upon plugin
	 * @param string $action The action string supplied via the filter_plugin_config hook
	 **/
	public function action_plugin_ui($plugin_id, $action)
	{
		if ($plugin_id === $this->plugin_id()) {
			switch ($action) {
				case _t('Configure', $this->class_name):
					$ui = new FormUI($this->class_name);
					$ui->append('select', 'color_scheme', 'option:' . $this->class_name . '__color_scheme', _t('Color Scheme', $this->class_name), self::get_color_schemes());
					// When the form is successfully completed, call $this->updated_config()
					$ui->append('submit', 'save', _t('Save', $this->class_name));
					$ui->set_option('success_message', _t('Options saved', $this->class_name));
					$ui->out();
					break;
			}
		}
	}

	private static function get_color_schemes()
	{
		$color_scheme_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'color-schemes' . DIRECTORY_SEPARATOR;
		$files = Utils::glob($color_scheme_dir . '*.css');
		$color_schemes = array();
		foreach ($files as $color_scheme) {
			$name = basename($color_scheme, '.css');
			$color_schemes[$name] = $name;
		}

		return $color_schemes;
	}

	/**
	 * Returns true if plugin config form values defined in action_plugin_ui should be stored in options by Habari
	 * @return bool True if options should be stored
	 **/
	public function updated_config($ui)
	{
		return true;
	}

	function theme_header()
	{
		Stack::add('template_stylesheet', array($this->get_url(TRUE) . 'color-schemes/' . $this->config['color_scheme'] . '.css', 'all'), $this->class_name);
	}

	function theme_footer()
	{
		Stack::add('template_footer_javascript', $this->get_url(TRUE) . 'prettify.js', $this->class_name);
	}

}

?>