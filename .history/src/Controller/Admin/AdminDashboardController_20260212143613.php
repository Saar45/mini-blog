<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        PostRepository $postRepository,
        CommentRepository $commentRepository
    ): Response {
        $totalUsers = count($userRepository->findAll());
        $totalPosts = count($postRepository->findAll());
        $pendingComments = count($commentRepository->findPending());
        $inactiveUsers = count($userRepository->findBy(['isActive' => false]));

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'pendingComments' => $pendingComments,
            'inactiveUsers' => $inactiveUsers,
        ]);
    }
}
