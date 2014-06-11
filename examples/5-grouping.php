<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("/tmp/*");

// Group all files by its filesize.

### Plain PHP: ###
$data = array();
foreach($files as $file) {
    $currentSize = filesize($file);
    $data[] = array("name" => $file, "size" => $currentSize);
}

uasort($data, function($a, $b) {
    $as = $a['size'];
    $bs = $b['size'];
    if($as == $bs) { return 0; }
    else return $as < $bs ? 1 : -1;
});

$grouped = array();
foreach($data as $x)
{
    if(isset($grouped[$x['size']])) {
        $grouped[$x['size']][] = $x;
    }
    else {
        $grouped[$x['size']] = array($x);
    }
}

foreach($grouped as $key => $value) {
    echo $key . " (" . count($value) . ")" . "<br />";
    foreach($value as $file) {
        echo " -" . $file["name"] . "<br>";
    }
};

### Linq: ###

echo "<br/><br> Linq: <br /><br>";

$linq = Linq::from($files)
    ->select(function($x) { return array("name" => $x, "size" => filesize($x)); })
    ->orderByDescending(function($x) { return $x['size']; })
    ->groupBy(function($x) { return $x['size']; })
    ->orderByDescending(function($x) { return $x->count(); })
    ->each(function($x) {
        echo $x->key() . " (" . $x->count() . ")" . "<br />";
        $x->each(function($y) { echo " -" . $y["name"] . "<br>"; });
    });