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
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Create implements ControllerInterface
{
    protected Engine $plates;
    protected Categories $categories;

    public function __construct(Engine $plates, Categories $categories)
    {
        $this->plates = $plates;
        $this->categories = $categories;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();

        // If no POST params just render new-category view
        if (empty($params)) {
            return new Response(
                200,
                [],
                $this->plates->render('admin::new-category', ['page'	=>  'categories'])
            );
        }

        $name = $params['name'] ?? '';
        $description = $params['description'] ?? '';
        $metaDescription = (string)$params['meta_description'] ?? '';
        
        $errors = $this->validateParams($name, $description);
        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $this->plates->render('admin::new-category', array_merge($errors, [
                    'name' => $name,
                    'page'	=>  'categories'
                ]))
            );
        }

        try {
            $this->categories->create($name, $description, $metaDescription);
            return new Response(
                201,
                [],
                $this->plates->render('admin::new-category', [
                    'result' => sprintf("The category %s has been successfully created!", $name),
                    'page'	=>  'categories'
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log error
            return new Response(
                500,
                [],
                $this->plates->render('admin::new-category', [
                    'error' => 'Error adding the category, please contact the administrator'
                ])
            ); 
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function validateParams(string $name, string $description): array
    {
        if (empty($name)) {
            return [
                'formErrors' => [
                    'name' => 'The name cannot be empty'
                ]
            ];
        }
        if (empty($description)) {
            return [
                'formErrors' => [
                    'description' => 'The description cannot be empty'
                ]
            ];
        }
        if ($this->categories->exists($name)) {
            return [
                'formErrors' => [
                    'name' => 'The name already exists!'
                ]
            ];
        }
        if (strlen($description) < Category::MIN_CONTENT_LENGTH) {
            return [
                'formErrors' => [
                    'description' => sprintf("The description must be at least %d characters long", Category::MIN_CONTENT_LENGTH)
                ]
            ];
        }
        return [];
    }
}
