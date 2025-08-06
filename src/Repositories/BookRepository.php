<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Author;
use App\Entities\Book;
use PDO;

class BookRepository implements RepositoryInterface
{
    private PDO $db;

    private AuthorRepository $authorRepository;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->authorRepository = new AuthorRepository();
    }

    private function hydrate(array $row): Book
    {
        $author = new Author(
            (int)$row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal', 
            $row['orcid'],
            $row['affilation']
         );
        //REEMPLAZAR HASH SIN REGENERAR
        $ref = new \ReflectionClass($author);
        $property = $ref->getProperty('password');
        $property->setAccessible(true);
        $property->setValue($author, $row['password']);

        return new Book(
            (int)$row['publication_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['isbn'],
            $row['genre'],
            $row['edition'],
        );
    }

    public function findAll(): array{
        $stmt = $this->db->query('CALL sp_book_list();');
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        $out = [];
        foreach ($rows as $r){
            $out[] = $this->hydrate($r);
        }
        return $out;
    }

    public function create(object $entity): bool{
        if (!$entity instanceof Book) {
            throw new \InvalidArgumentException('Expected instance of Book');
        }
        $stmt = $this->db->prepare('CALL sp_create_book(:title, :description, :publication_date, :author_id, :isbn, :genre, :edition);');
        $ok = $stmt->execute([
            ':title' => $entity->getTitle(),
            ':description' => $entity->getDescription(),
            ':publication_date' => $entity->getPublicationDate(),
            ':author_id' => $entity->getAuthor()->getId(),
            ':isbn' => $entity->getIsbn(),
            ':genre' => $entity->getGenre(),
            ':edition' => $entity->getEdition()
        ]);
        if(!$ok) {
         $ok -> fetchAll();
        }
        $stmt->closeCursor();
        return $ok;
    }
    
    public function update(object $entity): bool{
        if (!$entity instanceof Book) {
            throw new \InvalidArgumentException('Expected instance of Book');
        }
        $stmt = $this->db->prepare('CALL sp_update_book(:publication_id, :title, :description, :publication_date, :author_id, :isbn, :genre, :edition);');
        $ok = $stmt->execute([
            ':publication_id' => $entity->getId(),
            ':title' => $entity->getTitle(),
            ':description' => $entity->getDescription(),
            ':publication_date' => $entity->getPublicationDate(),
            ':author_id' => $entity->getAuthor()->getId(),
            ':isbn' => $entity->getIsbn(),
            ':genre' => $entity->getGenre(),
            ':edition' => $entity->getEdition()
        ]);

        if(!$ok) {
            $ok -> fetchAll();
        }
        $stmt->closeCursor();
        return $ok;
    }
    public function delete(int $id): bool{
        $stmt = $this->db->prepare('CALL sp_delete_book(:publication_id);');
        $ok = $stmt->execute([':publication_id' => $id]);
        
        if(!$ok) {
            $ok -> fetchAll();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare('CALL sp_book_find_by_id(:publication_id);');
        $stmt->execute([':publication_id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }
    
}