<?php

/*
Plugin Name: WP Protector
Plugin URI:
Description: Protects some important directories from virus and malware.
Version: 0.1
Author: Fabio Rotondo
Author URI: https://fsoft.dev
License: GPLv2
*/

add_action('wp_head', 'wp_protector_header');

function wp_protector_header()
{
	echo '<!-- WP Protector -->';
}

register_activation_hook(__FILE__, 'wp_protector_options');

function wp_protector_options()
{
	if (get_option('wp_protector_themes') === false) {
		add_option('wp_protector_themes', '0');
	}

	if (get_option('wp_protector_plugins') === false) {
		add_option('wp_protector_plugins', '0');
	}

	if (get_option('wp_protector_uploads') === false) {
		add_option('wp_protector_uploads', '0');
	}
}

add_action('admin_menu', 'wp_protector_settings_menu');

function wp_protector_settings_menu()
{
	add_options_page(
		'WP Protector',  // page title
		'WP Protector',  // menu title
		'manage_options',  // capability
		'wp_protector_settings',  // menu slug
		'wp_protector_settings_page' // function
	);
}

function recurse_get_all_dirs($base_path)
{
	// echo "<pre> RECURSE: $base_path</pre>";

	// recursively returns all directories contained in $base_path
	$dirs = array();
	$dir = opendir($base_path);
	while (false !== ($file = readdir($dir))) {
		if (is_dir($base_path . $file) && $file != '.' && $file != '..') {
			$dirs[] = $base_path . $file . '/';
			$dirs = array_merge($dirs, recurse_get_all_dirs($base_path . $file . '/'));
		}
	}
	closedir($dir);

	$dirs[] = $base_path;
	return $dirs;
}

function change_dirs_permissions($dirs, $permission)
{
	// changes permissions of all directories in $dirs to $permission
	foreach ($dirs as $dir) {
		// echo "<pre>$dir - $permission</pre>";
		chmod($dir, $permission);
	}
}

function section_lock($lock, $base_dir)
{
	// echo "<pre>LOCK: $base_dir - $lock</pre>";
	if ($lock == '1')
		$mode = 0555;
	else
		$mode = 0755;

	$dirs = recurse_get_all_dirs($base_dir);
	// dump dirs in JSON format
	// echo json_encode($dirs);

	change_dirs_permissions($dirs, $mode);
}

function wp_protector_settings_page()
{
	$themes = get_option('wp_protector_themes');
	$plugins = get_option('wp_protector_plugins');
	$uploads = get_option('wp_protector_uploads');

	// check if method is POST
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		$themes = $_POST['wp_protector_themes'];
		$plugins = $_POST['wp_protector_plugins'];
		$uploads = $_POST['wp_protector_uploads'];

		update_option('wp_protector_themes', $themes);
		update_option('wp_protector_plugins', $plugins);
		update_option('wp_protector_uploads', $uploads);

		section_lock($themes, WP_CONTENT_DIR . '/themes/');
		section_lock($plugins, WP_CONTENT_DIR . '/plugins/');
		section_lock($uploads, WP_CONTENT_DIR . '/uploads/');
	}

?>
	<div class="wrap">
		<h1>WP Protector</h1>
		This plugin only works on <em>Linux</em> systems.
		<form method="post" action="">
			<table class="form-table">
				<tr>
					<td>
						<p>
							<label for="wp_protector_themes">
								<input type="checkbox" name="wp_protector_themes" id="wp_protector_themes" value="1" <?php if ($themes == '1') {
																															echo 'checked';
																														} ?> />
								<strong>Themes</strong>
								- If you enable this, you will not be able to add / remove themes or edit their files.
							</label>
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<p>
							<label for="wp_protector_plugins">
								<input type="checkbox" name="wp_protector_plugins" id="wp_protector_plugins" value="1" <?php if ($plugins == '1') {
																															echo 'checked';
																														} ?> />
								<strong>Plugins</strong>
								- If you enable this, you will not be able to add / remove plugins or edit their files.
							</label>
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<p>
							<label for="wp_protector_uploads">
								<input type="checkbox" name="wp_protector_uploads" id="wp_protector_uploads" value="1" <?php if ($uploads == '1') {
																															echo 'checked';
																														} ?> />
								<strong>Uploads</strong>
								- If you enable this, you will not be able to add / remove files from this directory.
							</label>
						</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
			</p>
		</form>
	</div>

<?php
}
