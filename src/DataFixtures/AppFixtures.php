<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\Games;
use App\Entity\Teams;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $teamNames = [
            ['name' => 'Real Madrid', 'image' => 'real-madrid.png'],
            ['name' => 'Barcelone', 'image' => 'barca.png'],
            ['name' => 'Manchester City', 'image' => 'manchester-city.png'],
            ['name' => 'Liverpool', 'image' => 'liverpool.png'],
            ['name' => 'Juventus', 'image' => 'juventus.png'],
            ['name' => 'Bayern Munich', 'image' => 'bayern.png'],
            ['name' => 'Paris Saint-Germain', 'image' => 'psg.png'],
            ['name' => 'Chelsea', 'image' => 'chelsea.png'],
            ['name' => 'Arsenal', 'image' => 'arsenal.png'],
            ['name' => 'Manchester United', 'image' => 'manchester-united.png']
        ];

        $teams = [];
        foreach ($teamNames as $teamData) {
            $team = new Teams();
            $team->setName($teamData['name']);
            $team->setPathImage('/images/' . $teamData['image']);
            $team->setCreatedAt(new \DateTimeImmutable('today'));
            $manager->persist($team);
            $teams[] = $team;
        }

        // Création de faux utilisateurs
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setCreatedAt(new \DateTimeImmutable('today'));
            $manager->persist($user);
            $users[] = $user;
        }

        // Création de faux matchs
        $games = [];
        for ($i = 0; $i < 20; $i++) {
            $game = new Games();
            $game->setTitle($faker->sentence);
            $dateMatch = $faker->dateTimeBetween('-1 month', 'now');
            $game->setDateMatch(new \DateTimeImmutable('@' . $dateMatch->getTimestamp()));
            $game->setEquipeDomicile($teams[array_rand($teams)]);
            $game->setEquipeExterieur($teams[array_rand($teams)]);
            $game->setScore($faker->numberBetween(0, 7) . ' - ' . $faker->numberBetween(0, 7));
            $game->setCreatedAt(new \DateTimeImmutable('@' . $dateMatch->getTimestamp()));
            $manager->persist($game);
            $games[] = $game;
        }

        // Création de faux avis
        for ($i = 0; $i < 25; $i++) {
            $avis = new Avis();
            $avis->setCommentaire($faker->paragraph);
            $avis->setUser($users[array_rand($users)]);
            $avis->setGame($games[array_rand($games)]);
            $dateAvis = $faker->dateTimeBetween('-3 days', 'now');
            $avis->setCreatedAt(new \DateTimeImmutable('@' . $dateAvis->getTimestamp()));
            $manager->persist($avis);
        }

        $manager->flush();
    }
}
