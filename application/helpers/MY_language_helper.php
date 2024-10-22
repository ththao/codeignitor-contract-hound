<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

function tl($line, $for = '', $attributes = array()) {
    $sNewLine = lang($line);
    if (!empty($sNewLine)) {
        return $sNewLine;
    }

    return $line;
}

function tle($line, $for = '', $attributes = array()) {
    echo tl($line, $for, $attributes);
}
