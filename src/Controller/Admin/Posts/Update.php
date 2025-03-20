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
    protected ServicePosts $posts;
	protected $flash;
    protected Handlebars $handlebars;

    public function __construct(ServicePosts $posts, flash $flash, Handlebars $handlebars)
    {
        $this->posts = $posts;
		$this->flash = $flash;
		$this->handlebars = $handlebars;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $params = $request->getParsedBody();

        $post = $this->posts->get($id);
        $title = $params['title'] ?? '';
        $content = $params['content'] ?? '';
        $slug = $params['slug'] ?? '';
        $published = (int)$params['published'];
        $categoryId = (int)$params['category'];
        $tags = $params['tag'];
        $oldTags = $params['tag_ori'];

		$errors = $this->checkParams($title, $content, $tags);
		
		$output = flash()->display();
		$renderer = $this->handlebars->renderer('admin/post_edit');
		$data = array(
			'blog' => [                    
				'total' => $this->posts->getTotalPosts(),
				'class'	=> 'blog',
				'page'	=> 'posts'
			],
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => $errors,
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
            $this->posts->update($id, $published, $title, $content, $slug, $categoryId, $tags, $oldTags);
            return new Response(
                200,
                [],
                $renderer($data)
            );
        } catch (DatabaseException $e) {
            // @todo log error
            flash()->error(['Error updating the post, please contact the administrator']);
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
    private function checkParams(string $title, string $content, string $tags): array
    {
        if (empty($content)) {
            return [];
        }
        if (strlen($content) < Post::MIN_CONTENT_LENGTH) {
			flash()->error(['The content must be at least 10 characters long']);
            return [
                'formErrors' => [
                    'content' => 'The content must be at least 10 characters long'
                ]
            ];
        }
        return [];
    }
}
