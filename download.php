<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));

$imagePath = $faker->image(__DIR__, 800, 600, null, true, false, "TestImage", false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath . "\n";
} else {
    echo "Error downloading image.\n";
}

$imagePath = $faker->image(__DIR__, 0, 0, "fashion", true, true, null, false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath . "\n";
} else {
    echo "Error downloading image.\n";
}