<?php

/**
 * Plugin Name: Dionnie WP
 * Description: A simple plugin made for Dionnie Theme.
 * Version: 1.0
 * Author: Mark Dionnie Bulingit
 * License: GPL2 
 * Licese URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dionnie-wp
 * Domain Path: /languages
 */
if (!defined('WPINC')) {
    die;
}

require_once('dist/lib/helpers.php');
require_once('dist/lib/enqueue-assets.php');
include_once('dist/lib/books.php');
