<?php
declare(strict_types=1);

namespace App\Controller;

use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;

class Blog implements ControllerInterface
{
    protected Engine $plates;
    protected $flash;
    protected Handlebars $handlebars;

    public function __construct(Engine $plates, flash $flash, Handlebars $handlebars)
    {
        $this->plates = $plates;
        $this->flash = $flash;
		$this->handlebars = $handlebars;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
		$template = "{{> blog_home}}";
		$phpStr = LightnCandy::compile($template, $this->handlebars->getConfig());

		//echo "Generated PHP Code:\n$phpStr\n";

		// Input Data:
		$data = array(
		  'Data' => [
				'Hey',
				'How', 
				'Are',
				'You'
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
