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
    private $open_service = array('get_category','signup','login');

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
     * signup
     */
    public function signup($data)
    {
        /* echo "<pre>";
        print_r($data->name);die; */
        $this->r_data['message'] = 'Register Failure.';
        $request = array(
            "name"=> $data->name,
	    	"mobile"=> $data->mobile,
	    	"email"=> $data->email,
	    	"password"=> md5($data->password),
	    	"device_id"=> $data->device_id,
        );
        $login = $this->common->checkUserExists($data->mobile,$data->email);
        if ($login) {
            $this->r_data['success'] = false;
            $this->r_data['message'] = "You already registered..!";
        }
        // $this->r_data['success'] = 0;
        // $this->r_data['message'] = "Login detail does not matched, Please Try Again.!!";
        if (!$login) {
            $login = $this->common->signup($request);
            // $otp = rand(1111, 9999);
            // $data = array('mobile' => $login->mobile, 'otp' => $otp, 'password' => md5($password));
            // $this->staff_model->updateStaff($login->id, $data);
            // $this->common->otpVerification($login->mobile, $otp);
            $this->r_data['success'] = true;
            // $this->r_data['message'] = 'OTP sent successfully.';
            $this->r_data['message'] = 'Register successfully.';
            $this->r_data['user_id'] = $login;
            // $this->r_data['otp'] = $otp;
            $this->r_data['data'] = $request;
        }
        return $this->r_data;
    }

    /**
     * login
     */
    public function login($mobile, $password)
    {
        $this->r_data['message'] = 'Login Failure.';
        $login = $this->common->login($mobile, md5($password));
        
        $this->r_data['success'] = false;
        $this->r_data['message'] = "Login detail does not matched, Please Try Again.!!";
        if ($login) {
            
                //Add user session
                $this->r_data['token'] = $this->common->getSecureKey();
                $user_session = array(
                'user_id' => $login->id,
                'token' => $this->r_data['token'],
                'start_date' => DATETIME,
                );
                $this->r_data['secret_log_id'] = $this->common->insertUserSession($user_session);
                $this->r_data['success'] = true;
                $this->r_data['message'] = 'Login successfully.';
                $this->r_data['data'] = $login;
        } else {
             $this->r_data['success'] = false;
            $this->r_data['message'] = "Login detail does not matched, Please Try Again.!!";
        }
            return $this->r_data;
    }

    public function verify_otp($type = 'verify', $otp, $user_id)
    {
        $this->load->model('staff_model');
        $this->r_data['success'] = 0;

        if ($type == 'resend') {
            $mobile = $otp;
            $otp1 = rand(1111, 9999);
            $this->common->otpVerification($mobile, $otp1);
            $data = array('mobile' => $otp, 'otp' => $otp1);
            $this->staff_model->updateStaff($user_id, $data);
            $this->r_data['message'] = "OTP resent successfully.";
            $this->r_data['success'] = 1;
            $this->r_data['data'] = new stdClass();
            $this->r_data['data']->otp = $otp1;
            $this->r_data['data']->user_id = $user_id;
            return $this->r_data;
        }

        if ($type == 'verify') {
            $otp_verify = $otp;
            $this->r_data['message'] = "OTP is not valid.";
            $result = $this->staff_model->checkOtp($otp_verify, $user_id);
            if ($result) {
                $data = array('is_deleted' => 0, 'status' => 1);
                $this->staff_model->updateStaff($user_id, $data);
                $this->r_data['message'] = "OTP verify successfully.";
                $this->r_data['user_id'] = $user_id;
                $this->r_data['success'] = 1;
                // $this->r_data['data'] = $this->staff_model->getStaffById($user_id);
                return $this->r_data;
            } else {
                $this->r_data['message'] = "OTP is wrong, Please try again. ";
                $this->r_data['success'] = 0;
                return $this->r_data;
            }
        }
    }   

    public function otpVerification($staff_numbers, $otp) {
		// Account details
		$apiKey = urlencode('JEBuRVEAr38-eZYvhEtKFptMwQUhb0QpJ01kVhLvEL');
// Message details
		$numbers = $staff_numbers;
		$sender = urlencode('NLDoSE');
		$message = rawurlencode("Hello 

Your one time password is : ".$otp);
		// $numbers = implode(',', $numbers);
		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, 'sender' => $sender, 'message' => $message);
// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
// Process your response here
		// echo $response;

		return true;

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

    /**
     * Logout
     */
    public function logout($secret_log_id)
    {
        $session = $this->common->getSessionInfo($secret_log_id);
        if ($session) {
            if ($session->user_id != $this->user_id) {
                $this->r_data['message'] = 'Secret log does not belongs to you.';
                return $this->r_data;
            }
            $this->common->logoutUser($secret_log_id);
            $this->r_data['success'] = 1;
            $this->r_data['message'] = 'Logout Successful.';
        }
        return $this->r_data;
    }

}