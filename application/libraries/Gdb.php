<?php

//date_default_timezone_set('Asia/Kolkata');

/**
 * interface to the CodeIgniter db library.
 *
 * @author Dinesh Ninave
 */
class gdb {

	/**
	 * CodeIginiter instance.
	 *
	 * @var object
	 */
	private $CI;

	/**
	 * Database instance.
	 *
	 * @var object
	 */
	private $db;

	/**
	 * Config setting for job stage open or close.
	 *
	 * @var type
	 */
	private $fetch_mode = 'web';

	/**
	 * Intialization
	 */
	public function __construct() {
		$this->CI = &get_instance();
		$this->db = &$this->CI->db;
	}

	function checkAdminLogin() {
		if ($this->CI->session->userdata('admin_id') == '') {
			redirect(base_url('login'));
		}
	}

	function setFetchMode($fetch_mode) {
		$this->fetch_mode = $fetch_mode;
	}

	public function getFetchMode() {
		if ($this->fetch_mode === "service") {
			return 'open';
		} else {
			//fetch from database.
			return isset($_SESSION['fetch_mode']) ? $_SESSION['fetch_mode'] : 'open';
		}
	}

	/**
	 * Returns row of given table.
	 * 'where' condition applies when passed.
	 *
	 * @param   string          $table_name
	 * @param   string|array    $where
	 * @return  array
	 */
	public function getRow($table_name, $where = NULL) {
		$this->db->select('*')->from($table_name);

		if (!is_array($where)) {
			$where = array();
		}

		$where['is_deleted'] = 0;
		$this->db->where($where);
		return $this->db->get()->row();
	}

	/**
	 * Returns multiple records of given table.
	 * 'where' condition applies when passed.
	 *
	 * @param   string  $table_name
	 * @return  array
	 */
	public function getAll($table_name, $where = NULL) {
		$this->db->select('*')
			->from($table_name);

		if (!is_array($where)) {
			$where = array();
		}

		$where['is_deleted'] = 1;
		$this->db->where($where);

		return $this->db->get()->result();
	}

	/**
	 * return with filtering is deleted='0' data
	 *
	 * @param   string      $table
	 * @return  array
	 */
	function get($table) {
		$this->db->where('is_deleted', 0);
		return $this->db->get($table);
	}

	/**
	 * Insert into given table with common field automatically appended.
	 *
	 * @param   string      $table
	 * @param   array       $data
	 * @return  int|boolean
	 */
	public function insert($table, $data) {
		$data['creation_datetime'] = DATETIME;
		$data['modification_datetime'] = DATETIME;
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	/**
	 * Update given table with common fields automatically appended.
	 *
	 * @param   string      $table
	 * @param   array       $data
	 * @return  int|boolean
	 */
	public function update($table, $data) {
		$data['modification_datetime'] = DATETIME;
		return $this->db->update($table, $data);
	}

	/**
	 * Mark row as deleted in given table by setting is_deleted='1'.
	 *
	 * @param   string      $table
	 * @return  int|boolean
	 */
	public function delete($table) {
		$data['is_deleted'] = 1;
		$data['deletion_datetime'] = DATETIME;
		return $this->db->update($table, $data);
	}

	/**
	 * Calls CodeIginter Database function if exists.
	 *
	 * @param   string  $name
	 * @param   array   $arguments
	 * @return  mixed
	 */
	public function __call($name, $arguments) {
		if (method_exists($this->db, $name)) {
			return call_user_func_array(array($this->db, $name), $arguments);
		}
	}

}
