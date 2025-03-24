<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));

echo $faker->imageUrl(0, 0) . "\n\n";
echo $faker->imageUrl(0, 0, "fashion", false, null, false, "png") . "\n\n";
