<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));

echo "=== 1. Main Page ===\n";
echo "Main page: " . FillThisImage::BASE_URL . "\n\n";

echo "=== 2. Images (ImageController) ===\n";

echo "2.1. Default image:\n";
echo $faker->imageUrl(0, 0) . "\n\n";

echo "2.2. Image with dimensions 800x600 and text \"TestText\":\n";
echo $faker->imageUrl(800, 600, null, false, "TestText", false, "png") . "\n\n";

echo "2.3. Image with single dimension 500 (500x500):\n";
echo $faker->imageUrl(500, 500, null, false, null, false, "png") . "\n\n";

echo "2.4. Image with random parameter:\n";
echo $faker->imageUrl(800, 600, null, true, null, false, "png") . "\n\n";

echo "=== 3. Category Images ===\n";

echo "3.1. Placeholder for nonexistent category:\n";
echo $faker->imageUrl(800, 600, "nonexistent", false, null, false, "png") . "\n\n";

echo "3.2. Image from category \"anime\":\n";
echo $faker->imageUrl(0, 0, "anime", false, null, false, "png") . "\n\n";

echo "3.3. Image from category \"anime\" with dimensions 800x600:\n";
echo $faker->imageUrl(800, 600, "anime", false, null, false, "png") . "\n\n";

echo "3.4. Image from category \"all\":\n";
echo $faker->imageUrl(0, 0, "all", false, null, false, "png") . "\n\n";

echo "3.5. Image from category \"all\" with dimensions 800x600:\n";
echo $faker->imageUrl(800, 600, "all", false, null, false, "png") . "\n\n";

echo "=== 4. Categories Endpoints ===\n";
echo "4.1. List of categories: " . FillThisImage::BASE_URL . "/categories" . "\n";
echo "4.2. Images for category \"anime\": " . FillThisImage::BASE_URL . "/categories/anime/images" . "\n\n";

echo "=== 5. Font Parameter Examples ===\n";
$fonts = [
    "lato",
    "lora",
    "montserrat",
    "noto-sans",
    "open-sans",
    "oswald",
    "playfair-display",
    "poppins",
    "pt-sans",
    "raleway",
    "roboto",
    "source-sans-pro"
];
foreach ($fonts as $font) {
    echo "Font {$font}:\n";
    echo $faker->imageUrl(800, 600, null, false, "Custom", false, "png") . "&font={$font}" . "\n\n";
}

echo "=== 6. Download Examples ===\n";
$imagePath = $faker->image(__DIR__, 800, 600, null, true, true, "TestImage", false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath . "\n";
} else {
    echo "Error downloading image.\n";
}
