<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un administrateur
        $admin = new User();
        $admin->setEmail('admin@blog.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('Administrateur');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@blog.com');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // Créer un utilisateur inactif (en attente de validation)
        $inactiveUser = new User();
        $inactiveUser->setEmail('pending@blog.com');
        $inactiveUser->setFirstName('Marie');
        $inactiveUser->setLastName('Martin');
        $inactiveUser->setRoles(['ROLE_USER']);
        $inactiveUser->setIsActive(false);
        $inactiveUser->setPassword($this->passwordHasher->hashPassword($inactiveUser, 'pending123'));
        $manager->persist($inactiveUser);

        // Créer des catégories
        $categories = [];
        $categoryNames = [
            ['name' => 'Technologie', 'description' => 'Articles sur les nouvelles technologies'],
            ['name' => 'Voyages', 'description' => 'Récits et conseils de voyages'],
            ['name' => 'Cuisine', 'description' => 'Recettes et astuces culinaires'],
            ['name' => 'Sport', 'description' => 'Actualités et conseils sportifs'],
        ];

        foreach ($categoryNames as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setDescription($categoryData['description']);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Créer des articles
        $posts = [];
        $postData = [
            [
                'title' => 'Introduction à Symfony 7',
                'content' => "Symfony 7 est la dernière version du framework PHP le plus populaire. Cette version apporte de nombreuses améliorations en termes de performance et de simplicité d'utilisation.\n\nLes nouvelles fonctionnalités incluent une meilleure intégration avec les outils modernes de développement, des composants mis à jour et une documentation enrichie.\n\nDans cet article, nous allons explorer les principales nouveautés de Symfony 7 et comment les utiliser dans vos projets.",
                'picture' => 'https://via.placeholder.com/800x400/007bff/ffffff?text=Symfony+7',
                'category' => 0
            ],
            [
                'title' => 'Les meilleures destinations en 2026',
                'content' => "Planifier vos vacances pour 2026 ? Voici une sélection des destinations à ne pas manquer cette année.\n\n1. Dubai - Pour son architecture futuriste\n2. Tokyo - Pour sa culture unique\n3. Paris - La ville lumière\n4. Bali - Pour ses plages paradisiaques\n5. New York - La ville qui ne dort jamais\n\nChacune de ces destinations offre des expériences uniques et inoubliables.",
                'picture' => 'https://via.placeholder.com/800x400/28a745/ffffff?text=Voyages+2026',
                'category' => 1
            ],
            [
                'title' => 'Recette : Tarte aux pommes maison',
                'content' => "Rien de mieux qu'une bonne tarte aux pommes faite maison ! Voici notre recette familiale.\n\nIngrédients :\n- 1 pâte brisée\n- 5 pommes\n- 100g de sucre\n- 50g de beurre\n- Cannelle\n\nPréparation :\n1. Préchauffer le four à 180°C\n2. Éplucher et couper les pommes en tranches\n3. Disposer la pâte dans un moule\n4. Ajouter les pommes et saupoudrer de sucre et cannelle\n5. Cuire 35 minutes\n\nServez tiède avec une boule de glace vanille !",
                'picture' => 'https://via.placeholder.com/800x400/ffc107/ffffff?text=Tarte+aux+Pommes',
                'category' => 2
            ],
            [
                'title' => 'Comment débuter la course à pied',
                'content' => "La course à pied est un excellent sport pour rester en forme. Voici nos conseils pour bien débuter.\n\n1. Commencez progressivement - pas plus de 20 minutes les premières fois\n2. Investissez dans de bonnes chaussures\n3. Échauffez-vous toujours avant de courir\n4. Hydratez-vous correctement\n5. Alternez course et marche au début\n\nAvec de la régularité, vous progresserez rapidement !",
                'picture' => 'https://via.placeholder.com/800x400/dc3545/ffffff?text=Course+%C3%A0+Pied',
                'category' => 3
            ],
            [
                'title' => 'Docker et Symfony : Le guide complet',
                'content' => "Docker est devenu incontournable pour le développement d'applications Symfony. Ce guide vous explique comment configurer votre environnement de développement avec Docker.\n\nNous allons voir comment créer un Dockerfile optimisé, configurer Docker Compose pour gérer plusieurs services (PHP, Nginx, MySQL), et optimiser votre workflow de développement.\n\nAvec Docker, vous obtiendrez un environnement de développement reproductible et facilement partageable avec votre équipe.",
                'picture' => 'https://via.placeholder.com/800x400/17a2b8/ffffff?text=Docker+%2B+Symfony',
                'category' => 0
            ]
        ];

        foreach ($postData as $index => $data) {
            $post = new Post();
            $post->setTitle($data['title']);
            $post->setContent($data['content']);
            $post->setPicture($data['picture']);
            $post->setAuthor($admin);
            $post->setCategory($categories[$data['category']]);
            $post->setPublishedAt(new \DateTime('-' . ($index + 1) . ' days'));
            $manager->persist($post);
            $posts[] = $post;
        }

        // Créer des commentaires
        $comments = [
            ['post' => 0, 'author' => $user, 'content' => 'Excellent article ! J\'ai hâte de tester Symfony 7.', 'status' => 'approved'],
            ['post' => 0, 'author' => $inactiveUser, 'content' => 'Merci pour ce partage, très instructif.', 'status' => 'pending'],
            ['post' => 1, 'author' => $user, 'content' => 'J\'ai visité Tokyo l\'année dernière, c\'était incroyable !', 'status' => 'approved'],
            ['post' => 2, 'author' => $user, 'content' => 'Recette testée et approuvée ! Délicieuse.', 'status' => 'approved'],
            ['post' => 3, 'author' => $user, 'content' => 'Merci pour ces conseils, je vais commencer dès demain.', 'status' => 'pending'],
            ['post' => 4, 'author' => $user, 'content' => 'Docker est vraiment puissant, merci pour le guide !', 'status' => 'approved'],
        ];

        foreach ($comments as $commentData) {
            $comment = new Comment();
            $comment->setPost($posts[$commentData['post']]);
            $comment->setAuthor($commentData['author']);
            $comment->setContent($commentData['content']);
            $comment->setStatus($commentData['status']);
            $comment->setCreatedAt(new \DateTime('-' . rand(1, 5) . ' hours'));
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
