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
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\PaginatorHelper;

class Create implements ControllerInterface
{
    protected Posts $posts;
	protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(
		Posts $posts,
		flash $flash, 
		Handlebars $handlebars,
		PaginatorHelper $paging
	)
    {
		$this->flash = $flash;
		$this->handlebars = $handlebars;
		$this->posts = $posts; 
		$this->paging = $paging;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
		$output = flash()->display();
        $params = $request->getParsedBody();
        // If no POST params just render new-post view
        $data = array(
			'blog' => [                    
				'class'	=> 'blog',
				'page'	=> 'posts'					
			],
			'title' => 'Add Post',
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => null,
			'flash' => $output,
		);
		$renderer = $this->handlebars->renderer('admin/post_new');
        if (empty($params)) {			
            return new Response(
                200,
                [],
                $renderer($data),
            );
        }

        $title = $params['title'] ?? '';
        $content = $params['content'] ?? '';
        $published = $params['published'] ?? 0;
        $slug = $params['slug'] ?? '';
        
        $errors = $this->validateParams($title, $content);
        if (!empty($errors)) {
			$data['flash'] = flash()->display();
            return new Response(
                400,
                [],
                $renderer($data),
            );
        }

        try {
            $this->posts->create($title, $content, $published, $slug);
			flash()->success([sprintf("The post %s has been successfully created!", $title)]);

            return new Response(
                201,
                [],
                $renderer($data),
            );
        } catch (DatabaseException $e) {
            // @todo log error
            flash()->error(['Error adding the post, please contact the administrator']);
            $data['flash'] = flash()->display();
            return new Response(
                500,
                [],
                $renderer($data)
            ); 
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function validateParams(string $title, string $content): array
    {
        if (empty($title)) {
			flash()->error(['The title cannot be empty']);
            return [
                'formErrors' => [
                    'title' => 'The title cannot be empty'
                ]
            ];
        }
        if (empty($content)) {
			flash()->error(['The content cannot be empty']);
            return [
                'formErrors' => [
                    'content' => 'The content cannot be empty'
                ]
            ];
        }
        if ($this->posts->exists($title)) {
			flash()->error(['The title already exists!']);
            return [
                'formErrors' => [
                    'title' => 'The title already exists!'
                ]
            ];
        }
        if (strlen($content) < Post::MIN_CONTENT_LENGTH) {
			flash()->error([sprintf("The content must be at least %d characters long", Post::MIN_CONTENT_LENGTH)]);
            return [
                'formErrors' => [
                    'content' => sprintf("The content must be at least %d characters long", Post::MIN_CONTENT_LENGTH)
                ]
            ];
        }
        return [];
    }
}
