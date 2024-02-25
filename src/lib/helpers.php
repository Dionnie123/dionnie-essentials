<?php
function _pluginname_prefix($text)
{
    // Check if the text starts with an underscore
    if (substr($text, 0, 1) === '_') {
        // Replace consecutive underscores with a single underscore
        $text = preg_replace('/_+/', '_', $text);
    } else {
        // Add a single underscore at the beginning
        $text = '_' . $text;
    }

    return $text;
}
