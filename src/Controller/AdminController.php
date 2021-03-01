<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    // /**
    //  * @Route("/categories/", name="app_showallcategories")
    //  */
    // public function showallCategories(){
    //     $allCategories = $this->getDoctrine()->getRepository(Category::class)->findAll(); 

    //     return $this->render('admin/dashboard.html.twig', [
    //         'allCategories' => $allCategories
    //     ]); 
    // }

    // /**
    //  * @Route("/comments", name="app_showallcomments")
    //  */
    // public function showAllComments(){
    //     $comments = $this->getDoctrine()->getRepository(Comment::class)->findAll();

    //     return $this->render('comment/showall.html.twig', [
    //         'comments' => $comments
    //     ]); 

    // }
}
