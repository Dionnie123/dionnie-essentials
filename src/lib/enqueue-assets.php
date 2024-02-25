<?php

function _pluginname_assets()
{

    wp_enqueue_style('_pluginname-stylesheet',  get_template_directory_uri() . '/dist/css/bundle.css'); //this must be the last css to be added
    wp_enqueue_script('_pluginname-scripts',  get_template_directory_uri() . '/dist/js/bundle.js', array('jquery'), '1.0.0', true); //this must be the last js to be added
}
add_action('wp_enqueue_scripts', '_pluginname_assets');


function _pluginname_admin_assets()
{
    wp_enqueue_style('_pluginname-admin-stylesheet',  get_template_directory_uri() . '/dist/css/admin.css'); //this must be the last css to be added
    wp_enqueue_script('_pluginname-admin-scripts',  get_template_directory_uri() . '/dist/js/admin.js', array('jquery'), '1.0.0', true); //this must be the last js to be added
}
add_action('admin_enqueue_scripts', '_pluginname_admin_assets');

function _pluginname_customize_preview_js()
{
    wp_enqueue_script('_pluginname-cutomize-preview', get_template_directory_uri() . '/dist/js/customize-preview.js', array('customize-preview', 'jquery'), '1.0.0', true);

    include(get_template_directory() . '/dist/lib/inline-css.php');
    wp_localize_script('_pluginname-cutomize-preview', '_pluginname', array('inline-css' => $inline_styles_selectors));
}

add_action('customize_preview_init', '_pluginname_customize_preview_js');
