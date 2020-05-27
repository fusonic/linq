<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("/tmp/*");

// Group all files by its filesize.

### Plain PHP: ###
$data = [];
foreach($files as $file) {
    $currentSize = filesize($file);
    $data[] = ["name" => $file, "size" => $currentSize];
}

uasort($data, function($a, $b) {
    $as = $a['size'];
    $bs = $b['size'];
    if($as == $bs) { return 0; }
    else return $as < $bs ? 1 : -1;
});

$grouped = [];
foreach($data as $x)
{
    if(isset($grouped[$x['size']])) {
        $grouped[$x['size']][] = $x;
    }
    else {
        $grouped[$x['size']] = [$x];
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
    ->select(fn($x) => ["name" => $x, "size" => filesize($x)])
    ->orderByDescending(fn($x) => $x['size'])
    ->groupBy(fn($x) => $x['size'])
    ->orderByDescending(fn($x) => $x->count())
    ->each(fn($x) => {
        echo $x->key() . " (" . $x->count() . ")" . "<br />";
        $x->each(fn($y) { echo " -" . $y["name"] . "<br>"; });
    });