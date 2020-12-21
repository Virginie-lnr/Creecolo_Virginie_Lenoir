<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_showallposts")
     */
    public function showAll(): Response
    {
        $allPosts = $this->getDoctrine()->getRepository(Post::class)->findAll(); 

        return $this->render('post/showall.html.twig', [
            'allPosts' => $allPosts
        ]);
    }

    /**
     * @Route("post/create", name="app_createpost")
     */
    public function create(Request $request){

        $post = new Post();

        $form = $this->createForm(PostType::class, $post); 
        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){
            $manager= $this->getDoctrine()->getManager(); 
            $manager->persist($post); 
            $manager->flush(); 

            return $this->redirectToRoute('app_showallposts'); 
        }
        return $this->render('post/create.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }
}
