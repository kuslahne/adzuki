<?php
declare(strict_types=1);

namespace App\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\Posts as ServicePosts;

class Blog implements ControllerInterface
{
	const POSTS_PER_PAGE = 10;
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
		$start = (int) ($params['start'] ?? 0);
		$size = (int) ($params['size'] ?? self::POSTS_PER_PAGE);
            
		$template = "{{> blog_home}}";
		$phpStr = LightnCandy::compile($template, $this->handlebars->getConfig());

		//echo "Generated PHP Code:\n$phpStr\n";

		// Input Data:
		$data = array(
			'blog' => [                    
				'start' => $start,
				'size' => $size,
				'total' => $this->posts->getTotalPublished(),
				'recent_posts' => $this->posts->getRecentPosts(),
				'posts' => $this->posts->getAllPublished($start, $size),
				'class'	=> 'blog'
			]
		);

		// Get the render function from the php file
		$renderer = LightnCandy::prepare($phpStr);
				
		return new Response(
			200,
			[],
			$renderer($data)
		);
	}
}
