<?php 

class MyCoreExtension extends Twig_Extension_Core
{

	public function __construct(){}

	public function getFilters(){
		return array_merge(
			parent::getFilters(),
			[
				new Twig_SimpleFilter('correct', 'correct_phrase'),
				new Twig_SimpleFilter('contains', function($haystack, $needle){
					return (strpos($haystack, $needle)!==FALSE);
				}),
				new Twig_SimpleFilter('format_date', 'format_date'),
				new Twig_SimpleFilter('is_valid_date', 'is_valid_date'),
				new Twig_SimpleFilter('format_edit_url', 'format_midia_edit_url'),
				new Twig_SimpleFilter('get_host', 'get_host')
			]
		);
	}

	public function getFunctions(){
		return array_merge(
			parent::getFunctions(),
			[
				new Twig_SimpleFunction('php_*', 
					function(){
						$arg_list = func_get_args();
						$function = array_shift($arg_list);

						return call_user_func_array($function, $arg_list);
					},
					['pre_escape'=>'html', 'is_safe'=>['html']] 
				),
			]
		);
	}

	public function getGlobals(){
		return array_merge(
			parent::getGlobals(),
			[
				'HOME_URI'=> HOME_URI,
				'PRIVATE_URI'=> PRIVATE_URI,
				'PHP_EOL'=>PHP_EOL
			]
		);
	}
}