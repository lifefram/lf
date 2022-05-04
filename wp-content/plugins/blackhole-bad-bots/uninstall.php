<?php // Blackhole for Bad Bots - Uninstall Remove Options

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

global $wpdb;
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%blackhole_get_vars%'");

delete_option('bbb_options');
delete_option('bbb_badbots');