<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_showallposts")
     */
    public function showAll(): Response
    {
        $allPosts = $this->getDoctrine()->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC'] ); 
        $allCategories = $this->getDoctrine()->getRepository(Category::class)->findAll(); 

        // dd($allCategories);

        return $this->render('post/showall.html.twig', [
            'allPosts' => $allPosts, 
            'allCategories' => $allCategories, 
        ]);
    }

    /**
     * @Route("/post/{id<\d+>}", name="app_showpost")
     */
    public function show($id){
        $manager = $this->getDoctrine()->getManager(); 
        $post = $manager->getRepository(Post::class)->find($id); 

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]); 
    }

    /**
     * @Route("post/create", name="app_createpost")
     */
    public function create(Request $request){

        $post = new Post();

        $form = $this->createForm(PostType::class, $post); 
        $form->handleRequest($request); 
        $user = $this->getUser();

        if($form->isSubmitted() && $form->isValid()){
            $manager= $this->getDoctrine()->getManager(); 
            $post->setUser($user); 
            $manager->persist($post); 
            $manager->flush(); 

            return $this->redirectToRoute('app_showallposts'); 
        }
        return $this->render('post/create.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/update/{id<\d+>}", name="app_updatepost")
     */
    public function update(Request $request, $id, AuthorizationCheckerInterface $authChecker){
        $manager= $this->getDoctrine()->getManager(); 
        $post = $manager->getRepository(Post::class)->find($id); 

        $form = $this->createForm(PostType::class, $post); 
        $form->handleRequest($request); 

        $current_user = $post->getUser();

        if($post->getUser() !== $this->getUser() && false === $authChecker->isGranted('ROLE_ADMIN')){
            throw $this->createNotFoundException("You are not allowed to edit this post!"); 
        }

        if($form->isSubmitted() && $form->isValid()){
            $post->setUpdatedAt(new \Datetime('now'));
            $manager->persist($post); 
            $manager->flush(); 

            return $this->redirectToRoute('app_showallposts'); 
        }
        return $this->render('post/create.html.twig', [
            'form' => $form->createView(), 
            'post' => $post,
        ]);
    }

    /**
     * @Route("/post/delete/{id<\d+>}", name="app_deletepost")
     */
    public function delete($id, AuthorizationCheckerInterface $authChecker){
        $manager = $this->getDoctrine()->getManager();

        $post = $manager->getRepository(Post::class)->find($id);

        if($post->getUser() !== $this->getUser() && false === $authChecker->isGranted('ROLE_ADMIN')){
            throw $this->createNotFoundException("You are not allowed to delete this post!"); 
        }

        $manager->remove($post); 
        $manager->flush(); 

        return $this->redirectToRoute('app_showallposts');
    }

}
