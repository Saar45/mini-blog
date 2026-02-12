<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/toggle-active', name: 'app_admin_user_toggle_active', methods: ['POST'])]
    public function toggleActive(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($this->isCsrfTokenValid('toggle-active'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsActive(!$user->isActive());
            $entityManager->flush();

            $status = $user->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Le compte utilisateur a été {$status} avec succès !");
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/promote', name: 'app_admin_user_promote', methods: ['POST'])]
    public function promote(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($this->isCsrfTokenValid('promote'.$user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            if (!in_array('ROLE_ADMIN', $roles, true)) {
                $user->setRoles(array_merge($roles, ['ROLE_ADMIN']));
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été promu administrateur !');
            } else {
                $this->addFlash('info', 'L\'utilisateur est déjà administrateur.');
            }
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
