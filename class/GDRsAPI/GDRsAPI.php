<?php
require_once "HTTP/Request.php";

class GDRsAPIException extends Exception {}


/**
 * Description of GDRsAPI
 *
 * @author erickb
 */

/*
 * Usage:
 * $api = GDRsAPI::connection();
 * $api->pathwayLabel;
 * $api->pathway_default;
 * $api->pathwayIds;
 * $api->post($post_array, $pwId);
 * $api->get($to_get, $pwId = null);
 * 
 * 
 */


class GDRsAPI {
    private $db = array();
    private $url = "http://gdrights.org/calculator_dev/api/";
    private static $_instance;
    
    // Decided not to use getters and setters
    public $pathwayLabel = array(
        'low' => 'G8',
        'med' => 'BASIC Experts',
        'high' => 'AOSIS'
    );
    public $pathwayIds = array();
    public $pathway_default = 'high';
    
    private $pathway_array = array('low'=>'G8', 'med'=>'basic_experts', 'high'=>'AOSIS');
    
    private function build_get($to_get, $pwId=null) {
        $retval = $this->url . '?';
        $retval .= 'q=' . $to_get;
        if ($pwId) {
            $retval .= 'db=' . $this->db[$pwId];
        }
        return $retval;
    }
    
    public function get($to_get, $pwId=null) {
        $req =& new HTTP_Request($this->build_get($to_get, $pwId));
        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        if (!PEAR::isError($req->sendRequest())) {
             $response = json_decode($req->getResponseBody());
        } else {
            throw new GDRsAPIException($req->getMessage());
        }
        return (array) $response;
    }
    
    // Singleton: hide the constructor
    private function __construct() {
        $response = $this->get('pathways');
        foreach ($this->pathway_array as $key => $val) {
            foreach ($response as $pathway) {
                // The json_decode function returns these arrays as type StdClass
                $pw_info = (array) $pathway;
                if ($pw_info['short_code'] === $val) {
                    $this->pathwayIds[$key] = $pw_info['id'];
                    break;
                }
            }
        }
    }
    
    // Public interface to constructor
    public static function connection() {
        if (!(self::$_instance instanceof GDRsAPI)) {
            self::$_instance = new GDRsAPI();
        }
        return self::$_instance;
    }
    
    // Use a lazy creation approach: when pwId is first used, create the db
    private function get_db($pwId) {
        if (!in_array($pwId, $this->pathwayIds)) {
            throw new GDRsAPIException('Pathway id "' . $pwId . '" is not defined');
        }
        // Do we have it already?
        if (!(isset($this->db[$pwId]))) {
            // Have we stored it in a session variable?
            if (isset($_SESSION['gdrs_db'])) {
                $this->db = $_SESSION['gdrs_db'];
            }
            // And check again, whether in the session variable or not
            if (!(isset($this->db[$pwId]))) {
                $response = $this->get('new_db');
                $this->db[$pwId] = $response['db'];
            }
        }
        $_SESSION['gdrs_db'] = $this->db;
        return $this->db[$pwId];
    }
    
    public function post($post_array, $pwId) {
        $req =& new HTTP_Request($this->url);
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        // If pwId not defined, next line will throw an exception
        $db = $this->get_db($pwId);
        
        $req->addPostData("emergency_path", $pwId);
        $req->addPostData("db", $db);
        foreach ($post_array as $key => $val) {
            $req->addPostData($key, $val);
        }
        
        if (!PEAR::isError($req->sendRequest())) {
             $response = json_decode($req->getResponseBody());
             // Oddly, the decode procedure seems to duplicate the first element, so get the tail:
             $response = array_slice($response, 1);
        } else {
            throw new GDRsAPIException($req->getMessage());
        }
        
        return $response;
    }
    
}

?>
