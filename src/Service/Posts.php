<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\DatabaseException;
use App\Model\Post;
use RedBeanPHP\Facade as R;

class Posts
{
    /**
     * Returns a post by ID
     * @throws DatabaseException
     */
    public function get(int $id)
    {
        $post = R::load( 'posts', $id );
        return $post;
    }
    
    /**
     * Returns all posts
     * @return Post[]
     */
    public function getAll(int $start, int $size): array
    {
		$posts = R::findAll( 'posts' );
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
		$posts  = R::find( 'posts', ' title LIKE ? ', [ '%'. $postTitle .'%' ] );
        if (!$posts) {
            return false;
        }
        return true;
    }

    /**
     * Create a new post
     * @throws DatabaseException
     */
    public function create(string $title, string $content, int $published): int
    {	
		$is_published = $published ?: 1;
		$post = R::dispense( 'posts' );
		$post->title = $title;
		$post->content = $content;
		$post->published = $is_published;
        $id = R::store( $post );
        
        return (int)$id;
    }
    
    public function createTable(): void
    {
        $post = R::dispense( 'posts' );
		$post->title = 'Learn to Program';
        $post->content = 'This is hello world';
		$post->published = true;
        $id = R::store( $post );
	
    }

    /**
     * Update the post if not empty
     * @throws DatabaseException
     */
    public function update(int $id, int $published, string $title, string $content): void
    {
		$post = R::load( 'posts', $id ); //reloads our post
        if (empty($content)) {
			$post->published = 0;
        } else {
			$post->published = $published;
        }
		$post->title = $title;
		$post->content = $content;
		R::store( $post );
    }
}
