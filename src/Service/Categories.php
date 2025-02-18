<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\DatabaseException;
use App\Model\Category;
use RedBeanPHP\Facade as R;

use League\CommonMark\CommonMarkConverter;
use Ausi\SlugGenerator\SlugGenerator;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;

class Categories
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
     * Returns a category by ID
     * @throws DatabaseException
     */
    public function get(int $id)
    {
        $category = R::load( 'categories', $id );
        if (false === $category) {
            throw new DatabaseException(sprintf(
                "The category with ID %d does not exist",
                $id
            ));
        }
	$result = R::exportAll($category);
	return $result[0];
    }
    
    /**
     * Returns all categories
     * @return Category[]
     */
    public function getAll(int $start, int $size): array
    {
	$categories = R::findAll( 'categories' );
	$result = R::exportAll($categories);
	return $result;
    }

    /**
     * Delete a category with ID
     * @throws DatabaseException
     */
    public function delete(int $id): void
    {
		$category = R::load( 'categories', $id ); //reloads our category
		R::trash( $category ); //for one bean
    }

    /**
     * Returns the total number of categories
     */
    public function getTotalCategories(): int
    {
        $numOfCategories = R::count( 'categories' );
        return (int) $numOfCategories;
    }

    /**
     * Returns true if the categories already exist
     */
    public function exists(string $categoryName): bool
    {
		$categories  = R::find( 'categories', ' name = ? ', [$categoryName] );
        if (!$categories) {
            return false;
        }
        return true;
    }

    /**
     * Create a new category
     * @throws DatabaseException
     */
    public function create(string $name, string $description, string $metaDescription): void
    {	
		$category = R::dispense( 'categories' );
		$category->name = $name;
		$category->description = $description;
		$category->meta_description = $metaDescription;
        $id = R::store( $category );
        if (!$id) {
            throw new DatabaseException(sprintf(
                "Cannot add category %s: %s",
                $name,
                implode(',', $this->pdo->errorInfo())
            ));
        }
    }
    
    public function createTable(): void
    {
        $category = R::dispense( 'categories' );
		$category->name = 'Learning';
		$category->description = 'This is new category';
		$category->meta_description = null;
        $id = R::store( $category );
    }

    /**
     * Update the category if not empty
     * @throws DatabaseException
     */
    public function update(int $id, string $meta_description, string $name, string $description): void
    {
		$category = R::load( 'categories', $id );        
		$category->name = $name;
		$category->meta_description = $meta_description;
		$category->description = $description;
		R::store( $category );
		flash()->success([sprintf("The category %s has been successfully updated!", $name)]);
    }
}
