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

function getRenderedHTML($path, $pre_path)
{
	$content = file_get_contents($path);

	$content = str_replace( site_url(), '/wp-content/uploads/html/'.$pre_path, $content );

	return $content;
}

function render_page_as_html( $ID, $post ) {

	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$permalink = get_permalink( $ID );

	if ( $permalink !== $protocol.$_SERVER['HTTP_HOST'].'/' ) {
		$path_dir = str_replace( $protocol.$_SERVER['HTTP_HOST'].'/', '', rtrim($permalink, '/') );
	} else {
		$path_dir = '';
	}

	$slug = get_post_field( 'post_name', get_post() );

	$path_dir_noslug = rtrim(str_replace( $slug, '', $path_dir), "/");

	$path_parts = explode('/', $path_dir);

	$html = getRenderedHTML($permalink, $path_dir_noslug);

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
			$html,
			FS_CHMOD_FILE
		);
	}
	else
	{
		/* don't have direct write access. Prompt user with our notice */
		add_action('admin_notices', 'notice_function');
	}

}
