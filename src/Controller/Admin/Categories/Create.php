<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

 namespace App\Controller\Admin\Categories;

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Model\Category;
use App\Service\Categories;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\PaginatorHelper;


class Create implements ControllerInterface
{
    protected Categories $categories;
	protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(
        Categories $categories, 
        flash $flash, 
		Handlebars $handlebars,
		PaginatorHelper $paging)
    {
        $this->categories = $categories;
        $this->flash = $flash;
		$this->handlebars = $handlebars;
		$this->paging = $paging;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();
		$output = $this->flash->display();

        // If no POST params just render new-category view
        $data = array(
			'class'	=> 'categories',
			'page'	=> 'categories',
			'repo'  => 'category',
			'title' => 'Add Category',
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => null,
			'flash' => $output,
		);
		$renderer = $this->handlebars->renderer('admin/category_new');
        if (empty($params)) {			
            return new Response(
                200,
                [],
                $renderer($data),
            );
        }


        $name = $params['name'] ?? '';
        $description = $params['description'] ?? '';
        $metaDescription = (string)$params['meta_description'] ?? '';
        
        $errors = $this->validateParams($name, $description);

        if (!empty($errors)) {
			$data['flash'] = flash()->display();
            return new Response(
                400,
                [],
                $renderer($data),
            );
        }

        try {
            $this->categories->create($name, $description, $metaDescription);
			flash()->success([sprintf("The category %s has been successfully created!", $name)]);

            return new Response(
                201,
                [],
                $renderer($data),
            );
        } catch (DatabaseException $e) {
            // @todo log error
            flash()->error(['Error adding the category, please contact the administrator']);
            $data['flash'] = flash()->display();
            return new Response(
                500,
                [],
                $renderer($data)
            ); 
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function validateParams(string $name, string $description): array
    {
        if (empty($name)) {
            flash()->error(['The name cannot be empty']);
            return [
                'formErrors' => [
                    'name' => 'The name cannot be empty'
                ]
            ];
        }
        if (empty($description)) {
            flash()->error(['The description cannot be empty']);
            return [
                'formErrors' => [
                    'description' => 'The description cannot be empty'
                ]
            ];
        }
        if ($this->categories->exists($name)) {
            flash()->error(['The name already exists!']);
            return [
                'formErrors' => [
                    'name' => 'The name already exists!'
                ]
            ];
        }
        if (strlen($description) < Category::MIN_CONTENT_LENGTH) {
            flash()->error([sprintf("The description must be at least %d characters long", Category::MIN_CONTENT_LENGTH)]);
            return [
                'formErrors' => [
                    'description' => sprintf("The description must be at least %d characters long", Category::MIN_CONTENT_LENGTH)
                ]
            ];
        }
        return [];
    }
}
