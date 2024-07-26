<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\Games;
use App\Entity\Teams;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

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
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($teamData['name'])->lower();
            $team->setSlug($slug);
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
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;
        }

        // Création de faux matchs
        $games = [];
        for ($i = 0; $i < 30; $i++) {
            $game = new Games();
            $team_domocile = $teams[array_rand($teams)];
            $team_exterieur = $teams[array_rand($teams)];
            $game->setTitle($faker->sentence);
            $dateMatch = $faker->dateTimeBetween('-1 month', 'now');
            $game->setDateMatch(new \DateTimeImmutable('@' . $dateMatch->getTimestamp()));
            $game->setEquipeDomicile($team_domocile);
            $game->setEquipeExterieur($team_exterieur);
            $game->setScore($faker->numberBetween(0, 7) . ' - ' . $faker->numberBetween(0, 7));
            $game->setCreatedAt(new \DateTimeImmutable('@' . $dateMatch->getTimestamp()));
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($team_domocile . "-vs-" . $team_exterieur)->lower();
            $game->setSlug($slug);

            $manager->persist($game);
            $games[] = $game;
        }

        // Création de faux avis
        for ($i = 0; $i < 50; $i++) {
            $avis = new Avis();
            $game_actu = $games[array_rand($games)];
            $avis->setCommentaire($faker->paragraph);
            $avis->setUser($users[array_rand($users)]);
            $avis->setGame($game_actu);
            $dateAvis = $faker->dateTimeBetween('-3 days', 'now');
            $avis->setCreatedAt(new \DateTimeImmutable('@' . $dateAvis->getTimestamp()));
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($game_actu->getEquipeExterieur() . "-vs-" . $game_actu->getEquipeDomicile())->lower();
            $avis->setSlug($slug);
            $manager->persist($avis);
        }

        // Admin
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@gmail.com");
        $hashedPassword = $userPasswordHasher->hashPassword(
            $user,
            "123456"
        );
        $user->setPassword($hashedPassword);
        $user->setCreatedAt(new \DateTimeImmutable);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
