<?php
/**
 * TNA Cloud admin
 */

add_action('admin_menu', 'tna_cloud_menu');

function tna_cloud_menu() {
	add_menu_page( 'TNA cloud settings', 'TNA cloud', 'administrator', 'tna-cloud-admin-page', 'tna_cloud_admin_page', 'dashicons-cloud', 21  );
}

function tna_cloud_admin_page() {
	if (!current_user_can('administrator'))  {
		wp_die( __('You do not have sufficient pilchards to access this page.')    );
	}
	?>
	<div class="wrap tna-cloud">
		<h1>TNA Cloud</h1>
		<form method="post" action="admin.php?page=tna-cloud-admin-page" novalidate="novalidate">
			<?php
			wp_nonce_field('render_button_clicked');
			if (isset($_POST['submit-tna-cloud']) && check_admin_referer('render_button_clicked')) {

				get_all_pages_and_render();
			}
			?>
			<p class="submit">
				<input type="submit" name="submit-tna-cloud" id="submit" class="button button-primary" value="Render site to HTML">
			</p>
		</form>
	</div>
	<?php
}

function get_all_pages_and_render() {
	$pages = get_pages();
	echo '<ul>';
	foreach ( $pages as $page ) {
		render_page_as_html($page->ID);
		$option = '<li>';
		$option .= $page->post_title;
		$option .= '</li>';
		echo $option;
	}
	echo '</ul>';
	echo '<div class="notice"><p>Site rendered</p></div>';
}