<?php

/**
 * Plugin Name: _pluginname
 * Version: 1.0
 * Author: Mark Dionnie Bulingit
 * License: GPL2 
 * Licese URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: _pluginname
 * Domain Path: /languages
 */
if (!defined('WPINC')) {
    die;
}

include_once(plugin_dir_path(__FILE__) . 'dist/lib/helpers.php');

require_once('dist/lib/helpers.php');
require_once('dist/lib/enqueue-assets.php');
include_once('dist/lib/books.php');
