<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));

echo "=== Main Page ===\n";
echo "Main page: " . FillThisImage::BASE_URL . "\n\n";

echo "1. Default image (no dimensions):\n";
echo $faker->imageUrl(0, 0) . "\n\n";

echo "2. Image with single dimension 500 (500x500):\n";
echo $faker->imageUrl(500, 500, null, false, null, false, "png") . "\n\n";

echo "3. Image with random word:\n";
echo $faker->imageUrl(800, 600, null, true, null, false, "png") . "\n\n";

echo "4. Placeholder for nonexistent category:\n";
echo $faker->imageUrl(0, 0, "nonexistent", false, null, false, "png") . "\n\n";

echo "5. Image from category \"fashion\":\n";
echo $faker->imageUrl(0, 0, "fashion", false, null, false, "png") . "\n\n";

echo "6. Image from category \"all\":\n";
echo $faker->imageUrl(0, 0, "all", false, null, false, "png") . "\n\n";

echo "7. Video URL:\n";
echo $faker->url('video') . "\n\n";

echo "8. Download image example:\n";
$imagePath = $faker->image(__DIR__, 800, 600, null, true, true, "TestImage", false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath . "\n";
} else {
    echo "Error downloading image.\n";
}

echo "\n9. Download image from category \"fashion\":\n";
$imagePath = $faker->image(__DIR__, 0, 0, "fashion", true, true, null, false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath . "\n";
} else {
    echo "Error downloading image.\n";
}