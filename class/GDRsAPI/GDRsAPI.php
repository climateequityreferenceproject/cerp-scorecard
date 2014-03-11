<?php
/**
 * GDRsAPI.php
 * 
 * PHP Version 5
 *
 * @package GDRsScorecard
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */

require_once "HTTP/Request.php";

/**
 * Label exceptions from the GDRsAPI class
 * 
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */
class GDRsAPIException extends Exception
{
}


/**
 * Access the GDRs API
 * 
 * Provides a PHP interface to the GDRs web API.
 * Usage:
 * $api = GDRsAPI::connection();
 * $api->pathwayLabel;
 * $api->pathway_default;
 * $api->pathwayIds;
 * $api->post($post_array, $pwId);
 * $api->get($to_get, $pwId = null);
 * 
 * @author Eric Kemp-Benedict <eric.kemp-benedict@sei-international.org>
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */
class GDRsAPI
{
    private $_db = array();
    private $_url = '';
    private static $_instance;
    
    // Decided not to use getters and setters
    public $pathwayLabel = array(
        'low' => 'G8 pathway (very weak)',
        'med' => 'Weak 2&#8451; pathway',
        'high' => 'Strong 2&#8451; pathway'
    );
    public $pathwayIds = array();
    public $pathway_default = 'high';
    
    // KABs
    public static $use_kab_on = false;
    
    // TODO: Get this by querying the API
    public static $maxYear = 2030;
    
    // Store additional parameters for POST
    private $_user_params = array();
    private $_user_pathway = -1;
    
    private $_pathway_array = array('low'=>'G8Pathway', 'med'=>'2.0Cmarkerpathway', 'high'=>'1.5Cmarkerpathway');
    // TODO allow authors to identify in pathway db which are used here
    
    /**
     * Say whether the scoreard is being called from a directory with "dev" in the name
     * @return boolean 
     */
    private static function isDev() {
        // Note: might return "0" (a correct, non-false value)--have to check for false
        if (strpos($_SERVER['PHP_SELF'], 'dev')===false) {
            return false;
        } else {
            return true;
        }
    }

    
    /**
     * Build a URL for a "GET" call to the API
     * 
     * A helper function for get.
     * 
     * @param string $to_get Value to get
     * @param string $pwId   Pathway ID (if any: defaults to null)
     * 
     * @return string The constructed URL
     */
    private function _buildGet($to_get, $pwId=null)
    {
        $retval = $this->_url . '?';
        $retval .= 'q=' . $to_get;
        if ($pwId) {
            $retval .= '&db=' . $this->_getDB($pwId, 'none');
        }
        return $retval;
    }
    
    /**
     * Set additional parameters to be sent in post calls
     * 
     * Will check whether the parameters are in the list of acceptable parameters,
     * and discard any that are not.
     * 
     * @param array $new_params An array of param => value pairs
     * 
     * @return array Array of accepted parameters
     * 
     */
    public function set_params($new_params) {
        // Remove "emergency_path" from array, but save it first
        if (isset($new_params['emergency_path'])) {
            $this->_user_pathway = $new_params['emergency_path'];
            unset($new_params['emergency_path']);
        }
        $ok_params = array_keys($this->get('params'));
        $return_params = array();
        foreach (array_keys($new_params) as $param) {
            if (in_array($param, $ok_params) && is_numeric($new_params[$param])) {
                $return_params[$param] = $new_params[$param];
            }
        }
        $this->_user_params = $return_params;
        $_SESSION['user_params'] = $this->_user_params;
        $_SESSION['emergency_path'] = $this->_user_pathway;
        return $return_params;
    }
    
    /**
     * Set additional parameters to an empty array
     */
    public function clear_params() {
        $this->_user_params = array();
    }
    
    /**
     * Get user parameter values
     * 
     * @return array Array of parameter values
     */
    public function get_params() {
        if (count($this->_user_params) === 0) {
            // Have we stored it in a session variable?
            if (isset($_SESSION['user_params'])) {
                $this->_user_params = $_SESSION['user_params'];
            }
        }
        return $this->_user_params;
    }
    
     /**
     * Get user pathway
     * 
     * @return int Id of the pathway
     */
    public function get_user_pwId() {
        return $this->_user_pathway;
    }
    
    /**
     * Return the result of a call to GET
     * 
     * @param string  $to_get Value to get
     * @param integer $pwId   Pathway ID as recognized by the calculator (if any: defaults to null)
     * 
     * @return array Result of the call to GET as a decoded JSON-encoded array
     */
    public function get($to_get, $pwId=null)
    {
        $req = new HTTP_Request($this->_buildGet($to_get, $pwId));
        if ($this->isDev()) {
            $req->setBasicAuth("***REMOVED***", "***REMOVED***");
        }
        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        if (!PEAR::isError($req->sendRequest())) {
            if ($req->getResponseCode() == 200) {
                return (array) json_decode($req->getResponseBody());
            } else {
                throw new GDRsAPIException('Error code ' . $req->getResponseCode());
            }
        } else {
            throw new GDRsAPIException($req->getMessage());
        }
    }
    
    /**
     * Check whether the database still exists
     * 
     * @param type string $db Our database identifier
     * @return boolean
     * @throws GDRsAPIException 
     */
    public function db_exists($db) {
        $get_string = $this->_url . '?' . 'q=calc_ver&db=' . $db;
        $req = new HTTP_Request($get_string);
        if ($this->isDev()) {
            $req->setBasicAuth("***REMOVED***", "***REMOVED***");
        }
        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        if (!PEAR::isError($req->sendRequest())) {
            if ($req->getResponseCode() != 410) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new GDRsAPIException($req->getMessage());
        }
    }
    
    // 
    /**
     * Generate the array mapping pathway ids to pathway names
     * 
     * Singleton: hide the constructor
     */
    private function __construct()
    {
        if (strpos($_SERVER['PHP_SELF'], 'dev')!==false) {
            $this->_url = "http://gdrights.org/calculator_dev/api/";
        } else {
            $this->_url = "http://gdrights.org/calculator/api/";
        }
        $response = $this->get('pathways');
        foreach ($this->_pathway_array as $key => $val) {
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
        
    /**
     * Public interface to constructor: either return current instance or create one
     * 
     * Follows singleton pattern
     * 
     * @return GDRsAPI Single instance of the class
     */
    public static function connection()
    {
        if (!(self::$_instance instanceof GDRsAPI)) {
            self::$_instance = new GDRsAPI();
        }
        return self::$_instance;
    }
    
    /**
     * Construct a key into the array of databases, labeled by pathway
     * 
     * @param string $pwId  A valid pathway ID
     * @return string       The key
     */
    public static function get_db_key($pwId) {
        $kab_key = self::$use_kab_on ? 'kab' : 'nokab';
        return '_' . $pwId . '_' . $kab_key;
    }
    
    /**
     * Return existing db connection corresponding to a pathway ID, or create one
     * 
     * Use a lazy creation approach: when pwId is first used, create the db
     * 
     * @param integer $pwId The pathway ID
     * 
     * @return string API's label for the user database
     */
    private function _getDB($pwId)
    {
        if (!in_array($pwId, $this->pathwayIds)) {
            throw new GDRsAPIException('Pathway id "' . $pwId . '" is not defined');
        }
        // Do we have it already?
        $key = self::get_db_key($pwId);
        if (!isset($this->_db[$key])) {
            // Have we stored it in a session variable?
            if (isset($_SESSION['gdrs_db'])) {
                $this->_db = $_SESSION['gdrs_db'];
            }
        }
        
        // Does a database exist for this pathway?
        if (!isset($this->_db[$key]) || !$this->db_exists($this->_db[$key])) {
            // Make a copy of an existing db if possible, in case it was modified
            $existing_db = null;
            foreach ($this->_db as $db) {
                if ($db) {
                    $existing_db = $db;
                    break;
                }
            }
            $get_string = 'new_db';
            if ($existing_db) {
                $get_string .= '&old_db=' . $existing_db;
            }
            $response = $this->get($get_string);
            $this->_db[$key] = $response['db'];
        }
        $_SESSION['gdrs_db'] = $this->_db;
        return $this->_db[$key];
    }
    
    /**
     * Carry out a POST command to the API, using an array of post parameters and pathway id
     * 
     * @param array   $post_array Array of post parameter/value pairs
     * @param integer $pwId       Integer ID for the pathway as recognized by the calculator
     * 
     * @return array Result of the POST command to the API as a decoded JSON-encoded array 
     */
    public function post($post_array, $pwId)
    {
        // Add in any generally defined user parameters: order matters, and post_array supercedes _user_paramers
        $post_array = array_merge($this->get_params(), $post_array);
        $req = new HTTP_Request($this->_url);
        if ($this->isDev()) {
            $req->setBasicAuth("***REMOVED***", "***REMOVED***");
        }
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        // If pwId not defined, next line will throw an exception
        $db = $this->_getDB($pwId);

        $req->addPostData("emergency_path", $pwId);
        $req->addPostData("db", $db);
        foreach ($post_array as $key => $val) {
            $req->addPostData($key, $val);
        }
                    
        // Check whether we're using the same KAB setting as the calculator default
        $response = $this->get('params');
        $use_kabs_array = (array) $response['use_kab'];
        $kabs_match = true;
        if (($use_kabs_array['value'] === 1) && !self::$use_kab_on) {
            $use_kab = 0;
            $kabs_match = false;
        } elseif (($use_kabs_array['value'] === 0) && self::$use_kab_on) {
            $use_kab = 1;
            $kabs_match = false;
        }
        if (!$kabs_match) {
            $req->addPostData("use_kab", $use_kab);
        }
        // json_decode sometimes duplicating first element -- find out how many we expect
        $numitems = count(explode(",", $post_array['years'])) * count(explode(",", $post_array['countries']));
        
        if (!PEAR::isError($req->sendRequest())) {
             $response = (array) json_decode($req->getResponseBody());
             // Oddly, the decode procedure sometimes seems to duplicate the first element.
             if (count($response) > $numitems) {
                $response = array_slice($response, 1);
             }
        } else {
            throw new GDRsAPIException($req->getMessage());
        }
        
        return $response;
    }
    
    /**
     * Return the value for code for the pseudo-region "world"
     * 
     * @return string The code
     * 
     * @todo Query the API to get the actual value
     */
    public function get_world_code() {
        return 'world';
    }
    
}

?>
