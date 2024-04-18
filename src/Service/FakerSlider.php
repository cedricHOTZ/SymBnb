<?php

namespace App\Service;

use Faker\Factory;

class FakerSlider
{
    private $unsplashService;

    public function __construct(UnsplashService $unsplashService)
    {
        $this->unsplashService = $unsplashService;
    }

    public function generateFakeData(int $count = 3): array
    {
        $faker = Factory::create();
        $imageUrls = $this->unsplashService->getRandomImageUrls(30);

        $images = [];
        for ($i = 0; $i < $count; $i++) {
            $randomImageIndex = mt_rand(0, count($imageUrls) - 1);
            $images[] = $imageUrls[$randomImageIndex];
        }

        return $images;
    }
}
