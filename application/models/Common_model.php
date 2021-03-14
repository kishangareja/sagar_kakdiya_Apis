<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model {

	private $category = 'category';
	private $user_session = 'user_session';

	public function getUserSessionTable() {
		return $this->user_session;
	}

	public function getCategoryTable() {
		return $this->category;
	}

	/**
	 * Get secure key
	 */
	public function getSecureKey() {
		$string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$stamp = time();
		$secure_key = $pre = $post = '';
		for ($p = 0; $p <= 10; $p++) {
			$pre .= substr($string, rand(0, strlen($string) - 1), 1);
		}

		for ($i = 0; $i < strlen($stamp); $i++) {
			$key = substr($string, substr($stamp, $i, 1), 1);
			$secure_key .= (rand(0, 1) == 0 ? $key : (rand(0, 1) == 1 ? strtoupper($key) : rand(0, 9)));
		}

		for ($p = 0; $p <= 10; $p++) {
			$post .= substr($string, rand(0, strlen($string) - 1), 1);
		}
		return $pre . '-' . $secure_key . $post;
	}

	public function generatePassword() {
		$post = '';
		$string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for ($p = 0; $p <= 7; $p++) {
			$post .= substr($string, rand(0, strlen($string) - 1), 1);
		}
		return $post;
	}
	
	/**
	 * Add user session data
	 */
	public function insertUserSession($data) {
		$this->db->insert($this->user_session, $data);
		return $this->db->insert_id();
	}

	public function getUserSession($user_id, $token) {
		$this->db->where('is_active', 1);
		$this->db->where('user_id', $user_id);
		$this->db->where('token', $token);
		return $this->db->get($this->user_session)->row();
	}

	public function getSessionInfo($secret_log_id) {
		$this->db->where('is_active', 1);
		$this->db->where('session_id', $secret_log_id);
		return $this->db->get($this->user_session)->row();
	}

	public function logoutUser($secret_log_id) {
		$data = array('is_active' => 0, 'end_date' => DATETIME);
		$this->db->where('session_id', $secret_log_id);
		$this->db->update($this->user_session, $data);
	}

	public function getUserById($user_id) {
		$this->db->where('user_id', $user_id);
		return $this->gdb->get($this->users)->row();
	}

}