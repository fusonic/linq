<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("./samples/*");

### Plain PHP: ###

$sum = 0;
$i = 0;
$m = 0;
foreach($files as $file) {
    $currentSize = filesize($file);
    if($currentSize > 1024) {
       if($m < 2) {
           $m++;
           continue;
       }
       else if ($m > 5) {
            break;
       }
       $m++;
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