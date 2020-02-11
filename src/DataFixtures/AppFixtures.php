<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Projet;
use App\Entity\Techno;
use App\Repository\TechnoRepository;
use Faker\Factory;

class AppFixtures extends Fixture
{
    const TECHNO = [
        'HTML5',
        'CSS3',
        'JavaScript'
    ];


    private function loadTechno( ObjectManager $manager ){
        foreach( $this::TECHNO AS $value){
            $techno = new Techno();
            $techno->setName($value);
            $this->addReference('techno_'.$value, $techno);
            $manager->persist($techno);
        }

        $manager->flush();
    }

    private function loadUser( ObjectManager $manager ){
        $faker = \Faker\Factory::create('fr_FR');

        for( $i = 0; $i <10; $i++){
            $projet =new Projet();
            $projet->setName($faker->sentence($nbWords = 6, $variableNbWords = true));
            $projet->setDescription($faker->text($maxNbChars = 150));
            $projet->setDate(new \DateTime($faker->date($format = 'Y-m-d', $max = 'now')));
            $projet->addTechno($this->getReference('techno_'.self::TECHNO[rand(0, 2)]));
            $manager->persist($projet);
        }

        $manager->flush();
    }

    public function load( ObjectManager $manager )
    {
        $this->loadTechno( $manager );
        $this->loadUser( $manager );

    }
}
