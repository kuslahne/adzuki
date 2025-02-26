<?php
declare(strict_types=1);

namespace App\Service;

use LightnCandy\LightnCandy;

class Handlebars
{	
	public function getConfig() {
		 $options = array(
			// Used compile flags
			'flags' => LightnCandy::FLAG_RUNTIMEPARTIAL | LightnCandy::FLAG_ELSE | LightnCandy::FLAG_HANDLEBARS,
			'partialresolver' => function ($cx, $name) {
				$path = dirname(__DIR__, 1)."/templates/";
				if (substr($name, 0, 6) == 'admin_') {
					$name = substr($name, 6);
					$path = dirname(__DIR__, 1)."/templates/admin/";
				}
				if (file_exists("$path$name.tpl")) {
					return file_get_contents("$path$name.tpl");
				}
				return "[partial (file:$path$name.tpl) not found]";
			},
			'helpers' => array(
			  "isequal" => function ($arg1, $arg2) {
				return ($arg1 === $arg2) ? true : false;
			  },
			  "isactive" => function ($arg1, $arg2) {
				return ($arg1 === $arg2) ? 'active' : '';
			  },
			  "ariacurrent" => function ($arg1, $arg2) {
				return ($arg1 === $arg2) ? 'aria-current="page"' : '';
			  },
			  "json_encode" => function ($arg1) {
				return (string)json_encode($arg1);
			  },
			  "isChecked" => function ($arg1) {
				return ($arg1 == 1) ? 'checked' : '';
			  },
              "isSelected" => function ($arg1, $arg2) {
				return ($arg1 == $arg2) ? 'selected' : '';
			  }
			)
		);
		
		return $options;
	 }
	 
	 public function renderer($tpl)
	 {
		$template = "{{> ". $tpl ."}}";
		$phpStr = LightnCandy::compile($template, $this->getConfig());
		$renderer = LightnCandy::prepare($phpStr);
		return $renderer;
	 }
}
