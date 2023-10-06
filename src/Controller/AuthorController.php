<?php

namespace App\Controller;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Request;

use App\Form\AuthorType;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
 

    #[Route('/add', name: 'add_author')]
    public function add(ManagerRegistry  $manager , Request $req){

        $author = new Author();
        $em = $manager->getManager();
      $form = $this->createForm(AuthorType::class,$author);
       // $author-> setUsername("aziz");
        //$author->setEmail("aziz@email.com");
 $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute("app_list");
        }
        return  $this->renderForm("author/add.html.twig", ["f" => $form]);
    }


#[Route('/list', name: 'app_list')]
public function list (AuthorRepository $repo){

    return $this->render('author/list.html.twig', ["auteurs" => $repo->findAll()]);
}
 
#[Route('/delete/{ide}', name: "delete")]
    public function deleteAuthor($ide, AuthorRepository $repo, ManagerRegistry $manager)
    {

        $author = $repo->find($ide);

        $em = $manager->getManager();   


        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute("app_list");
    }

#[Route('/edit/{ide}',name: "edit")]
public function editAuthor($ide, AuthorRepository $repo,ManagerRegistry $manager,Request $req){

    $author = $repo->find($ide);
    $form = $this->createForm(AuthorType::class,$author);
    $form ->handleRequest($req);
    if($form->isSubmitted() && $form->isValid()){
   $manager->getManager()->flush();
return $this->redirectToRoute("app_list");
    }

return $this->renderForm('author/edit.html.twig',[
    'forme'=>$form
]);

}


    
}
