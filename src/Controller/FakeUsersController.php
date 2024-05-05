<?php

declare(strict_types=1);

namespace App\Controller;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FakeUsersController extends AbstractController
{
    private const DEFAULT_REGION = 'en_US';

    private const REGIONS = [
        self::DEFAULT_REGION => 'USA',
        'fr_FR' => 'France',
        'zh_CN' => 'China',
    ];

    #[Route('/', name: 'main', methods: ['GET'])]
    public function create(Request $request): Response
    {
        $selectedRegion = $request->query->get('region');
        if (array_key_exists($selectedRegion, self::REGIONS) === false) {
            $selectedRegion = self::DEFAULT_REGION;
        }

        $errors = 5;

        $faker = Factory::create($selectedRegion);
        $faker->seed(1);
        $fakerUser = [];
        for ($i = 1; $i <= 20; $i++) {
            $name = $faker->name;
            $address = $faker->address;
            $number = $faker->phoneNumber;

            if ($errors >= 0) {
                // for ($i = 1; $i <= $errors; $i++) {
                $field = $this->chooseField($faker, $name, $address, $number);
                $field = current($field);
                $algorithm = $this->chooseAlgorithm($faker);
                $position = (int)$faker->randomFloat(max: mb_strlen($field) - 1);


                switch ($algorithm) {
                    case 'add':
                        $field = mb_strcut($field, 0, $position).$faker->randomAscii.mb_strcut(
                            $field,
                            $position,
                        ). '('.$name.')';
                        break;
                    case 'remove':
                        $field = mb_strcut($field, 0, $position - 1).mb_strcut(
                                $field,
                                $position,
                            ). '('.$name.')';
                        break;
                }

            }

            // delete one symbol
//            $symbolToDelete = (int)$faker->randomFloat(max: mb_strlen($name) - 1);
            // if symbol to delete is equal to 0 then do not apply first mb_strcut
//            $name = mb_strcut($name, 0, $symbolToDelete - 1) . mb_strcut($name, $symbolToDelete) . '('.$name.')';
            // add one symbol
//            $positionToAdd = (int)$faker->randomFloat(max: mb_strlen($name) - 1);
//            $name = mb_strcut($name, 0, $positionToAdd) . $faker->randomAscii . mb_strcut($name, $positionToAdd) . '('.$name.')';
            // swap nearest symbols
            // TODO

            // 0. while errors > 0
            // 1. switch - choose field
            // 2. switch - choose algorithm
            // 3. execute algorithm
            // 4. decrease count of errors

//            $nameLength = 5;
//            $threshold = 2; // in percent (for example 30%)

            $options = ['add', 'remove', 'swap'];

//            dump($faker->randomElement($options));
            $fakerUser[] = [
                'id' => $i,
                'idUser' => $faker->uuid(),
                'name' => $name,
                'address' => $address,
                'number' => $number,
            ];
        }

        return $this->render('fakeUsers.html.twig', [
            'fakerUsers' => $fakerUser,
            'regions' => self::REGIONS,
            'selected_region' => self::REGIONS[$selectedRegion],
        ]);
    }

    public function chooseField($faker, $name, $address, $number): array
    {
        $fields = ['name', 'address', 'number'];
        $field = $faker->randomElement($fields);
        switch ($field) {
            case 'name':
                $field = ['name' => $name];
                break;
            case 'address':
                $field = ['address' => $address];
                break;
            case 'number':
                $field = ['number' => $number];
        }

        return $field;
    }

    public function chooseAlgorithm($faker): string
    {
        $algorithms = ['add', 'remove', 'swap'];

        return $faker->randomElement($algorithms);
    }
}
