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
use App\Service\Categories as ServiceCategories;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Response\HaltResponse;

class Read implements ControllerInterface
{
    const CATEGORIES_PER_PAGE = 10;

    protected Engine $plates;
    protected ServiceCategories $categories;

    public function __construct(Engine $plates, ServiceCategories $categories)
    {
        $this->plates = $plates;
        $this->categories = $categories;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getAttribute('id', null);
        if (empty($id)) {
            $params = $request->getQueryParams();
            $start = (int) ($params['start'] ?? 0);
            $size = (int) ($params['size'] ?? self::CATEGORIES_PER_PAGE);
            return new Response(
                200,
                [],
                $this->plates->render('admin::categories', [
                    'start' => $start,
                    'size' => $size,
                    'total' => $this->categories->getTotalCategories(),
                    'categories' => $this->categories->getAll($start, $size),
                    'page'	=>  'categories'
                ])
            );
        }
        try {
            $category = $this->categories->get((int) $id);
            return new Response(
                200,
                [],
                $this->plates->render('admin::edit-category', [
                    'category' => $category,
                    'page'	=>  'categories'
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log the category does not exist
            return new HaltResponse(
                303,
                ['Location' => Route::DASHBOARD]
            );  
        }
    }
}
