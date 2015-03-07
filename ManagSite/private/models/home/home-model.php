<?php 
class HomeModel extends MainModel {
	

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

	public function validade_register_form(){
		if(!empty($this->userdata)) //verifica se n eh login
			return;

		// Configura os dados do formulário
		$this->form_data = array();

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
			foreach ( $_POST as $key => $value ) {
				if ( empty( $value ) ) {
					$this->form_msg = format_msg('danger', 'There are empty fields.');
					return;	
				}	
				$this->form_data[$key] = $value;
			}
		}

		if( empty( $this->form_data ) ) {
			return;
		}

		// basic validation
		if(!preg_match('/^[a-zA-Z0-9_-]{2,}$/', chk_array($this->form_data, 'user'))){
			$this->form_msg = format_msg('danger', 'Invalid UserId, use only letters, _, - and numbers, or use atleast 2 characters.');
			return;	
		}else if(!preg_match('/^[ a-zA-Z0-9_-]{2,}$/', chk_array($this->form_data, 'user_name'))){
			$this->form_msg = format_msg('danger', 'Invalid User Name, use only letters, _, -, spaces and numbers, or use atleast 2 characters.');
			return;	
		}else if(!preg_match('/^[a-zA-Z0-9_-]{2,}$/', chk_array($this->form_data, 'user_password'))){
			$this->form_msg = format_msg('danger', 'Invalid User Password, use only letters, _, - and numbers, or use atleast 2 characters.');
			return;	
		}

		$db_check_user = $this->db->query (
			'SELECT * FROM user WHERE user = ?', 
			array( 
				chk_array( $this->form_data, 'user')		
			) 
		);

		if ( ! $db_check_user ) {
			$this->form_msg = format_msg('danger', 'Internal error.');
			return;
		}

		if(!empty($db_check_user->fetch())){
			$this->form_msg = format_msg('danger', 'Already there is a User with this userid.');
			return;
		}

		// Criptografa o password do usuario
		$password_hash = new PasswordHash(8, FALSE);
		$password = $password_hash->HashPassword( $this->form_data['user_password'] );

		// Configura as permissoes
		$permissions = chk_array($this->form_data, 'user_permissions');
		if(!$permissions) $permissions = 'any'; //default para todos [nao estou usando isso]

		$permissions = array_map('trim', explode(',', $permissions));
		$permissions = array_unique( $permissions );
		$permissions = array_filter( $permissions );
		$permissions = serialize( $permissions );

		// insert
		$query = $this->db->insert('user', [
			'user'=>chk_array($this->form_data, 'user'),
			'user_name'=>chk_array($this->form_data, 'user_name'),
			'user_password'=>$password,
			'user_session_id'=>md5(time()),
			'user_permissions'=>$permissions
		]);

		if (!$query) {
			$this->form_msg = format_msg('danger', 'Internal error. Data has not been sent.');
			return;
		}

		$this->form_msg = format_msg('success', 'User successfuly registered.');
	}

	private function add_midia($midia, $midia_which){
		if(empty($midia)) return;

		extract($midia);
		$this->midias[$midia_which][$midia_tab][] = [
			'midia_id'=> $midia_id,
			'midia_which'=> $midia_which,
			'midia_name'=> $midia_name,
			'midia_tab'=> $midia_tab,
			'midia_comments'=> $midia_comments,
			'midia_week_day'=> $midia_week_day,
			'midia_date'=> $midia_date,
			'links'=> $links
		];
	}

	public function load_data(){
		$this->midias = [];
		$tables = [
			[
				'which'=>'cine_movie',
				'tab'=>'not_released',
				'orderby'=>'-midia_date DESC' 
			],
			[
				'which'=>'game',
				'tab'=>'not_released',
				'orderby'=>'-midia_date DESC'
			],
		];

		foreach ($tables as $value) {
			$tmp = [];

			$query = $this->db->query(
				"SELECT *,m.midia_which,m.user_id_fk,l.link_id,l.midia_fk FROM midia m LEFT JOIN link l 
				 	ON m.midia_id = l.midia_fk WHERE user_id_fk = ? AND midia_which = ? AND midia_tab = ? ORDER BY {$value['orderby']}",
				array( $this->userdata['user_id'], $value['which'], $value['tab'] )
			);

			if(!$query){
				$this->form_msg = format_msg('danger', 'Internal error.');
				return;
			}

			// Obtém os dados
			while($midia = $query->fetch(PDO::FETCH_ASSOC)){
				if(empty($tmp) or $tmp['midia_id'] !== $midia['midia_id']){
					$this->add_midia($tmp, $value['which']);
					$tmp = $midia;
					$tmp['links'] = [ [ 'link_address'=>$tmp['link_address'] ] ]; 
				}else{
					$tmp['links'][] = [ 'links_address'=>$midia['link_address'] ];
				}
			}
			$this->add_midia($tmp, $value['which']); //adiciona a ultima midia
		}	
	}
}