<?php
require 'vendor/autoload.php';
$now = \Carbon\Carbon::parse('08:15');
$target = \Carbon\Carbon::parse('08:00');
echo $now->diffInMinutes($target, false) . "\n";
echo $target->diffInMinutes($now, false) . "\n";
