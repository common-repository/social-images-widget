<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
 
// delete options
$option_name = 'social-images-widget';
delete_option($option_name);
 
// for site options in Multisite
delete_site_option($option_name);

?>