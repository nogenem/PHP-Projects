<?php
class MidiaModel extends MainModel {

	/**
	 * Conta a quantidade de panels para poder ser usado
	 * no ID de cada um.
	 */
	public static $panel_count = 0;

	private $accordion = 
'<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
        	<a data-toggle="collapse" data-parent="#acc_:tab" href="#collapse:count">:title :complement</a>
        </h4>
    </div>
    <div id="collapse:count" class="panel-collapse collapse :collapsed" aria-expanded=":expanded">
        <div class="panel-body">
        	<div class="panel panel-default">
			  <div class="panel-heading">Comments</div>
			  <div class="panel-body">
			    :comments
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading">Links</div>
			  <div class="panel-body links-over-flow">
			    :links
			  </div>
			</div>
			<div class="accordion_btns">
				<a href=":base_url/index/edit/:tab/:id" class="btn btn-primary">Edit</a>
				<a href=":base_url/index/del/:tab/:id" class="btn btn-primary">Delete</a>
			</div>
        </div>
    </div>
</div>';

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

	public function validate_midia_form () {
		// Configura os dados do formulário
		$this->form_data = array();

		// Variaveis auxiliares
		$links; $link; $last_id;

		// Campos q podem vir em branco
		$can_be_empty = ['midia_comments', 'midia_links', 'midia_date'];
		
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
			foreach ( $_POST as $key => $value ) {
				
				if ( !in_array($key, $can_be_empty) AND empty( $value ) ) {
					$this->form_msg = format_msg('danger', 'There are empty fields. Data has not been sent.');
					return;	
				}			

				if($key == 'midia_week_day' and is_array($value)){
					if(isset($value[0]))
						$value = $value[0] . (isset($value[1]) ? '-'.$value[1] : '');
					else
						$value = NULL;
				}
				if($key == 'midia_date' AND chk_array($_POST, 'midia_date') !== NULL){
					$value = format_date($value, 'd/m/Y', 'Y/m/d'); 
				}

				if($key == 'midia_links'){
					$links = (empty($value) ? [] : explode(PHP_EOL, trim($value," \t")));
				}else{
					$this->form_data[$key] = $value;
				}
			}
			$this->form_data['user_id_fk'] = $this->userdata['user_id'];
			$this->form_data['midia_which'] = rtrim(str_replace(' ', '_', strtolower($this->controller->title)), 's');
		} else {
			return;
		}

		if( !is_valid_date(chk_array($this->form_data, 'midia_date')) )
			$this->form_data['midia_date'] = NULL;

		$midia_id = chk_array($this->parametros, 2);
		
		// Verifica se eh um update ou insert
		if($midia_id){
			// update
			$query = $this->db->update('midia', 'midia_id', $midia_id, $this->form_data);

			if(!$query){
				$this->form_msg = format_msg('danger', 'Internal error. Data has not been modified.');
				return;
			}

			// deleta os links antigos da midia
			$query = $this->db->delete('link', 'midia_fk', $midia_id);

			if(!$query){
				$this->form_msg = format_msg('danger', 'Internal error. Links has not been modified.');
				return;
			}
		}else{
			// verificaçao se ja existe uma midia com o ms midia_name e midia_which
			$db_midia = $this->db->query(
				'SELECT midia_id FROM midia 
					WHERE LCASE(midia_name) = LCASE(?) AND midia_which = ? LIMIT 1',
				[$this->form_data['midia_name'], $this->form_data['midia_name']]
			);

			if(!$db_midia){
				$this->form_msg = format_msg('danger', 'Internal error. Data has not been sent.');
				return;
			}

			$fetch = $db_midia->fetch(PDO::FETCH_ASSOC);

			if(chk_array($fetch, 'midia_id') !== NULL){
				$article = get_article($this->form_data['midia_which']);
				$this->form_msg = format_msg('danger', "There is already {$article} {$this->form_data['midia_which']} with this name.");
				return;
			}

			// insert
			$query = $this->db->insert('midia', $this->form_data);

			if (!$query) {
				$this->form_msg = format_msg('danger', 'Internal error. Data has not been sent.');
				return;
			}
		}

		// Caso seja um update, o id eh o id da midia q esta sendo editada
		// Caso contrario, o id eh o id da ultima midia adicionada
		$id = ($midia_id ? $midia_id : $this->db->last_id);
		foreach ($links as $url) {
			if(!empty($url)){
				$link = [
					'link_address'=>correct_url($url),
					'midia_fk'=>$id
				];

				$query = $this->db->insert('link', $link);
			}
		}

		$this->form_msg = format_msg('success', rtrim($this->controller->title,'s').
				' successfully '. ($midia_id ? 'updated' : 'added') .'.');	
	}

	/**
	 *
	 */
	public function del_midia(){
		$midia_id = NULL;
		$params = $this->parametros;

		if(chk_array($params, 0) == 'del'){
			if (is_numeric( chk_array( $params, 2 ) )
					&& chk_array( $params, 3 ) == 'confirma') {

				$midia_id = chk_array( $params, 2 );
			}
		}

		if(!empty($midia_id)){
			$midia_id = (int)$midia_id;
			
			// Deleta a midia
			$query = $this->db->delete('midia', 'midia_id', $midia_id);

			if(!$query){
				$this->form_msg = format_msg('danger', 'Internal error. Data has not been deleted.');
				return;
			}
			$this->controller->goto_page($this->url);
		}
	}

	private function add_midia($midia){
		extract($midia);
		if(isset($this->midias[$midia_tab]) AND chk_array( $this->midias[$midia_tab], $midia_id ) !== NULL){
			$this->midias[$midia_tab][$midia_id]['links'][] = ['link_address'=>$link_address];
		}else{
			$this->midias[$midia_tab][$midia_id] = [
				'midia_name'=>$midia_name,
				'midia_comments'=>$midia_comments,
				'midia_week_day'=>$midia_week_day,
				'midia_date'=>$midia_date,
				'links'=>[['link_address'=>$link_address]]
			];
		}
	}

	public function load_data(){
		$query = $this->db->query(
			'SELECT *,m.midia_which,m.user_id_fk,l.link_id,l.midia_fk FROM midia m LEFT JOIN link l 
			 	ON m.midia_id = l.midia_fk WHERE user_id_fk = ? AND midia_which = ? ORDER BY midia_name',
			[ $this->userdata['user_id'], correct_phrase($this->controller->title, 'which') ]
		);

		if(!$query){
			$this->form_msg = format_msg('danger', 'Internal error.');
			return;
		}

		while($midia = $query->fetch(PDO::FETCH_ASSOC))
			$this->add_midia($midia);
	}

	public function create_accordion($tab, $midia){
		self::$panel_count += 1;
		
		$links = '<ul>';
		foreach ($midia['links'] as $link) {
			$links .= '<li><a href="'.$link['link_address'].'" target="_blank">' . get_host($link['link_address']) . '</a><br></li>';
		}
		$links .= '</ul>';

		$complement = '';

		if(chk_array( $midia, 'midia_week_day' ) !== NULL)
			$complement .= '['. chk_array( $midia, 'midia_week_day' ) .']';
		if( is_valid_date(chk_array($midia, 'midia_date')) )
			$complement .= ' ['. $this->inverte_data(chk_array( $midia, 'midia_date' )) .']';

		$complement = trim($complement);

		$collapsed = ''; $expanded = 'false';
		if(chk_array($this->parametros, 2)){
			if($this->parametros[2] == $midia['midia_id']){
				$collapsed = 'in';
				$expanded = 'true';
			}
		}

		$keys = [':count', ':title', ':complement', ':collapsed', ':expanded', ':comments', ':links', 
				 ':base_url', ':tab', ':id'];
		$replace = [self::$panel_count, $midia['midia_name'], $complement, $collapsed, $expanded,
					$midia['midia_comments'], $links, $this->url, 
					$tab, $midia['midia_id']];
		return str_replace($keys, $replace, $this->accordion);
	}
}