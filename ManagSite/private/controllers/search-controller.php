<?php
/**
 * SearchController - Controlador da page de search do site
 */
class SearchController extends MainController
{

	public $login_required = true;

	/**
	 * Carrega a página "/views/midia/midia-view.twig"
	 */
    public function index() {
		// Configs da page
		$this->title = 'Search';
		
		// Verifica se o usuário está logado
    	if ( $this->login_required === true and !$this->logged_in ) {
			$this->logout();
			$this->goto_page(HOME_URI);
			return;
    	}

		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo da page e seus metodos
		$modelo = $this->load_model('search/search-model');
		$modelo->validate_search_form();

		// Carrega os arquivos do view 
		try{
		  $template = $this->twig->loadTemplate('search/search-view.twig');

		  echo $template->render([
		  	'this'=>$this,
		  	'modelo'=>$modelo,
		  	'midias'=>$modelo->midias,
		  ]);
		} catch (Exception $e) {
		  die ('ERROR: ' . $e->getMessage());
		}
		
    } // index
	
} // class SearchController