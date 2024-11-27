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

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Service\Posts as ServicePosts;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Response\HaltResponse;

class Read implements ControllerInterface
{
    const POSTS_PER_PAGE = 10;

    protected Engine $plates;
    protected ServicePosts $posts;

    public function __construct(Engine $plates, ServicePosts $posts)
    {
        $this->plates = $plates;
        $this->posts = $posts;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getAttribute('id', null);
        if (empty($id)) {
            $params = $request->getQueryParams();
            $start = (int) ($params['start'] ?? 0);
            $size = (int) ($params['size'] ?? self::POSTS_PER_PAGE);
            return new Response(
                200,
                [],
                $this->plates->render('admin::posts', [
                    'start' => $start,
                    'size' => $size,
                    'total' => $this->posts->getTotalPosts(),
                    'posts' => $this->posts->getAll($start, $size),
					'page'	=>  'posts'
                ])
            );
        }
        try {
            $post = $this->posts->get((int) $id);
            return new Response(
                200,
                [],
                $this->plates->render('admin::edit-post', [
                    'post' => $post
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log the post does not exist
            return new HaltResponse(
                303,
                ['Location' => Route::DASHBOARD]
            );  
        }
    }
}
