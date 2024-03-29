<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
// use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CommentController extends AbstractController
{
    /**
     * 
     * Form to add a comment 
     * 
     * @Route("/comment/create/{id<\d+>}", name="app_createcomment")
     */
    public function create(Request $request, $id): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime);
            $comment->setUser($user);
            $comment->setPost($post);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('app_showpost', array('id' => $post->getId()));
        }

        // $response = $this->forward('App\Controller\PostController::show', [
        //     'form'  => $form,
        // ]);

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,

        ]);
    }

    /**
     * Gather all the comments related to one post for the dashboard admin
     * 
     * @Route("/comments/post/{id<\d+>}", name="app_showcomments")
     * 
     */
    public function showComments($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $comments = $post->getComments();

        return $this->render('comment/show.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * 
     * Delete a comment
     * 
     * @Route("/delete/comment/{id<\d+>}", name="app_deletecomment")
     */
    public function delete($id, AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();

        $comment = $em->getRepository(Comment::class)->find($id);
        // dd($comment); 
        if ($comment != null) {
            $em->remove($comment);
            $em->flush();
        }

        if ($comment->getUser() !== $this->getUser() && false === $authChecker->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException("You are not allowed to delete this comment");
        }

        $this->addFlash('warning', 'The comment has been successfully deleted');

        return $this->redirectToRoute('app_showallcomments');
    }

    /**
     * Get all comments 
     * 
     * @Route("/comments", name="app_showallcomments")
     */
    public function showAll()
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAll();

        return $this->render('comment/showall.html.twig', [
            'comments' => $comments
        ]);
    }
}
