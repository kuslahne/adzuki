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
use App\Service\Posts as ServicePosts;
use App\Service\Categories as ServiceCategories;
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
    const POSTS_PER_PAGE = 2;

    protected ServicePosts $posts;
    protected ServiceCategories $categories;
    protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(flash $flash, Handlebars $handlebars, ServicePosts $posts, ServiceCategories $categories, PaginatorHelper $paging)
    {
        $this->flash = $flash;
	    $this->handlebars = $handlebars;
	    $this->posts = $posts;
        $this->categories = $categories;
	    $this->paging = $paging;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getAttribute('id', null);
        $output = flash()->display();
        
        if (empty($id)) {
		    $params = $request->getQueryParams();
		    $total = $this->posts->getTotalPosts(); // Total items
		    $perpage = self::POSTS_PER_PAGE; // Items per page
		    $size = (int) ($params['size'] ?? $perpage);
					    
		    list($start, $reqPage) = $this->paging->getStart($size, $params);					
		    $paginator = $this->paging->getPaginator($total, $perpage, $start, $size, $params, $reqPage);
		    $renderer = $this->handlebars->renderer('admin/posts');
		    $data = array(
			    'blog' => [                    
				    'total' => $total,
				    'class'	=> 'blog',
				    'start' => $start,
				    'size'  => $size,
				    'posts' => $this->posts->getAllPosts($start, $size),
				    'page'	=> 'posts',
			    ],
                'categories' => $this->categories->getCategories(),			
			    'repo'  => 'post',
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
			    $renderer($data)
		    );
        }
		
	    $data = array(
		    'blog' => [                    
			    'class'	=> 'blog',
			    'page'	=> 'posts'
		    ],
            'categories' => $this->categories->getCategories(),	
		    'session' => [
			    'username' => $_SESSION['username'],
			    'link_logout' => \App\Config\Route::LOGOUT
		    ],
		    'formErrors' => null,
		    'flash' => $output
	    );


        try {
            $renderer = $this->handlebars->renderer('admin/post_edit');
            $post = $this->posts->get((int) $id);
            $data['post'] = $post;
            $data['title'] = 'Edit Post';
            return new Response(
                200,
                [],
                $renderer($data)
            );
        } catch (DatabaseException $e) {
            // @todo log the post does not exist
            return new HaltResponse(
                303,
                ['Location' => Route::DASHBOARD]
            );  
        }
    }
}
