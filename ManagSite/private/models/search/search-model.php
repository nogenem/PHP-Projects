<?php
class SearchModel extends MainModel {
	/**
	 * Construtor para essa classe
	 *
	 * Configura o DB, o controlador, os parâmetros e dados do usuário.
	 *
	 * @since 0.1
	 * @access public
	 * @param object $db Objeto da nossa conexão PDO
	 * @param object $controller Objeto do controlador
	 */
	public function __construct( $db = false, $controller = null ) {
		// Configura o DB (PDO)
		$this->db = $db;
		
		// Configura o controlador
		$this->controller = $controller;

		// Configura os parâmetros
		$this->parametros = $this->controller->parametros;

		// Configura os dados do usuário
		$this->userdata = $this->controller->userdata;

		// Configura a url base desse modelo
		$this->url = $controller->url;
	}

	public function validate_search_form(){
		// Configura os dados do formulário
		$midia_name = NULL;

		if ('GET' == $_SERVER['REQUEST_METHOD'] && !empty($_GET) && chk_array($_GET, 'midia_name')) {
			$midia_name = $_GET['midia_name'];
		}else{
			$this->form_msg = format_msg('danger', 'Nothing was found.');
			return;
		}

		$this->load_data($midia_name);
	}

	public function load_data($midia_name){

		$query = $this->db->query(
			'SELECT midia_id, midia_name, midia_which, midia_tab 
				FROM midia
					WHERE LCASE(midia_name) LIKE LCASE(?) ORDER BY midia_name',
			['%' .$midia_name.'%']
		);

		if(!$query){
			$this->form_msg = format_msg('danger', 'Internal error.');
			return;
		}

		$this->midias = [];

		// Obtém os dados
		while($midia = $query->fetch(PDO::FETCH_ASSOC))
			$this->midias[] = $midia;

		if(empty($this->midia)){
			$this->form_msg = format_msg('danger', 'Nothing was found.');
			return;
		}
	}
}