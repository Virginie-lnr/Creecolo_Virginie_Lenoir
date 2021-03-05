<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Entity\Category;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    public function show($id, PaginatorInterface $paginator, Request $request){
        $manager = $this->getDoctrine()->getManager(); 
        $post = $manager->getRepository(Post::class)->find($id); 
        $comments = $post->getComments(); 

        // dd($comments);

        $commentsToPaginate = $paginator->paginate(
            $comments, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            1 // Nombre de résultats par page
        );

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments, 
            'commentsToPaginate' => $commentsToPaginate
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

    /**
     * To like or dislike a post
     * 
     * @Route("/post/{post_id}/like", name="app_like", requirements={"id":"\d+"})
     * 
     * 
     * @param Post $post
     * @param User $user
     * @param ObjectManager $manager
     * @param LikeRepository $likeRepo
     * @return void
     */
    public function like($post_id)
    {
        //dd();
        $manager = $this->getDoctrine()->getManager();
        $post = $manager->getRepository(Post::class)->find($post_id);
        $likeRepo = $manager->getRepository(Like::class);

        $connectedUser = $this->getUser();
        
        // if(!$post->isLikedByUser($connectedUser)) return $this->json([
        //     'code' => 403,
        //     'message' => 'unauthorized'
        // ], 403);

        if($post->isLikedByUser($connectedUser)){
            $like = $likeRepo->findOneBy([
                'post' => $post,
                'user' => $connectedUser
            ]);
        
            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200, 
                'message' => "like bien supprimé", 
                'likes' => $likeRepo->count(['post' => $post])
            ], 200);
        }

        $like = new Like();
        $like
            ->setPost($post)
            ->setUser($connectedUser);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200, 
            'message' => "Like bien ajouté",
            'likes' => $likeRepo->count(['post' => $post])
        ], 200);
    }

}
