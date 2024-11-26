<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller\Admin\Posts;

use App\Exception\DatabaseException;
use App\Service\Posts as ServicePosts;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Delete implements ControllerInterface
{
    protected ServicePosts $posts;

    public function __construct(ServicePosts $posts)
    {
        $this->posts = $posts;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // If the post is the last one I cannot delete it, otherwise no admin access anymore
        if ($this->posts->getTotalPosts() < 2) {
            return new Response(
                409,
                [],
                json_encode(['error' => 'Cannot delete the post since it\'s the last one'])
            );
        }
        try {
            $this->posts->delete($id);
            return new Response(
                200,
                [],
                json_encode(['result' => 'ok'])
            );
        } catch (DatabaseException $e) {
            return new Response(
                404,
                [],
                json_encode(['error' => sprintf("The post ID %d does not exist", $id)])
            );
        }
    }
}
