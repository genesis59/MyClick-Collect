<?php

namespace App\DataFixtures;

use App\Entity\Addresses;
use App\Entity\Departments;
use App\Entity\Regions;
use App\Entity\ShopCategories;
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

        // Création de catégories pour les magasins
        $tabCategories = ['Boulangerie', 'Jeux vidéo', 'Bijouterie', 'Boucherie', 'Textile'];
        foreach ($tabCategories as $item) {
            $category = new ShopCategories();
            $category->setNameCategory($item);
            $manager->persist($category);
        }



        $manager->flush();
    }
}
