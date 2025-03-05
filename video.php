<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));

echo "Video URL " . $faker->url('video') . "\n\n";
echo "Url: " . $faker->url() . "\n\n";