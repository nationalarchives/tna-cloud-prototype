<?php
/**
 * TNA Cloud functions
 *
 */

function notice_function() {
	?>
	<div class="notice">
		<p>Did not work!!!!</p>
	</div>
	<?php
}

function render_page_as_html( $ID, $post ) {

	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$permalink = get_permalink( $ID );

	$path_dir = str_replace( $protocol.$_SERVER['HTTP_HOST'].'/', '', rtrim($permalink, '/') );

	$path_parts = explode('/', $path_dir);

	$directory = ABSPATH . 'wp-content/uploads/html/' . $path_dir . '/';

	$access_type = get_filesystem_method();
	if($access_type === 'direct')
	{
		/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
		$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

		/* initialize the API */
		if ( ! WP_Filesystem($creds) ) {
			/* any problems and we exit */
			return false;
		}

		global $wp_filesystem;

		$counter = count($path_parts);

		$pre_path = '';

		$wp_filesystem->mkdir(ABSPATH . 'wp-content/uploads/html/');


		for ( $i=0 ; $i<=$counter ; $i++ ) {

			$wp_filesystem->mkdir(ABSPATH . 'wp-content/uploads/html/' . $pre_path . $path_parts[$i]);

			$pre_path .= $path_parts[$i] . '/';

		}

		/* do our file manipulations below */
		$wp_filesystem->put_contents(
			$directory.'index.html',
			$directory,
			FS_CHMOD_FILE
		);
	}
	else
	{
		/* don't have direct write access. Prompt user with our notice */
		add_action('admin_notices', 'notice_function');
	}





	/*
	 *
	 * $url = wp_nonce_url('themes.php?page=example','example-theme-options');
	if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
		return; // stop processing here
	}

	wp_mkdir_p($directory);

	if ( wp_mkdir_p( $directory ) )
	{
		echo "Folder $directory successfully created";
	}
	else
	{
		new WP_Error;
	}

	if ( file_exists('/wp-content/uploads/html/'.$path_dir) ) {
		echo "The file $path_dir exists ";
	} else {
		echo "The file $path_dir does not exist ";
	}

	$url = wp_nonce_url('post.php?post='.$ID, 'example-theme-options');
	if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
		return '!!!'; // stop processing here
	}

	if ( ! WP_Filesystem($creds) ) {
		request_filesystem_credentials($url, '', true, false, null);
		return '!!!';
	}

	global $wp_filesystem;
	$wp_filesystem->put_contents(
		'/tmp/example.txt',
		$directory,
		FS_CHMOD_FILE // predefined mode settings for WP files
	);
	*/

	// print_r($path_parts);
}