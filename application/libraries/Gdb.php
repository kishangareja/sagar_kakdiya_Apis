<?php

date_default_timezone_set('Asia/Kolkata');

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
            redirect(base_url('admin/login'));
        }
    }
    
    function checkBeauticianLogin() {
        if ($this->CI->session->userdata('beautician_id') == '') {
            redirect(base_url('beautician'));
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
     * return time string
     *
     * @param   string      $datetime
     * @param   boolean     $full
     * @return  string
     */
    function timeElapsedString($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * Use for compress image
     *
     * @param   string      $source_url
     * @param   string      $destination_url
     * @param   string      $quality
     * @return  string
     */
    function compress_image($source_url, $destination_url, $quality) {
        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source_url);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source_url);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source_url);
        }

        imagejpeg($image, $destination_url, $quality);
        return $destination_url;
    }

    /**
     * Use for create thumbnail image
     *
     * @param   string      $source_image_path
     * @param   string      $thumbnail_image_path
     * @param   string      $width
     * @param   string      $height
     * @return  boolean
     */
    function generate_image_thumbnail($source_image_path, $thumbnail_image_path, $width, $height) {

        @define('THUMBNAIL_IMAGE_MAX_WIDTH', $width);
        @define('THUMBNAIL_IMAGE_MAX_HEIGHT', $height);
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
        }
        if ($source_gd_image === false) {
            return false;
        }
        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = THUMBNAIL_IMAGE_MAX_WIDTH / THUMBNAIL_IMAGE_MAX_HEIGHT;
        if ($source_image_width <= THUMBNAIL_IMAGE_MAX_WIDTH && $source_image_height <= THUMBNAIL_IMAGE_MAX_HEIGHT) {
            $thumbnail_image_width = $source_image_width;
            $thumbnail_image_height = $source_image_height;
        } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
            $thumbnail_image_width = (int) (THUMBNAIL_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
            $thumbnail_image_height = THUMBNAIL_IMAGE_MAX_HEIGHT;
        } else {
            $thumbnail_image_width = THUMBNAIL_IMAGE_MAX_WIDTH;
            $thumbnail_image_height = (int) (THUMBNAIL_IMAGE_MAX_WIDTH / $source_aspect_ratio);
        }

        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

        $img_disp = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        //$backcolor = imagecolorallocate($img_disp,255, 255, 255);
        //imagefill($img_disp,0,0,$backcolor);

        imagecopy($img_disp, $thumbnail_gd_image, (imagesx($img_disp) / 2) - (imagesx($thumbnail_gd_image) / 2), (imagesy($img_disp) / 2) - (imagesy($thumbnail_gd_image) / 2), 0, 0, imagesx($thumbnail_gd_image), imagesy($thumbnail_gd_image));

        imagejpeg($img_disp, $thumbnail_image_path, 90);
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        imagedestroy($img_disp);
        return true;
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
