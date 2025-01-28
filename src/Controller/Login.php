<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller;

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Service\Auth;
use App\Service\Users;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Controller\RouteTrait;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;

class Login implements ControllerInterface
{
    use RouteTrait;

    protected Auth $auth;
    protected Users $users;
    protected LoggerInterface $logger;
	protected $flash;
    protected Handlebars $handlebars;

    public function __construct(flash $flash, Handlebars $handlebars, Auth $auth, Users $users, LoggerInterface $logger)
    {
        $this->auth = $auth;
        $this->users = $users;
        $this->logger = $logger;
		$this->flash = $flash;
		$this->handlebars = $handlebars;
    }

    protected function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
		$template = "{{> login}}";
		$phpStr = LightnCandy::compile($template, $this->handlebars->getConfig());

		$data = array(
			'htmlClass'	=> 'login',
			'title' => 'Login',
			'login_url' => Route::LOGIN
		);

		$renderer = LightnCandy::prepare($phpStr);
        return new Response(
            200, 
            [], 
            $renderer($data),
        );
    }

    protected function post(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();

        $username = $params['username'] ?? null;
        $password = $params['password'] ?? null;

        if (empty($username) || empty($password) || false === $this->auth->verifyUsername($username, $password)) {
            if (!empty($username) && !empty($password)) {
                $this->logger->warning(sprintf("Invalid credentials for user %s", $username));
            }
            flash()->error(['Invalid credentials']);
            $output = flash()->display();
            
			$template = "{{> login}}";
			$phpStr = LightnCandy::compile($template, $this->handlebars->getConfig());

			$data = array(
				'htmlClass'	=> 'login',
				'title' => 'Login',
				'login_url' => Route::LOGIN,
				'flash' => $output
			);

			$renderer = LightnCandy::prepare($phpStr);
            return new Response(
                400, 
                [],
                $renderer($data),
            );
        }
        $_SESSION['username'] = $username;
        try {
            $this->users->updateLastLogin($username);
        } catch (DatabaseException $e) {
            $this->logger->error(sprintf("Update last login: %s", $e->getMessage()));
        }
        $this->logger->info(sprintf("Login user %s", $username));
        // Redirect to ADMIN_URL
        return new Response(
            303,
            ['Location' => Route::DASHBOARD]
        );
    }
}
