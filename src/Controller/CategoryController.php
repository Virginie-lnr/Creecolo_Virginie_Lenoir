<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/create", name="app_createcategory")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager(); 

            $manager->persist($category); 
            $manager->flush();
            
            return $this->redirectToRoute('app_showallposts');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(), 
            'category' => $category
        ]); 
    }

    /**
     * @Route("/category/update/{id<\d+>}", name="app_updatecategory")
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager(); 

        $category = $manager->getRepository(Category::class)->find($id); 

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            

            $manager->persist($category); 
            $manager->flush();
            
            return $this->redirectToRoute('app_showallcategories');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(), 
            'category' => $category
        ]); 
    }

    /**
     * @Route("/categories/", name="app_showallcategories")
     */
    public function showall(){
        $allCategories = $this->getDoctrine()->getRepository(Category::class)->findAll(); 

        return $this->render('category/showall.html.twig', [
            'allCategories' => $allCategories
        ]); 
    }

    /**
     * @Route("/category/{id<\d+>}", name="app_category")
     */
    public function show($id){
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id); 
        $allPosts = $category->getPosts(); 
        $allCategories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        // dd($category);

        return $this->render('category/show.html.twig', [
            'allPosts' => $allPosts, 
            'category' => $category, 
            'allCategories' => $allCategories
        ]);
    }

    /**
     * @Route("/category/delete/{id<\d+>}", name="app_deletecategory")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete($id){

        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $manager = $this->getDoctrine()->getManager(); 

        $manager->remove($category); 
        $manager->flush(); 
        
        return $this->redirectToRoute('app_showallcategories');
    }
}
