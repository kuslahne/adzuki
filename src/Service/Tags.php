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

class Tags
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

    public function getAllTagsbyFilters(array $arr)
    {
        
        $tags = R::find( 'tags',
            ' name IN ('.R::genSlots( $arr ).')',
            $arr );
        $result = [];
        if($tags){
            foreach (R::exportAll( $tags ) as $tag) {
                $result[] = strtolower($tag['name']);
            }
        }

        return $result;
    }

    public function getAllTagsbyInput(array $arr)
    {
        
        $tags = R::find( 'tags',
            ' name IN ('.R::genSlots( $arr ).')',
            $arr );
//        $result = [];
//        if($tags){
//            foreach (R::exportAll( $tags ) as $tag) {
//                $result[] = $tag['name'];
//            }
//        }

        return $tags;
        //return R::exportAll( $tags );
    }

    //Only on update tags
    public function diff(string $tags, string $oldTags)
    {
//        $tagsArray = explode(',', $tags);
        $tagsArray = $this->cleanAndCreateArray($tags);
//        $oldTagsArray = explode(',', $oldTags);
        $oldTagsArray = $this->cleanAndCreateArray($oldTags);
        $tagsToDelete = [];
        $tagsToKeep = [];

        $tagsToCompare = [];
        $newTags = [];

        foreach ($oldTagsArray as $oldTag)
        {
            if(in_array($oldTag, $tagsArray)){
                $tagsToKeep[] = $oldTag;
            }else {
                $tagsToDelete[] = $oldTag;
            }
        }

        foreach ($tagsArray as $tag)
        {
            if(in_array($tag, $tagsToKeep)){
                $tagsToCompare[] = $tag;
            } else {
                $newTags[] = $tag;
            }
        }

        $diffs = array(
            'new' => $newTags,
            'compare' => $tagsToCompare,
            'keep'  =>  $tagsToKeep,
            'create' => array_merge($newTags, $tagsToKeep),
            'delete' => $tagsToDelete
        );
        return $diffs;
    }

    public function cleanAndCreateArray(string $tags)
    {
        $tagsArray = explode(',', $tags);
        
        $coll = [];
        foreach($tagsArray as $item) {
            if($item == ' ' || in_array(trim($item), $coll)){
                continue;
            }
            $coll[] = trim($item);
        }
        return $coll;
    }

    public function create(array $tagsArray)
    {	
//        $tagsArray = explode(',', $tags);
//        
//        $coll = [];
//        foreach($tagsArray as $item) {
//            if($item == ' ' || in_array(trim($item), $coll)){
//                continue;
//            }
//            $coll[] = trim($item);
//        }
//        $coll = $this->cleanAndCreateArray($tags);

        $allTags = $this->getAllTagsbyFilters($tagsArray);
        //$allTags = ['testd'];
        //print_r($allTags);
        //exit;

        $inputs = [];
        foreach($tagsArray as $tag) {
            if (in_array(strtolower($tag), $allTags)) {
                continue;
            }
            $inputs[] = $tag;
        }
        if(count($inputs) > 0){
            // Insert new tag
            // for each tag create a new bean as a row/record          
            foreach ($inputs as $input) {
                $bean = R::dispense('tags');
                //assign column values 
                $bean->name = $input;
                $generator = new SlugGenerator;
                $slug = $this->generator->generate($input);
                $bean->url_key = $slug;

                //push row to array
                $beans[] = $bean;
             }
             //store the whole array of beans at once               
             R::storeAll($beans);
        }

//        return $coll;
    }

    public function findTagsByName($arr)
    {
        $tags = R::find( 'tags',
            ' name IN ( '. R::genSlots( $arr ) .' ) ',
            $arr );

        return $tags;
    }

    public function findPostTagsByTagName($arr)
    {
        print_r($arr);
        $sql = "SELECT posttags.*, tags.name as tag_name FROM posttags JOIN tags 
            ON posttags.tag_id = tags.id WHERE tags.name IN (".    R::genSlots( $arr ) .")";
        $rows = R::getAll($sql, $arr);
//        $posts = R::convertToBeans('posttags', $rows);
//        $result = R::exportAll($posts);

//        return $result;

//        $tags = R::find('tags');
//        $posts = R::loadJoined( $tags, 'posttags' );
//        $result = R::exportAll($rows);

        return $rows;
    }

    public function countPostTagsByTagName($arr)
    {
        print_r($arr);
        $sql = "SELECT posttags.*, tags.name as tag_name, count(post_id) as count_posts FROM posttags JOIN tags 
            ON posttags.tag_id = tags.id WHERE tags.name IN (".    R::genSlots( $arr ) .")  GROUP BY tag_name";
        $rows = R::getAll($sql, $arr);
        return $rows;
    }

    public function deletePostTagsByPostId($id)
    {
        $ids = [$id];
        R::hunt( 'posttags',
            ' post_id IN ( '. R::genSlots( $ids ) .' ) ',
            $ids );
    }

    public function deletePostTagsByTagIdAndPostId($tagId, $postId)
    {
        echo $tagId." " .$postId;
        $posttags  = R::find( 'posttags', ' tag_id = :tag_id AND post_id = :post_id', [ ':tag_id' => $tagId, ':post_id' => $postId ] );
        R::trashAll( $posttags );
//        $result = R::exportAll($posttag);
//        print_r($result[0]);
//        echo $result[0]['id'];
//        $this->deletePostTagsByPostId($result[0]['id']);
//        return $result;
    }

    public function deleteTagsByTagId($tagId)
    {
        $tagIds = [$tagId];
        R::hunt( 'tags',
            ' id IN ( '. R::genSlots( $tagIds ) .' ) ',
            $tagIds );
    }

    public function addPostTags($postId, $tags, $published)
    {
        foreach($tags as $tag) {
            $bean = R::dispense('posttags');
            $post = R::load( 'posts', $postId ); //load our post
            $bean->post = $post;
            $bean->post_published = $published;
            //$bean->post_id = $postId;
            $tag = R::load( 'tags', $tag->id );
            $bean->tag = $tag;
            //$bean->tag_id = $tag->id;
            $beans[] = $bean;
        }
        R::storeAll($beans);
    }

    public function getAllTagsByPostId($postId)
    {
        $sql = "SELECT posttags.*, tags.name as tag_name FROM posttags JOIN tags ON posttags.tag_id = tags.id WHERE posttags.post_id = ?";

        $rows = R::getAll($sql, [$postId]);
        $post = R::convertToBeans('posttags', $rows);
        $result = R::exportAll($post );

        return $result;
    }

    public function getTagStringbyPostId($postId)
    {
        $tags = $this->getAllTagsByPostId($postId);
        if ($tags) {
            $tagArray = [];
            foreach ($tags as $tag) {
                $tagArray[] = $tag['tag_name'];
            }
            return implode(', ', $tagArray);
        }
        return false;
    }
}
