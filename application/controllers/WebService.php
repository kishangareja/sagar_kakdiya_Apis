<?php

/**
 * Description of WebService
 *
 * @author Dinesh Ninave
 */
class WebService extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Web service for mobile application.
	 *
	 */
	public function service() {
		$this->load->library('web_service');
		$this->web_service->call();
	}
	
 }