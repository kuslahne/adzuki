<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Model\User;
use App\Service\Users;
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
    protected Users $users;
    protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(
		Users $users,
		flash $flash,
		Handlebars $handlebars,
		PaginatorHelper $paging
	)
    {
        $this->users = $users;
		$this->flash = $flash;
		$this->handlebars = $handlebars;
		$this->paging = $paging;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
		$output = flash()->display();
        $params = $request->getParsedBody();
		$data = array(
			'users' => [                    
				'class'	=> 'user',
				'page'	=> 'users'
			],
			'title' => 'Add User',
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => null,
			'flash' => $output
		);
        // If no POST params just render new-user view
        $renderer = $this->handlebars->renderer('admin/user_new');
        if (empty($params)) {
            return new Response(
                200,
                [],
                $renderer($data)
            );
        }

        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';
        $confirmPassword = $params['confirmPassword'] ?? '';
        
        $errors = $this->validateParams($username, $password, $confirmPassword);
        if (!empty($errors)) {
			$data['flash'] = flash()->display();
            return new Response(
                400,
                [],
                $renderer($data)
            );
        }

        try {
            $this->users->create($username, $password);
			flash()->success([sprintf("The user %s has been successfully created!", $username)]);
						
			return $response
			  ->withHeader('Location', '/admin/users')
			  ->withStatus(302);
        } catch (DatabaseException $e) {
            // @todo log error
			flash()->error(['Error adding the user, please contact the administrato']);
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
    private function validateParams(string $username, string $password, string $confirmPassword): array
    {
        if (empty($username)) {
			flash()->error(['The username cannot be empty!']);
            return [
                'formErrors' => [
                    'username' => 'The username cannot be empty'
                ]
            ];
        }
        if (empty($password)) {
			flash()->error(['The password cannot be empty!']);
            return [
                'formErrors' => [
                    'password' => 'The password cannot be empty'
                ]
            ];
        }
        if ($this->users->exists($username)) {
			flash()->error(['The username already exists!']);
            return [
                'formErrors' => [
                    'username' => 'The username already exists!'
                ]
            ];
        }
        if (strlen($password) < User::MIN_PASSWORD_LENGHT) {
			flash()->error([sprintf("The password must be at least %d characters long", User::MIN_PASSWORD_LENGHT)]);
            return [
                'formErrors' => [
                    'password' => sprintf("The password must be at least %d characters long", User::MIN_PASSWORD_LENGHT)
                ]
            ];
        }
        if ($password !== $confirmPassword) {
			flash()->error(['The password and the confirm must be equal!']);
            return [
                'formErrors' => [
                    'password' => 'The password and the confirm must be equal'
                ]
            ];
        }
        return [];
    }
}
