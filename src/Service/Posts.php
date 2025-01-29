<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\DatabaseException;
use App\Model\Post;
use RedBeanPHP\Facade as R;
use League\CommonMark\CommonMarkConverter;
use Ausi\SlugGenerator\SlugGenerator;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;

class Posts
{
	protected $generator;
	protected $flash;
    protected Handlebars $handlebars;
	
	public function __construct(
		SlugGenerator $generator, 
		flash $flash, 
		Handlebars $handlebars, 
	)
	{
		$this->generator = $generator;
		$this->flash = $flash;
		$this->handlebars = $handlebars;
	}
	
    /**
     * Returns a post by ID
     * @throws DatabaseException
     */
    public function get(int $id)
    {
        $post = R::load( 'posts', $id );
        $result = R::exportAll($post);
		return $result[0];
    }
    
    public function getPost(int $id)
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
	    $posts = R::find( 'posts' , ' LIMIT ?, ? ', [ $start, $size ] );
		return $posts;
    }
    
    public function getPublishedPosts(int $start, int $size): array
    {
		$posts = R::find( 'posts' , 'published = ? order by id desc LIMIT ?, ? ', [ 1, $start, $size ] );
		return $posts;
	}
	
	public function getAllPublished(int $start, int $size): array
    {
		$posts = $this->getPublishedPosts($start, $size);
		$array = [];
		if($posts){
			$items = R::exportAll($posts);
		}
		
		return $this->mdConvert($items, $start);		
    }
    
    public function getRecentPosts(): array
    {
		$posts = R::find( 'posts' , 'published = ? order by id desc LIMIT ? ', [ 1, 5 ] );
		return R::exportAll($posts);
	}
    
    public function getAllPosts(int $start, int $size): array
    {
		$posts = $this->getAll($start, $size);
		$array = [];
		if($posts){
			$items = R::exportAll($posts);
		}
		
		return $this->mdConvert($items, $start);		
    }
    
    public function mdConvert($items, $start)
    {
		$converter = new CommonMarkConverter();
		$result = [];
		$counter = $start;
		foreach($items as $key => $item){
			$num_words = 36;
			$words = [];
			$words = explode(" ", $item['content'], $num_words);
			$shown_string = "";

			if(count($words) == 36){				
				$item['read_more'] = true;
				$readMoreLink = '<a href="/post/'.$item['slug'].'">Read more</a>';
				$words[35] = "..." . ' ' . $readMoreLink;
			}

			$shown_string = implode(" ", $words);		
			$item['content'] = $converter->convert($shown_string);
			$counter++;
			$item['counter'] = $counter;
			$item['is_published'] = $item['published'] === '1' ? true : false; 
			$result[] = $item;
		}
		return $result;
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
    
    public function getTotalPublished(): int
    {
        $numOfPosts = R::count( 'posts', 'published = ?', [1]);
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
    public function create(string $title, string $content, int $published, string $slug): int
    {	
		$is_published = $published ?: 1;
		$post = R::dispense( 'posts' );
		$post->title = $title;
		$post->content = $content;
		$post->published = $is_published;
		$slug = $this->createSlug($slug, $title);
		if(is_null($slug)){
			return null;
		}
		$post->slug = $slug;
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
    public function update(int $id, int $published, string $title, string $content, string $slug): void
    {
		$post = R::load( 'posts', $id ); //reloads our post
        if (empty($content)) {
			$post->published = 0;
        } else {
			$post->published = $published;
        }
        
        $slug = $this->createSlug($slug, $title, $id);
        if(is_null($slug)){
			return;
		}
        
		$post->title = $title;
		$post->content = $content;
		$post->slug = $slug;
		R::store( $post );
		flash()->success([sprintf("The post %s has been successfully updated!", $title)]);
    }
    
    public function slugExist(string $slug, int $id)
    {
		$posts  = R::find( 'posts', ' slug = ? AND id != ?', [ $slug, $id] );
        if (!$posts) {
            return false;
        }
        return true;
	}
	
	public function createSlug($slug, $title, $id = null)
	{
		if (empty($slug)) {
			$generator = new SlugGenerator;
			$slug = $this->generator->generate($title);
        } else {
			$position = strpos($slug, ' ');
			if ($position !== false) {
				$slug = $this->generator->generate($slug);
			}
			//Check existing slug
			if($id){
				if($this->slugExist($slug, $id)){
					flash()->error(['Invalid slug! Slug already used.']);
					return null;
				}
			}
        }
        return $slug;
	}
}
