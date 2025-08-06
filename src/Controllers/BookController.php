<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Book;
use App\Repositories\BookRepository;
use App\Entities\Author;
use App\Repositories\AuthorRepository;

class BookController
{

    private BookRepository $bookRepository;
    private AuthorRepository $authorRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
        $this->authorRepository = new AuthorRepository();
    }

    public function handle(): void
    {
        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $book = $this->bookRepository->findById((int)$_GET['id']);
                echo json_encode($book ? $this->bookToArray($book) : ['error' => 'Book not found']);
            } else {
                $list = array_map([$this, 'bookToArray'], $this->bookRepository->findAll());
                echo json_encode($list);
            }
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if($method === 'POST'){
            $author = $this->authorRepository->findById((int)$payload['author_id']?? 0);
            if(!$author){
                http_response_code(400);
                echo json_encode(['error' => 'Author not found']);
                return;
            }
            $book = new Book(
                0,
                $payload['title'], 
                $payload['description'], 
                new \DateTime($payload['publication_date'] ?? 'now'), 
                $author, 
                $payload['isbn'], 
                $payload['edition'], 
                $payload['genre']
            );

            echo json_encode(['success' => $this->bookRepository->create($book)]);
            return;

            //INSTALAR POSTMAN
        }

        //Verificar si el libro exite 
        if($method === "PUT"){
            $id = (int) ($payload['id'] ?? 0);
            $existing = $this->bookRepository->findById($id);
            if(!$existing){
                http_responde_code(404);
                echo json_encode(['error' => 'BOOK NOT FOUND']);
                return;
            }
//Verificar si autor exite 
            if(isset($payload['autorId'])){
                $author = $this->authorRepository->findById((int) $payload['autorId']);
                if($author) $existing -> setAuthor($author);
            }
//setear valores
            if(isset ($payload['tittle'])) $existing -> setTittle ($payload['tittle']);
            if(isset ($payload['description'])) $existing -> setdescription ($payload['description']);
            if(isset ($payload['publicacionDate'])) $existing -> setpublicacionDate ($payload['publicacionDate']);
            if(isset ($payload['isbn'])) $existing -> setIsbn ($payload['isbn']);
            if(isset ($payload['genre'])) $existing -> setGenre ($payload['genre']);
            if(isset ($payload['edition'])) $existing -> setEdition ($payload['edition']);
            



        }

        if($method === "DELETE"){
            echo json_encode(['success' -> $this->bookRepository->delete((int)($payload['id'] ?? 0))]);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'METHOD NOT ']);  

    }

    public function bookToArray(Book $book): array
    {
        return [
            'publication_id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'first_name' => $book->getAuthor()->getFirstName(),
                'last_name' => $book->getAuthor()->getLastName()
            ],
            'isbn' => $book->getIsbn(),
            'genre' => $book->getGenre(),
            'edition' => $book->getEdition()
        ];
    }
}
