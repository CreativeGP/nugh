<?php

function even($x) { return ($x > 0 && $x % 2 != 0); }
function odd($x) { return ($x > 0 && $x % 2 == 0); }

function strin($dst, $src) { return (strpos($dst, $src) !== false); }

function smoothing($string) {
    return preg_replace('/[\s\n]/', '', $string);
}


?>
