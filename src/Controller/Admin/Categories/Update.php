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

class Update implements ControllerInterface
{
    protected Engine $plates;
    protected ServiceCategories $categories;

    public function __construct(Engine $plates, ServiceCategories $categories)
    {
        $this->plates = $plates;
        $this->categories = $categories;
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
        
        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $this->plates->render('admin::edit-category', array_merge($errors, [
                    'category' => $category,
                    'page'	=>  'categories'
                ]))
            );
        }

        try {
            $this->categories->update($id, $metaDescription, $name, $description);
            return new Response(
                200,
                [],
                $this->plates->render('admin::edit-category', [
                    'result' => sprintf("The category %s has been successfully updated!", $category->name),
                    'category' => $category,
                    'page'	=>  'categories'
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log error
            return new Response(
                500,
                [],
                $this->plates->render('admin::edit-category', [
                    'error' => 'Error updating the category, please contact the administrator',
                    'category' => $category
                ])
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
            return [
                'formErrors' => [
                    'content' => 'The content must be at least 10 characters long'
                ]
            ];
        }
        return [];
    }
}
