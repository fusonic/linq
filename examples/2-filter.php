<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("/tmp/*");

// Calculate the average filesize of all files greater than 1024 bytes in a directory.

### Plain PHP: ###

$sum = 0;
$i = 0;
foreach($files as $file) {
    $currentSize = filesize($file);
    if($currentSize > 1024) {
        $sum += $currentSize;
        $i++;
    }
}
$avg = $sum / $i;

echo "Average: " . $avg;

### Linq: ###

$avgL = Linq::from($files)
    ->select(function($f) { return filesize($f); })
    ->where(function($fs) { return $fs > 1024; })
    ->average();

echo "<br/><br>Average Linq: " . $avgL;