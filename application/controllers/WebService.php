<?php

/**
 * Description of WebService
 *
 * @author Dinesh Ninave
 */
class WebService extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('category_model');
	}

	/**
	 * Web service for mobile application.
	 *
	 */
	public function service() {
		$this->load->library('web_service');
		$this->web_service->call();
	}

	/**
     * Save Category
     */
    public function add_category($id=0) {
		$category = array(
			'name' => $this->input->post('name'),
		);
		if (isset($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
			$temp_file = $_FILES['image']['tmp_name'];
			$img_name = "category_" . mt_rand(10000, 999999999) . time();
			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$category['image'] = $img_name . "." . $ext;
			$url = UPLOAD . $category['image'];
			move_uploaded_file($temp_file, $url);
		}
		$this->r_data['message'] = "Category not stored.";	
		if ($id) {
			$result = $this->category_model->updateCategory($id,$category);
		}else{
			$result = $this->category_model->addCategory($category);
			$insert_id = $this->db->insert_id();
		}
		$cat_id = $id ? $id : $insert_id;
		$cat_data = $this->category_model->getCategoryById($cat_id);
		if ($result) {
			$this->r_data['message'] = "Category stored successfully.";
			$this->r_data['success'] = 1;
			$this->r_data['data'] = $cat_data;
			$this->r_data['data']->image_url = base_url().UPLOAD.$cat_data->image;
		}
		echo json_encode($this->r_data);
	}
 }