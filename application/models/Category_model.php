<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

	/**
	 * Start Category
	 */
	public function addCategory($data) {
		return $this->gdb->insert($this->common->getCategoryTable(), $data);
	}

	public function getCategory() {
		return $this->gdb->get($this->common->getCategoryTable())->result();
	}

	public function getCategoryById($id) {
		$this->gdb->where('id', $id);
		return $this->gdb->get($this->common->getCategoryTable())->row();
	}
	public function updateCategory($id, $data) {
		$this->gdb->where('id', $id);
		return $this->gdb->update($this->common->getCategoryTable(), $data);
	}

	public function deleteCategory($id) {
		$this->gdb->where('id', $id);
		return $this->gdb->delete($this->common->getCategoryTable());
	}

}