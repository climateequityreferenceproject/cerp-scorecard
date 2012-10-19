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
    private $_url = "http://gdrights.org/calculator_dev/api/";
    private static $_instance;
    
    // Decided not to use getters and setters
    public $pathwayLabel = array(
        'low' => 'LOW AMBITION', // G-8
        'med' => '2.0&#8451;',
        'high' => '1.5&#8451;'
    );
    public $pathwayIds = array();
    public $pathway_default = 'high';
    
    // TODO: Get this by querying the API
    public static $maxYear = 2030;
    
    private $_pathway_array = array('low'=>'G-8', 'med'=>'2.0Cmarkerpathway', 'high'=>'1.5Cmarkerpathway');
    // TODO allow authors to identify in pathway db which are used here
    
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
        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        if (!PEAR::isError($req->sendRequest())) {
            if ($req->getResponseCode() == 200) {
                return (array) json_decode($req->getResponseBody());
            } else {
                return null;
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
     * Construct a key into the array of databases, labeled by pathway and kab/no-kab
     * 
     * @param string $pwId  A valid pathway ID
     * @param string $kab   One of 'none', 'all', or 'ratified' (defaults to 'none')
     * @return string       The key
     */
    public static function get_db_key($pwId, $kab = 'none') {
        return '_' . $pwId . ':' . $kab;
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
    private function _getDB($pwId, $kab)
    {
        if (!in_array($pwId, $this->pathwayIds)) {
            throw new GDRsAPIException('Pathway id "' . $pwId . '" is not defined');
        }
        // Do we have it already?
        $key = self::get_db_key($pwId, $kab);
        if (!(isset($this->_db[$key]))) {
            // Have we stored it in a session variable?
            if (isset($_SESSION['gdrs_db'])) {
                $this->_db = $_SESSION['gdrs_db'];
            }
            // Does a database exist for this pathway?
            if (!(isset($this->_db[$key])) || !$this->get('calc_ver', $pwId)) {
                $response = $this->get('new_db');
                $this->_db[$key] = $response['db'];
            }
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
    public function post($post_array, $pwId, $kab='none')
    {
        switch ($kab) {
            case 'none':
                $post_array['use_kab'] = 0;
                $post_array['kab_only_ratified'] = 0;
                break;
            case 'all':
                $post_array['use_kab'] = 1;
                $post_array['kab_only_ratified'] = 0;
                break;
            case 'ratified':
                $post_array['use_kab'] = 1;
                $post_array['kab_only_ratified'] = 1;
                break;
            default:
                throw new GDRsAPIException('Value for kab parameter "' . $kab .'" not valid: Use "none", "all", or "ratified"');
        }
        $req = new HTTP_Request($this->_url);
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        // If pwId not defined, next line will throw an exception
        $db = $this->_getDB($pwId, $kab);
        
        $req->addPostData("emergency_path", $pwId);
        $req->addPostData("db", $db);
        foreach ($post_array as $key => $val) {
            $req->addPostData($key, $val);
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
