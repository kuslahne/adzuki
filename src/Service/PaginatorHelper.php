<?php
declare(strict_types=1);

namespace App\Service;

class PaginatorHelper
{	
	public function cleanParams($paginator) {
		 $newPaginator = [];
			foreach ($paginator as $key => $item) {
				if ($item && $key == 'prev') {
					if($item['name'] == 1) {
						$item['url'] = $_SERVER['PATH_INFO'];
					}					
				}
				if ($item && $key == 'pages') {
					foreach ($item as $k => $nav) {
						if($nav['name'] == 1) {
							$nav['url'] = $_SERVER['PATH_INFO'];
							$item[$k] = $nav;							
						}
					}
				}
				$newPaginator[$key] = $item; 
			}
		
		return $newPaginator;
	 }
	 
	 public function getPaginator($total, $perpage, $start, $size, $params, $reqPage, $path = null)
	 {
		$current = $reqPage; // Current page
		$neighbours = 2; // Neighbours links beside current page

		$y = new \dotzero\YPaginator($total, $perpage, $current);
		
		if($path){
			$serverPath = $_SERVER['HTTP_ORIGIN'] . $path;
		} else {
			$serverPath = $_SERVER['PATH_INFO'];
		}
		
		$paginator = $y
			->setNeighbours($neighbours)
			->setUrlMask('#num#')
			->setUrlTemplate($serverPath.'?page=#num#')
			->getPaginator();

		return $this->cleanParams($paginator);
	 }
	 
	 public function getStart($size, $params)
	 {
		$start = (int) ($params['start'] ?? 0);
		if (!isset($params['page'])) {
			$reqPage = 1;
			$start = $start * $reqPage;
		} else {
			$reqPage = $params['page'];
			$start = $size * ($reqPage - 1);
		}
		return [$start, $reqPage];
	 }
}
