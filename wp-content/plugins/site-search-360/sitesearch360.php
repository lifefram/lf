<?php

/*
Plugin Name: Site Search 360
Plugin URI: https://sitesearch360.com
Description: Site Search 360 enhances and improves your standard WordPress search with search suggests, autocompletion, semantic search, and a whole lot of customization. Also, you'll be amazed of how much faster you get relevant search results.
Author: SEMKNOX GmbH
Version: 1.1.28
Author URI: https://sitesearch360.com
Text Domain: site-search-360
Domain Path: /languages/
*/

define( 'SITESEARCH360_VERSION', '1.1.28' );

require_once 'class-sitesearch360-widget.php';
require_once 'class-sitesearch360-client.php';
require_once 'class-sitesearch360-plugin.php';
require_once 'class-sitesearch360-indexer.php';

$sitesearch360_plugin = new SiteSearch360Plugin();