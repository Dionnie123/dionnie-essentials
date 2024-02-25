<?php

function _pluginname_private_metakey($text)
{
    // Check if the text starts with an underscore
    if (substr($text, 0, 1) === '_') {
        // Replace consecutive underscores with a single underscore
        $text = preg_replace('/_+/', '_', $text, 1);
    } else {
        // Add a single underscore at the beginning
        $text = '_' . $text;
    }

    return $text;
}
