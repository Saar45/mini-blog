<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/comment')]
#[IsGranted('ROLE_ADMIN')]
class AdminCommentController extends AbstractController
{
    #[Route('/', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $commentRepository->findBy([], ['createdAt' => 'DESC']),
            'pendingComments' => $commentRepository->findPending(),
        ]);
    }

    #[Route('/{id}/approve', name: 'app_admin_comment_approve', methods: ['POST'])]
    public function approve(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$comment->getId(), $request->request->get('_token'))) {
            $comment->approve();
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire approuvé avec succès !');
        }

        return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_admin_comment_reject', methods: ['POST'])]
    public function reject(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject'.$comment->getId(), $request->request->get('_token'))) {
            $comment->reject();
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire rejeté avec succès !');
        }

        return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
