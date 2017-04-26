<?php
/**
 * Plugin Name: TNA Cloud
 * Plugin URI: https://github.com/nationalarchives/tna-cloud-prototype
 * Description: The National Archives Cloud plugin
 * Version: 0.1
 * Author: The National Archives
 * Author URI: https://github.com/nationalarchives
 * License: GPL2
 */

/* Included functions */
include 'functions.php';

add_action( 'publish_page', 'render_page_as_html' );
