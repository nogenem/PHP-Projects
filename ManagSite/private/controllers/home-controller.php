<?php
/**
 * HomeController - Controlador da home do site
 */
class HomeController extends MainController
{

	/**
	 * Carrega a página "/views/home/home-view.twig"
	 */
    public function index() {
		// Título da página
		$this->title = 'Home';
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo da page e seus metodos
		$modelo = $this->load_model('home/home-model');
		$modelo->validade_register_form();
		$modelo->load_data();

		// Carrega os arquivos do view 
		try{
			$template = $this->twig->loadTemplate('home/home-view.twig');
		  
		  	echo $template->render([
			  	'this'=>$this,
			  	'modelo'=>$modelo,
			  	'midias'=>$modelo->midias,
			  	'now'=>date_create(date('Y-m-d'))
		  	]);  
		} catch (Exception $e) {
		  	die ('ERROR: ' . $e->getMessage());
		}
		
    } // index
	
} // class HomeController