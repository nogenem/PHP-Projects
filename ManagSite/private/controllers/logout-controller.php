<?php
/**
 * LogoutController - Desloga o usuario.
 */
class LogoutController extends MainController
{

    public function index() {
		
		if ( $this->logged_in ) {
			$this->logout();
			$this->logged_in = false;
		}

		// Redireciona para a home
		$this->goto_page(HOME_URI);
		
    } // index
	
} // class LogoutController