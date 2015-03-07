<?php
/**
 * SeriesController - Controlador da page de series do site
 */
class SeriesController extends MainController
{

	public $login_required = true;

	/**
	 * Carrega a página "/views/midia/midia-view.twig"
	 */
    public function index() {
		// Configs da page
		$this->title = 'Series';
		$this->url = HOME_URI.'series';
		$this->view_config = [
			'disabled_fields'=>[],
			'tabs'=>['following', 'not_released', 'already_saw', 'finished'],
		];
		
		// Verifica se o usuário está logado
    	if ( $this->login_required === true and !$this->logged_in ) {
			$this->logout();
			$this->goto_page(HOME_URI);
			return;
    	}

		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo da page e seus metodos
		$modelo = $this->load_model('midia/midia-model');
		$modelo->validate_midia_form();
		$modelo->load_data();
		$modelo->del_midia();

		// Configs do Modal
		$modal_config = ['is_edit'=>false, 'is_del'=>false];
		if(in_array(chk_array($parametros, 0), ['edit','del']) and count($parametros) == 3){
			$modal_config['tab'] = $parametros[1];
		    $modal_config['id'] = (int) $parametros[2];
		    
		    $modal_config['is_edit'] = chk_array($parametros, 0) == 'edit';
		    $modal_config['is_del'] = chk_array($parametros, 0) == 'del';
		}

		// Carrega os arquivos do view 
		try{
		  $template = $this->twig->loadTemplate('midia/midia-view.twig');

		  echo $template->render([
		  	'this'=>$this,
		  	'modelo'=>$modelo,
		  	'midias'=>$modelo->midias,
		  	'modal_config'=>$modal_config,
		  	'full_url'=>$_SERVER['REQUEST_URI']
		  ]);
		} catch (Exception $e) {
		  die ('ERROR: ' . $e->getMessage());
		}
		
    } // index
	
} // class SeriesController