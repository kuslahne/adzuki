<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;
session_start();
use App\Controller\Blog;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RedBeanPHP\Facade as R;
use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\Posts as ServicePosts;
use Ausi\SlugGenerator\SlugGenerator;
use App\Model\Db;

final class BlogTest extends TestCase
{
   
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    private Blog $blog;
    
	protected $flash;
    protected Handlebars $handlebars;
    protected ServicePosts $posts;
    protected SlugGenerator $generator;

    public function setUp(): void
    {
		$path = dirname(__DIR__, 2).'/config';
		$c = require $path.'/config.php';
		$db = new Db($c['database']['pdo_dsn']);
		$this->handlebars = new Handlebars();
        $this->flash = new Flash();
		
		$this->generator = new SlugGenerator();
		$this->bean = new R();
		dump($this->bean);
		$this->posts = new ServicePosts($this->generator, $this->flash, $this->handlebars);
        $this->blog = new Blog($this->flash, $this->handlebars, $this->posts);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testExecuteReturn200(): void
    {
        $response = $this->blog->execute($this->request, $this->response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    //public function testExecuteHasHomeViewBody(): void
    //{
        //$response = $this->blog->execute($this->request, $this->response);
        //$this->assertEquals($this->plates->render('home'), (string) $response->getBody());
    //}
}
