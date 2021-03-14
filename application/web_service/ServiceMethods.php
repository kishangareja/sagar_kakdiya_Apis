<?php

date_default_timezone_set('Asia/Kolkata');

/**
 * Description of ServiceMethods
 *
 */
class ServiceMethods
{

    /**
     * Services on which authentication not required.
     *
     * @var array
     */
    private $open_service = array('get_category');

    function __construct()
    {
        $this->r_data['message'] = '';
        $this->r_data['success'] = 0;
    }

    function paramValidation($paramarray, $data)
    {
        $NovalueParam = array();
        foreach ($paramarray as $val) {
            if ($data[$val] == '') {
                $NovalueParam[] = $val;
            }
        }
        if (is_array($NovalueParam) && count($NovalueParam) > 0) {
            $this->r_data['message'] = 'Sorry, that is not valid input. You missed ' . implode(', ', $NovalueParam) . ' parameters';
        } else {
            $this->r_data['success'] = 1;
        }
        return $this->r_data;
    }    
 

    /**
     * Get Category
     */
    public function get_category()
    {
        $this->load->model('category_model');
        $this->r_data['message'] = "Category Not found.";
        $category = $this->category_model->getCategory();        
        $data = array();
        foreach ($category as $key => $value) {
            $data[$key] = $value;
            if ($value->image) {
                $data[$key]->image = base_url() . UPLOAD . $value->image;
            }
        }
        if ($category) {
            $this->r_data['message'] = "Category fetched successfully.";
            $this->r_data['success'] = 1;
            $this->r_data['data'] = $data;
        }
        return $this->r_data;
    }

}