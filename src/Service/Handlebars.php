<?php
declare(strict_types=1);

namespace App\Service;

use LightnCandy\LightnCandy;

class Handlebars
{	
	public function getConfig() {
		 $options = array(
			// Used compile flags
			'flags' => LightnCandy::FLAG_RUNTIMEPARTIAL,
			'partialresolver' => function ($cx, $name) {
				$path = dirname(__DIR__, 1)."/../src/templates/";
				if (file_exists("$path$name.tpl")) {
					return file_get_contents("$path$name.tpl");
				}
				return "[partial (file:$path$name.tpl) not found]";
			}
		);
		
		return $options;
	 }
}
