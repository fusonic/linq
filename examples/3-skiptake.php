<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("/tmp/*");

// Calculate the average filesize of all files greater than 1024 bytes in a directory
// but skip the very first 2 files and then take only 4 files.

### Plain PHP: ###

$sum = 0;
$i = 0;
$y = 0;
foreach($files as $file) {
    $currentSize = filesize($file);
    if($currentSize > 1024) {
       if($y < 2) {
           $y++;
           continue;
       }
       else if ($y > 5) {
            break;
       }
       $y++;
       $sum += $currentSize;
       $i++;
    }
}
$avg = $sum / $i;

echo "Average: " . $avg;

### Linq: ###

$avgL = Linq::from($files)
    ->select(function($x) { return filesize($x); })
    ->where(function($x) { return $x > 1024; })
    ->skip(2)
    ->take(4)
    ->average();

echo "<br/><br>Average Linq: " . $avgL;