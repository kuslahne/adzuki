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
use App\Model\Post;
use App\Service\Posts as ServicePosts;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Update implements ControllerInterface
{
    protected Engine $plates;
    protected ServicePosts $posts;

    public function __construct(Engine $plates, ServicePosts $posts)
    {
        $this->plates = $plates;
        $this->posts = $posts;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $params = $request->getParsedBody();

        $post = $this->posts->get($id);
        $title = $params['title'] ?? '';
        $content = $params['content'] ?? '';
        $published = (int)$params['published'];
        //var_dump($params); exit;
        
        $errors = $this->checkParams($title, $content);
        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $this->plates->render('admin::edit-post', array_merge($errors, [
                    'post' => $post
                ]))
            );
        }

        try {
            $this->posts->update($id, $published, $title, $content);
            return new Response(
                200,
                [],
                $this->plates->render('admin::edit-post', [
                    'result' => sprintf("The post %s has been successfully updated!", $post->title),
                    'post' => $post
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log error
            return new Response(
                500,
                [],
                $this->plates->render('admin::edit-post', [
                    'error' => 'Error updating the post, please contact the administrator',
                    'user' => $user
                ])
            );
        }
    }

    /**
     * Check the parameters and returns errors if any
     * 
     * @return array<string, array<string, string>>
     */
    private function checkParams(string $title, string $content): array
    {
        if (empty($content)) {
            return [];
        }
        if (strlen($content) < Post::MIN_CONTENT_LENGTH) {
            return [
                'formErrors' => [
                    'content' => 'The content must be at least 10 characters long'
                ]
            ];
        }
        return [];
    }
}
