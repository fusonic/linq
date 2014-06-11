<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("/tmp/*");

// Calculate the average filesize of all files in a directory.

### Plain PHP: ###

$sum = 0;
$i = 0;
foreach($files as $file) {
    $sum += filesize($file);
    $i++;
}
$avg = $i == 0 ? 0 : $sum / $i;

echo "Average: " . $avg;

### Linq: ###

$avgL = Linq::from($files)
    ->select(function($f) { return filesize($f); })
    ->average();

echo "<br/><br>Average Linq: " . $avgL;