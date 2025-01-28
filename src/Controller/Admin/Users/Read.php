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
use App\Service\Users as ServiceUsers;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Response\HaltResponse;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\PaginatorHelper;

class Read implements ControllerInterface
{
    const USERS_PER_PAGE = 2;
    protected ServiceUsers $users;
    
	protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(
		ServiceUsers $users,
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
        $id = $request->getAttribute('id', null);
        $output = flash()->display();
        if (empty($id)) {
            $params = $request->getQueryParams();
			$total = $this->users->getTotalUsers(); // Total items
            $perpage = self::USERS_PER_PAGE; // Items per page            
            $size = (int) ($params['size'] ?? $perpage);            			
						
			list($start, $reqPage) = $this->paging->getStart($size, $params);					
			$paginator = $this->paging->getPaginator($total, $perpage, $start, $size, $params, $reqPage);
			$renderer = $this->handlebars->renderer('admin/users');
			$data = array(
				'users' => [                    
					'total' => $total,
					'class'	=> 'user',
					'page'	=> 'users',
					'all' => $this->users->getAllUsers($start, $size)					
				],
				'session' => [
					'username' => $_SESSION['username'],
					'link_logout' => \App\Config\Route::LOGOUT
				],
				'formErrors' => null,
				'flash' => $output,
				'pagination' => $paginator
			);
			
            return new Response(
                200,
                [],
                $renderer($data),
			);
        }
		
		$data = array(
			'users' => [                    
				'class'	=> 'user',
				'page'	=> 'users'
			],
			'session' => [
				'username' => $_SESSION['username'],
				'link_logout' => \App\Config\Route::LOGOUT
			],
			'formErrors' => null,
			'flash' => $output
		);
		
        try {
			$renderer = $this->handlebars->renderer('admin/user_edit');
            $user = $this->users->get((int) $id);
            $data['user'] = $user;
            $data['title'] = 'Edit User';
            $data['editUser'] = true;
            return new Response(
                200,
                [],
                $renderer($data)               
            );
        } catch (DatabaseException $e) {
            // @todo log the user does not exist
            return new HaltResponse(
                303,
                ['Location' => Route::DASHBOARD]
            );  
        }
    }
}
