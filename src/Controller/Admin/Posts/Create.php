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
use App\Service\Posts;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Create implements ControllerInterface
{
    protected Engine $plates;
    protected Posts $posts;

    public function __construct(Engine $plates, Posts $posts)
    {
        $this->plates = $plates;
        $this->posts = $posts;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();
        // If no POST params just render new-post view
        if (empty($params)) {
            return new Response(
                200,
                [],
                $this->plates->render('admin::new-post')
            );
        }

        $title = $params['title'] ?? '';
        $content = $params['content'] ?? '';
        
        $errors = $this->validateParams($title, $content);
        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $this->plates->render('admin::new-post', array_merge($errors, [
                    'title' => $title
                ]))
            );
        }

        try {
            $this->posts->create($title, $content);
            return new Response(
                201,
                [],
                $this->plates->render('admin::new-post', [
                    'result' => sprintf("The post %s has been successfully created!", $title)
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log error
            return new Response(
                500,
                [],
                $this->plates->render('admin::new-post', [
                    'error' => 'Error adding the post, please contact the administrator'
                ])
            ); 
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function validateParams(string $title, string $content): array
    {
        if (empty($title)) {
            return [
                'formErrors' => [
                    'title' => 'The title cannot be empty'
                ]
            ];
        }
        if (empty($content)) {
            return [
                'formErrors' => [
                    'content' => 'The content cannot be empty'
                ]
            ];
        }
        if ($this->posts->exists($title)) {
            return [
                'formErrors' => [
                    'title' => 'The title already exists!'
                ]
            ];
        }
        if (strlen($content) < Post::MIN_PASSWORD_LENGTH) {
            return [
                'formErrors' => [
                    'content' => sprintf("The content must be at least %d characters long", Post::MIN_PASSWORD_LENGTH)
                ]
            ];
        }
        return [];
    }
}
