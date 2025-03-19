<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller\Blog;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\Posts as ServicePosts;

class Article implements ControllerInterface
{
	protected $flash;
    protected Handlebars $handlebars;
    protected ServicePosts $posts;

    public function __construct(flash $flash, Handlebars $handlebars, ServicePosts $posts)
    {
        $this->flash = $flash;
		$this->handlebars = $handlebars;
		$this->posts = $posts;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $slug = $request->getAttribute('slug', null);
//		$slug = $params['slug'] ?? null;
            
		$template = "{{> post_view}}";
		$phpStr = LightnCandy::compile($template, $this->handlebars->getConfig());

		//echo "Generated PHP Code:\n$phpStr\n";
//        dump($params);
        //dd($slug);

		// Input Data:
		$data = array(
			'Data' => [
				'Hey',
				'How', 
				'Are',
				'You'
			],
			'blog' => [                    				
				'class'	=> 'blog'
			],
            'post' => $this->posts->getPostBySlug($slug),
		);
        //dd($data);

		$renderer = LightnCandy::prepare($phpStr);
				
		return new Response(
			200,
			[],
			$renderer($data)
		);
    }
}
