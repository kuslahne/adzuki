<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller\Admin\Categories;

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Service\Categories as ServiceCategories;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Response\HaltResponse;

use League\Plates\Engine;

use function Tamtamchik\SimpleFlash\flash;
use \Tamtamchik\SimpleFlash\Flash;
use LightnCandy\LightnCandy;
use App\Service\Handlebars;
use App\Service\PaginatorHelper;

class Read implements ControllerInterface
{
    const CATEGORIES_PER_PAGE = 5;

    protected Engine $plates;
    protected ServiceCategories $categories;
    protected $flash;
    protected Handlebars $handlebars;
    protected PaginatorHelper $paging;

    public function __construct(flash $flash, Handlebars $handlebars, PaginatorHelper $paging, Engine $plates, ServiceCategories $categories)
    {
        $this->plates = $plates;
        $this->categories = $categories;
        $this->flash = $flash;
	    $this->handlebars = $handlebars; 
	    $this->paging = $paging;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
	$output = $this->flash->display();
        $id = $request->getAttribute('id', null);
        if (empty($id)) {

		$params = $request->getQueryParams();
		$total = $this->categories->getTotalCategories(); // Total items
		$perpage = self::CATEGORIES_PER_PAGE; // Items per page
		$size = (int) ($params['size'] ?? $perpage);
					
		list($start, $reqPage) = $this->paging->getStart($size, $params);					
		$paginator = $this->paging->getPaginator($total, $perpage, $start, $size, $params, $reqPage);
		$renderer = $this->handlebars->renderer('admin/categories');
		$data = array(
			'total' => $total,
			'class'	=> 'categories',
			'start' => $start,
			'size'  => $size,
			'categories' => $this->categories->getAllCategories($start, $size),
			'page'	=> 'categories',
			'repo'  => 'category',
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
		'class'	=> 'categories',
		'page'	=> 'categories',
		'session' => [
			'username' => $_SESSION['username'],
			'link_logout' => \App\Config\Route::LOGOUT
		],
		'formErrors' => null,
		'flash' => $output
	);
        try {
	    $renderer = $this->handlebars->renderer('admin/category_edit');
            $category = $this->categories->get((int) $id);
            $data['item'] = $category;
            $data['title'] = 'Edit Category';
            return new Response(
                200,
                [],
                $renderer($data)
            );
        } catch (DatabaseException $e) {
            // @todo log the category does not exist
            return new HaltResponse(
                303,
                ['Location' => Route::DASHBOARD]
            );  
        }
    }
}
