<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Country;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    const COUNTRIES = [
        'Germany' => ['DE', 19],
        'Italy' => ['IT', 22],
        'Greece' => ['GR', 24]
    ];

    const PRODUCTS = [
        'headphone' => 100,
        'case' => 20
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::COUNTRIES as $country => $value) {
            $countryObj = new Country();
            $countryObj->setName($country);
            $countryObj->setTaxCode($value[0]);
            $countryObj->setTaxPercentage($value[1]);

            $manager->persist($countryObj);
        }

        foreach (self::PRODUCTS as $name => $price) {
            $productObj = new Product();
            $productObj->setName($name);
            $productObj->setPrice($price);

            $manager->persist($productObj);
        }

        $manager->flush();
    }
}
