<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use App\Form\EditProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{  
    /**
     * @Route("/user/profile/{id<\d+>}", name="app_userprofile")
     */
    public function showUserProfile($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id); 

        $allPosts = $user->getPosts();
        $likes = $user->getLikes();
        
        return $this->render('user/showuserprofile.html.twig', [
            'allPosts' => $allPosts, 
            'user' => $user,
            'likes' => $likes
        ]);
    }

    /**
     * @Route("/users", name="app_showallusers")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAllUsers(){
        $users = $this->getDoctrine()->getRepository(User::class)->findAll(); 

        return $this->render('user/showallusers.html.twig', [
            'users' => $users, 
        ]);
    }

    /**
     * @Route("/user/post/{id<\d+>}", name="app_showpostuser")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showPostUser($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id); 
        $allPosts = $user->getPosts(); 

        return $this->render('user/showpostuser.html.twig', [
            'allPosts' => $allPosts, 
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/delete/{id<\d+>}", name="app_deleteuser")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $manager = $this->getDoctrine()->getManager(); 
        
        if($user->getComments()){
            foreach ($user->getComments() as $comment) {
                $manager->remove($comment);
            }
        }
        if($user->getLikes()){
            foreach ($user->getLikes() as $like) {
                $manager->remove($like);
            }
        }
        if($user->getPosts()){
            foreach ($user->getPosts() as $post) {
                $manager->remove($post);
            }
        }
        
        $manager->remove($user); 
        $manager->flush();

        $this->addFlash('warning', 'The user has been successfully deleted');

        return $this->redirectToRoute('app_showallusers');
    }

    /**
     * @Route("/user/update/{id<\d+>}", name="app_editprofile")
     */
    public function editProfile(Request $request, $id){
        $manager= $this->getDoctrine()->getManager(); 
        $user = $manager->getRepository(User::class)->find($id); 

        $form = $this->createForm(EditProfileType::class, $user); 
        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){
            // dd($form['imageFile']->getData());
            $user->setUpdatedAt(new \Datetime('now'));
            $manager->persist($user); 
            $manager->flush(); 

            $this->addFlash(
                'info',
                'Your changes have been saved successfully'
            );
            return $this->redirectToRoute('app_userprofile', array('id' => $user->getId()) ); 
        }
        return $this->render('user/editprofile.html.twig', [
            'form' => $form->createView(), 
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/profile/{id<\d+>}/likes", name="app_userlikes")
     * 
     * Get all the posts liked by a user
     *
     * @param [type] $id
     * @return void
     */
    public function showUserLikes($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id); 
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll(); 

        $likes = $user->getLikes();
        // dd($likes);

        return $this->render('user/_userlikes.html.twig', [
            'user' => $user, 
            'likes' => $likes,
            'category' => $categories
        ]);
    }
}
