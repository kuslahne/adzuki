<?php

declare(strict_types=1);

namespace App\Controller\Admin\Categories;

use App\Exception\DatabaseException;
use App\Service\Categories as ServiceCategories;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Delete implements ControllerInterface
{
    protected ServiceCategories $categories;

    public function __construct(ServiceCategories $categories)
    {
        $this->categories = $categories;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // If the category is the last one I cannot delete it, otherwise no admin access anymore
        if ($this->categories->getTotalCategories() < 2) {
            return new Response(
                409,
                [],
                json_encode(['error' => 'Cannot delete the category since it\'s the last one'])
            );
        }
        try {
            $this->categories->delete($id);
            return new Response(
                200,
                [],
                json_encode(['result' => 'ok'])
            );
        } catch (DatabaseException $e) {
            return new Response(
                404,
                [],
                json_encode(['error' => sprintf("The category ID %d does not exist", $id)])
            );
        }
    }
}
