<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->gdb->checkAdminLogin();
	}

	public function index() {
		$this->data['view'] = 'dashboard';
		$this->load->view('admin_master', $this->data);
	}

	public function district() {
		$this->data['page_title'] = 'District Management';
		$this->data['sent_data'] = $this->common->getAllDistrict();
		$this->data['view'] = 'district/view';
		$this->load->view('admin_master', $this->data);
	}

	public function category() {
		$this->data['page_title'] = 'Category Management';
		$this->data['sent_data'] = $this->common->getAllCategory();
		$this->data['view'] = 'category/view';
		$this->load->view('admin_master', $this->data);
	}

	public function staff_upload() {
		$this->data['page_title'] = 'Staff Uploads';
		$this->data['upload_data'] = $this->common->getStaffUpload();
		$this->data['view'] = 'staffUpload/index';
		$this->load->view('admin_master', $this->data);
	}

	public function useful_links() {
		$this->data['page_title'] = 'Usefull Links';
		$this->data['link_data'] = $this->common->getUsefullLinks();
		if ($this->input->post('description')) {
			$description = array(
				'description' => trim($this->input->post('description')),
			);
			$result = $this->common->updateUsefullLinks(1, $description);
			if ($result) {
				$this->session->set_flashdata('success', "Content Updated Successfully.");
			}
			redirect(base_url('home/useful_links'));
		}
		$this->data['view'] = 'useful_links';
		$this->load->view('admin_master', $this->data);
	}

}
