<?php

namespace App\DataFixtures;

use App\Entity\Addresses;
use App\Entity\Departments;
use App\Entity\Regions;
use App\Entity\ShopCategories;
use App\Entity\Shops;
use App\Entity\Towns;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Ajout du Factory faker
        $faker = Faker\Factory::create('fr_FR');
        // Récupération des données
        $listRegions = json_decode(file_get_contents('infoJson/regions.json'));
        $listDepartments = json_decode(file_get_contents('infoJson/departments.json'));
        $listTowns = json_decode(file_get_contents('infoJson/cities.json'));
        // Création de tableau pour création d'autres entités nécéssitant ces objets
        $tabTown = [];
        $tabUsers = [];
        $tabTraders = [];
        $tabCategoriesObject = [];
        //  Création des régions avant insertion dans la base de données
        foreach ($listRegions as $item) {
            $region = new Regions();
            $region->setNameRegion($item->name)
                ->setCodeRegion($item->code);
            $manager->persist($region);
            //  Création des départements avant insertion dans la base de données
            foreach ($listDepartments as $item) {

                if ($region->getCodeRegion() == $item->region_code) {
                    $department = new Departments();
                    $department->setNameDepartment($item->name)
                        ->setCodeDepartment($item->code)
                        ->setRegion($region);
                    $manager->persist($department);
                    //  Création des villes avant insertion dans la base de données
                    foreach ($listTowns as $item) {
                        if ($department->getCodeDepartment() == $item->department_code) {
                            $town = new Towns();
                            $town->setNameTown($item->name)
                                ->setZipCode($item->zip_code)
                                ->setDepartment($department);
                            $manager->persist($town);
                            // Ajout des villes au tableau
                            array_push($tabTown, $town);
                        }
                    }
                }
            }
        }
        // Création d'un utilisateur admin
        $user = new User();
        $user->setFirstName('admin')
            ->setLastName($faker->lastName)
            ->setPhoneNumber($faker->phoneNumber)
            ->setEmail('admin@mail.com')
            ->setRoles(['ROLE_ADMIN', 'ROLE_TRADER', 'ROLE_USER'])
            ->setPassword($this->userPasswordEncoder->encodePassword($user, 'admin'))
            ->setTown($faker->randomElement($tabTown))
            ->setStreetNumber($faker->buildingNumber)
            ->setStreet($faker->streetName);
        $manager->persist($user);

        // Création de quelques utilisateurs
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName(null))
                ->setLastName($faker->lastName)
                ->setPhoneNumber($faker->phoneNumber)
                ->setEmail($faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->userPasswordEncoder->encodePassword($user, 'root'))
                ->setTown($faker->randomElement($tabTown))
                ->setStreetNumber($faker->buildingNumber)
                ->setStreet($faker->streetName);
            // Ajout des users au tableau
            array_push($tabUsers, $user);
            $manager->persist($user);
        }

        // Création de quelques commerçants
        for ($i = 0; $i < 100; $i++) {
            $trader = new User();
            $trader->setFirstName($faker->firstName(null))
                ->setLastName($faker->lastName)
                ->setPhoneNumber($faker->phoneNumber)
                ->setEmail($faker->email)
                ->setRoles(['ROLE_USER','ROLE_TRADER'])
                ->setPassword($this->userPasswordEncoder->encodePassword($trader, 'root'))
                ->setTown($faker->randomElement($tabTown))
                ->setStreetNumber($faker->buildingNumber)
                ->setStreet($faker->streetName);
            // Ajout des traders au tableau
            array_push($tabTraders, $trader);
            $manager->persist($trader);
        }

        // Création de catégories pour les magasins
        $tabCategories = ['Boulangerie', 'Jeux vidéo', 'Bijouterie', 'Boucherie', 'Textile'];
        foreach ($tabCategories as $item) {
            $category = new ShopCategories();
            $category->setNameCategory($item);
            // Ajout des objets catégories au tableau
            array_push($tabCategoriesObject, $category);
            $manager->persist($category);
        }
        // Création de magasin 3/trader pour 50 trader
        for($i = 0; $i <50; $i++){
            $user = $faker->randomElement($tabTraders);
            for($j = 1; $j <=3; $j++){
                $shop = new Shops();
                $shop->setNameShop($faker->company)
                    ->setTrader($user)
                    ->setCategory($faker->randomElement($tabCategoriesObject))
                    ->setTown($faker->randomElement($tabTown))
                    ->setPicture($j . '.jpg')
                    ->setPresentation($faker->paragraph(3,true))
                    ->setStreetNumber($faker->buildingNumber)
                    ->setStreet($faker->streetName)
                    ->setEmail($faker->email)
                    ->setPhoneNumber($faker->phoneNumber);
                
                $manager->persist($shop);
            }
        }
        $manager->flush();
    }
}
