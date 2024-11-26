<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Service;

use App\Exception\DatabaseException;
use App\Model\Post;
use PDO;
use RedBeanPHP\Facade as R;

//use \RedBeanPHP\OODB as R;
//use RedBeanPHP\Facade as R;
//use RedBeanPHP\OODB;
//use RedBeanPHP\R as R;

//use \RedBeanPHP\Util;
//use \RedBeanPHP\Util\DispenseHelper;
//use App\Model\Db;

class Posts
{
    private PDO $pdo;
    
    //public Db $db;
    
    public function __construct(PDO $pdo 
	//Db $db
    )
    {
        $this->pdo = $pdo;
	//$this->db = $db;
    }

    /**
     * Returns a post by ID
     * @throws DatabaseException
     */
    public function get(int $id): Post
    {
        $sth = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchObject(Post::class);
        if (false === $result) {
            throw new DatabaseException(sprintf(
                "The post with ID %d does not exist",
                $id
            ));
        }
        return $result;
    }
    
    /**
     * Returns all posts
     * @return Post[]
     */
    public function getAll(int $start, int $size): array
    {
	$posts = R::findAll( 'posts' );
        //$sth = $this->pdo->prepare('SELECT * FROM posts LIMIT :start, :size');
        //$sth->bindParam(':start', $start, PDO::PARAM_INT);
        //$sth->bindParam(':size', $size, PDO::PARAM_INT);
        //$sth->execute();
        //return $sth->fetchAll(PDO::FETCH_CLASS, User::class);
	return $posts;
    }

    /**
     * Delete a post with ID
     * @throws DatabaseException
     */
    public function delete(int $id): void
    {
		$post = R::load( 'posts', $id ); //reloads our post
	    R::trash( $post ); //for one bean
    }

    /**
     * Returns the total number of posts
     */
    public function getTotalPosts(): int
    {
        $numOfPosts = R::count( 'posts' );
        return (int) $numOfPosts;
    }

    /**
     * Returns true if the post already exist
     */
    public function exists(string $postTitle): bool
    {
		$books  = R::find( 'posts', ' title LIKE ? ', [ '%'. $postTitle .'%' ] );
        if (!$books) {
            return false;
        }
        return $books;
    }

    /**
     * Create a new post
     * @throws DatabaseException
     */
    public function create(string $title, string $content, int $published): void
    {	
		$is_published = $published?: 1;
		$post = R::dispense( 'posts' );
		$post->title = $title;
		$post->content = $content;
		$post->published = $is_published;
        $id = R::store( $post );
        if (!$id) {
            throw new DatabaseException(sprintf(
                "Cannot add title %s: %s",
                $title,
                implode(',', $this->pdo->errorInfo())
            ));
        }
    }
    
    public function createTable(): void
    {
        $post = R::dispense( 'posts' );
	$post->title = 'Learn to Program';
        $post->content = 'This is hello world';
	$post->published = true;
        $id = R::store( $post );
	
	//return $id;
    }

    /**
     * Update the post if not empty
     * @throws DatabaseException
     */
    public function update(int $id, int $published, string $title, string $content): void
    {
		//var_dump($id, $published, $title, $content); exit;
		$post = R::load( 'posts', $id ); //reloads our post
        if (empty($content)) {
			$post->published = 0;
			R::store( $post );
        } else {
			$post->published = $published;
        }
		$post->title = $title;
		$post->content = $content;
		R::store( $post );
        /*if (!$sth->execute()) {
            throw new DatabaseException(sprintf(
                "Cannot update post id %d: %s",
                $id,
                implode(',', $this->pdo->errorInfo())
            ));
        }*/
    }

    /**
     * Update the last login with the actual time
     * @throws DatabaseException
     */
    public function updateLastLogin(string $username): void
    {
        $sth = $this->pdo->prepare('UPDATE users SET last_login = :last_login WHERE username = :username');
        $sth->bindValue(':last_login', date("Y-m-d H:i:s"));
        $sth->bindParam(':username', $username);
        if (!$sth->execute()) {
            throw new DatabaseException(sprintf(
                "Cannot update last login for user %s",
                $username
            ));
        }
    }
}
