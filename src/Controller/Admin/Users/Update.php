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
use App\Service\Users as ServiceUsers;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\PaginatorHelper;
use App\Controller\Admin\Users\Read;

class Update implements ControllerInterface
{
    protected ServiceUsers $users;
	protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(ServiceUsers $users, flash $flash, Handlebars $handlebars, PaginatorHelper $paging)
    {
        $this->users = $users;
		$this->flash = $flash;
		$this->handlebars = $handlebars;
		$this->paging = $paging;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $params = $request->getParsedBody();

        $user = $this->users->get($id);
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';
        $confirmPassword = $params['confirmPassword'] ?? '';
        $active = $params['active'] ?? 'off';
        
        $errors = $this->checkParams($password, $confirmPassword);
        $output = flash()->display();
		$data = array(
			'users' => [                    
				'class'	=> 'user',
				'page'	=> 'users'
			],
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => $errors,
			'flash' => $output
		);
		$renderer = $this->handlebars->renderer('admin/user_edit');

        if (!empty($errors)) {
			$user = $this->users->get((int) $id);
            $data['user'] = $user;	
            $data['title'] = 'Edit User';	
            return new Response(
                400,
                [],
                $renderer($data)
            );
        }

        try {
            $this->users->update($id, $active === 'on' ? true : false, $password);
			flash()->success([sprintf("User %s has been successfully updated!", $username)]);

			return $response
			  ->withHeader('Location', '/admin/users')
			  ->withStatus(302);
        } catch (DatabaseException $e) {
            // @todo log error
            flash()->error(['Error updating the user, please contact the administrator']);
            $data['flash'] = flash()->display();
            return new Response(
                500,
                [],
                $renderer($data)
            );
        }
    }

    /**
     * Check the parameters and returns errors if any
     * 
     * @return array<string, array<string, string>>
     */
    private function checkParams(string $password, string $confirmPassword): array
    {
		//TODO can not use same password as previously
        if (empty($password)) {
			flash()->error(['The password cannot be empty!']);
            return [
                'formErrors' => [
                    'password' => 'The password cannot be empty'
                ]
            ];
        }
		if (empty($confirmPassword)) {
			flash()->error(['The confirm password cannot be empty!']);
            return [
                'formErrors' => [
                    'confirmPassword' => 'The confirm password cannot be empty'
                ]
            ];
        }
        if (strlen($password) < User::MIN_PASSWORD_LENGHT) {
			flash()->error(['The password must be at least 10 characters long']);
            return [
                'formErrors' => [
                    'password' => 'The password must be at least 10 characters long'
                ]
            ];
        }
        if ($password !== $confirmPassword) {
			flash()->error(['The password and the confirm must be equal']);
            return [
                'formErrors' => [
                    'password' => 'The password and the confirm must be equal'
                ]
            ];
        }
        return [];
    }
}
