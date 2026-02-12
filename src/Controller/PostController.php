<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post_show')]
    public function show(Post $post, Request $request, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isGranted('ROLE_USER')) {
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été envoyé et est en attente de validation.');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        $approvedComments = $commentRepository->findApprovedByPost($post->getId());

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $approvedComments,
            'commentForm' => $form,
        ]);
    }
}
