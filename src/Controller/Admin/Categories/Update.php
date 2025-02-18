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
use App\Service\Categories as ServiceCategories;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;


class Update implements ControllerInterface
{
    protected Engine $plates;
	protected $flash;
    protected ServiceCategories $categories;
    protected Handlebars $handlebars;

    public function __construct(Engine $plates, ServiceCategories $categories, flash $flash, Handlebars $handlebars)
    {
        $this->plates = $plates;
        $this->categories = $categories;
        $this->flash = $flash;
        $this->handlebars = $handlebars;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $params = $request->getParsedBody();

        $category = $this->categories->get($id);
        $name = $params['name'] ?? '';
        $description = $params['description'] ?? '';
        $metaDescription = (string)$params['meta_description'];
       
        $errors = $this->checkParams($name, $description);
        $output = $this->flash->display();

		$renderer = $this->handlebars->renderer('admin/category_edit');
		$data = array(
			'total' => $this->categories->getTotalCategories(),
			'class'	=> 'categories',
			'page'	=> 'categories',
			'repo'  => 'category',
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => null,
			'flash' => $output
		);

        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $renderer($data)
            );
        }
        try {
            $this->categories->update($id, $metaDescription, $name, $description);
            return new Response(
                200,
                [],
                $renderer($data)
            );
        } catch (DatabaseException $e) {
            // @todo log error
            flash()->error(['Error updating the category, please contact the administrator']);
            return new Response(
                500,
                [],
                $renderer($data),
              );
        }

    }

    /**
     * Check the parameters and returns errors if any
     * 
     * @return array<string, array<string, string>>
     */
    private function checkParams(string $name, string $description): array
    {
        if (empty($description)) {
            return [];
        }
        if (strlen($description) < Category::MIN_CONTENT_LENGTH) {
            flash()->error(['The content must be at least 50 characters long']);
			return [
                'formErrors' => [
                    'description' => 'The content must be at least 50 characters long'
                ] 
            ];
        }
        return [];
    }
}
