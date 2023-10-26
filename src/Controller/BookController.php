<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{



    #[Route('/addbook', name: "add_book")]
    public function addBook(ManagerRegistry $manager, Request $req)
    {


        $em = $manager->getManager();
        $book = new Book();

        $book->setPublished(true);

        $form = $this->createForm(BookType::class,  $book);

        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            $nb =  $book->getAuthor()->getNb_books() + 1;

            $book->getAuthor()->setNb_books($nb);

            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute("list_books");
        }
        return $this->renderForm("book/add.html.twig", ["f" => $form]);
    }


    #[Route('/listBooks', name: "list_books")]
    public function listBooks(BookRepository $repo)
    {
        return $this->render("book/list.html.twig", ["books" => $repo->findAll()]);
    }



    #[Route('/update/{id}', name: "update")]
    public function update($id, ManagerRegistry $manager, Request $req, BookRepository $repo)
    {
        $em = $manager->getManager();
        $book = $repo->find($id);

        $book->setPublished(true);

        $form = $this->createForm(BookType::class,  $book);

        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute("list_books");
        }
        return $this->renderForm("book/update.html.twig", ["f" => $form]);
    }



    #[Route('/deleteBook/{id}', name: "delete_book")]
    public function delete($id, ManagerRegistry $manager, BookRepository $repo, AuthorRepository $authorRepo)
    {
        $em = $manager->getManager();
        $book = $repo->find($id);
    
        $author = $book->getAuthor();
        $nb = $author->getNb_books() - 1;
        $author->setNb_books($nb);
    


            $book->getAuthor()->setNb_books($nb);
            if ($nb === 0) {
                $em->remove($author);
            }

        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute("list_books");
    }

    #[Route('/showBook/{id}', name: "show_book")]

    public function showBook($id, BookRepository $repo)
    {
        $book = $repo->find($id);
        return $this->render("book/show.html.twig", ["book" => $book]);
    }

    #[Route('/searchbook', name: "search_book")]
public function searchbook(BookRepository $repo){


    return $this->render("book/list.html.twig", ["books" => $repo->searchBookByRef(1)]);
}


}