<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/create/{id<\d+>}", name="app_createcomment")
     */
    public function create(Request $request, $id): Response
    {
        // set date
        // set user
        // set post
        $comment = new Comment(); 
        
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);  

        $user = $this->getUser(); 
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime); 
            $comment->setUser($user); 
            $comment->setPost($post);
            $manager = $this->getDoctrine()->getManager(); 
            $manager->persist($comment); 
            $manager->flush(); 

            return $this->redirectToRoute('app_showallposts'); 
        }

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/comments/post/{id<\d+>}", name="app_showcomments")
     */
    public function showComments($id){
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id); 
        $comments = $post->getComments(); 

        return $this->render('comment/show.html.twig', [
            'comments' => $comments
        ]); 
    }

    /**
     * @Route("/delete/comment/{id<\d+>}", name="app_deletecomment")
     */
    public function delete($id){
        $em = $this->getDoctrine()->getmanager();

        $comment = $em->getRepository(Comment::class)->find($id); 
        // dd($comment); 
        if($comment != null){
            $em->remove($comment); 
            $em->flush(); 
        }

        return $this->redirectToRoute('app_showallcomments'); 
    }

    /**
     * @Route("/comments", name="app_showallcomments")
     */
    public function showAll(){
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAll();

        return $this->render('comment/showall.html.twig', [
            'comments' => $comments
        ]); 

    }
}
