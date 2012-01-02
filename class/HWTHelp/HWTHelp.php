<?php
/**
 * HWTHelp.php
 * 
 * PHP Version 5
 *
 * @package HWTHelp
 * @copyright 2012 Tyler Kemp-Benedict
 * @license All rights reserved
 * @link http://www.hardworkingtype.com/
 */

/**
 * Label exceptions from the HWTHelp class
 * 
 */
class HWTHelpException extends Exception
{
}

/**
 * Parse a HWT Help-formatted XML file
 * 
 * Example usage:
 * $parser = new HWTHelpParser();
 * $myArray = $parser->parse($xmlfile);
 *
 */
class HWTHelpParser
{
    private $_parser;
    private $_state; // State of the finite state machine
    private $_currID; // Current ID value (if any)
    private $_lineNo; // Current line number
    private $_array = array(); // Parsed data structure
    
    /**
     * Constructor for the help parser
     */
    public function __construct()
    {
        $this->_parser = xml_parser_create();
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, "_openTag", "_closeTag");
        xml_set_character_data_handler($this->_parser, "_handleData");
        
        $this->_state = 'start';
    }
    
    /**
     * Destructor: Free up the XML parser data structure
     */
    public function __destruct()
    {
        xml_parser_free($this->_parser);
    }
    
    /**
     * Parse the XML file and return the data structure
     * 
     * Line lengths are limited to 4096 characters
     * 
     * @param URL $url A string holding the URL or path to the XML file
     * 
     * @return array The parsed array from the XML file
     * @todo Allow for a proper URL using fsockopen(). Right now limited to a file.
     */
    public function parse($url)
    {
        $this->_array = array();
        $this->_lineNo = 1;
        
        $fp = fopen($url, 'r');
        if (!$fp) {
            throw new HWTHelpException('Cannot read file "' . $url .'"');
        }
        while ($data = fgets($fp, 4096)) {
            $parse_ok = xml_parse($this->_parser, $data);
            if (!$parse_ok) {
                throw new HWTHelpException($this->_lineNo . ': ' . xml_error_string(xml_get_error_code($this->_parser)));
            }
            $this->_lineNo++;
        }
        
        return $this->_array;
    }
    
    /**
     * Take action on any open tags
     * 
     * @param xml_parser $parser        An xml parser (not a proper object--a pointer)
     * @param string     $element_name  Name of the found element
     * @param array      $element_attrs Collection of element attributes
     * 
     * @return nothing
     */
    private function _openTag($parser, $element_name, $element_attrs)
    {
        switch ($this->_state) {
            case 'start':
                switch ($element_name) {
                    case 'HWT_HELP':
                        $this->_state = 'receptive';
                        break;
                    default:
                        throw new HWTHelpException($this->_lineNo . ': Expected a "hwt_help" tag, but got "' . $element_name . '"');
                }
                break;
            case 'receptive':
                switch ($element_name) {
                    case 'ENTRY':
                        $this->_state = 'new_entry';
                        if (!isset($element_attrs['ID'])) {
                            throw new HWTHelpException($this->_lineNo . ': No id for entry');
                        }
                        $this->_currID = $element_attrs['ID'];
                        break;
                    default:
                        throw new HWTHelpException($this->_lineNo . ': Expected an "entry" tag, but got "' . $element_name . '"');
                }
                break;
             case 'new_entry':
                switch ($element_name) {
                    case 'LABEL':
                        $this->_state = 'expect_label_data';
                        $this->_array[$this->_currID]['label'] = '';
                        break;
                    default:
                        throw new HWTHelpException($this->_lineNo . ': Expected a "label" tag, but got "' . $element_name . '"');
                }
                break;
             case 'expect_label_data':
                 throw new HWTHelpException($this->_lineNo . ': Expected character data for label, but got "' . $element_name . '"');
                 break;
             case 'expect_text':
                 $this->_array[$this->_currID]['text'] = '';
                 switch ($element_name) {
                    case 'TEXT':
                        $this->_state = 'expect_text_data';
                        break;
                    case 'ENTRY':
                        // No text: not a problem, can be blank
                        $this->_state = 'new_entry';
                        if (!isset($element_attrs['ID'])) {
                            throw new HWTHelpException($this->_lineNo . ': No id for entry');
                        }
                        $this->_currID = $element_attrs['ID'];
                        break;
                    default:
                        throw new HWTHelpException($this->_lineNo . ': Expected a "text" or "entry" tag, but got "' . $element_name . '"');
                }
                break;
       }
    }
    
    /**
     * Use this to stop reading character data for labels and text
     * 
     * @param xml_parser $parser       An xml parser (not a proper object--a pointer)
     * @param string     $element_name Name of the found close element
     * 
     * @return nothing
     */
    private function _closeTag($parser, $element_name)
    {
        switch ($this->_state) {
            case 'expect_label_data':
                switch ($element_name) {
                    case 'LABEL':
                        $this->_state = 'expect_text';
                        break;
                    default:
                        throw new HWTHelpException($this->_lineNo . ': Expected a closing "label" tag, but got "' . $element_name . '"');
                }
                break;
            case 'expect_text_data':
                switch ($element_name) {
                    case 'TEXT':
                        $this->_state = 'receptive';
                        break;
                    default:
                        throw new HWTHelpException($this->_lineNo . ': Expected a closing "text" tag, but got "' . $element_name . '"');
                }
                break;
        }

    }
    
    /**
     * Take care of data within elements
     * 
     * @param xml_parser $parser An xml parser (not a proper object--a pointer)
     * @param string     $data   Character data
     * 
     * @return nothing
     */
    private function _handleData($parser, $data)
    {
        switch ($this->_state) {
            case 'expect_label_data':
                $this->_array[$this->_currID]['label'] .= $data;
                break;
            case 'expect_text_data':
                $this->_array[$this->_currID]['text'] .= html_entity_decode($data);
                break;
        }
    }
}


/**
 * Generic help interface
 * 
 * Provides a generic help interface
 * Example usage:
 * $glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');
 * $glossary->getLink('gloss_path'); // For making links in the page
 * $glossary->getJSON('gloss_path'); // For AJAX calls
 * foreach ($glossary as $id => $entry) {} // Iterator over help entries
 *
 * @todo Store help systems in an associative array indexed by path to xml file to avoid reloading or use $_PHPSESSION
 */
class HWTHelp implements Iterator
{
    private $_entries = array();
    private $_markup_flag;
    private $_url;
    private $_help_page;
    private $_index;
    private $_ids;
    
    /**
     * Constructor for the HWTHelp system
     * 
     * @return nothing
     * @todo Fill in $_entries from the file at $url
     */
    public function __construct($url, $markup_flag, $help_page)
    {
        $this->_markup_flag = $markup_flag;
        $this->_url = $url;
        $this->_help_page = $help_page;
        
        $parser = new HWTHelpParser();
        $this->_entries = $parser->parse($url);
        $this->_ids = array_keys($this->_entries);
        
        // Initalize the iterator
        $this->_index = 0;
    }
    
    /**
     * For debugging purposes: best to use iterator
     * 
     * @return array Array of entries
     */
    public function getEntries()
    {
        return $this->_entries;
    }
    
    /**
     * Sends you to the first help entry
     * 
     * @return nothing
     */
    public function rewind()
    {
        $this->_index = 0;
    }
    
    /**
     * Return the current entry
     * 
     * @return array Has the structure array('label' => '', 'text' => '')
     */
    public function current()
    {
        return $this->_entries[$this->_ids[$this->_index]];
    }
    
    /**
     * Return the id for the current entry
     * 
     * @return string Short identifier for the entry
     */
    public function key()
    {
        return $this->_ids[$this->_index];
    }
    
    /**
     * Increments the internal entry pointer
     * 
     */
    public function next()
    {
        $this->_index++;
    }
    
    /**
     * Say whether entry at current internal pointer exists
     * 
     * @return boolean Say whether entry is valid or not
     */
    public function valid()
    {
        return isset($this->_ids[$this->_index]);
    }
    
    /**
     * Generate JSON-encoded entry array for the specified ID
     * 
     * @param string $id Short name for the entry
     * @return JSON JSON-encoded entry array
     */
    public function getJSON($id)
    {
        if (isset($this->_entries[$id])) {
            return json_encode($this->_entries[$id]);
        } else {
            throw new HWTHelpException('No entry for identifier "' . $id . '" found');
        }
    }
    
    /**
     * Generate HTML for link into help
     * 
     * @param string  $id       Short name for the entry
     * @param boolean $to_lower Flag whether to convert to lower case
     * @param boolean $use_span Flag whether to wrap label in a span
     * 
     * @return HTML Link markup for item you're getting help entry for
     */
    public function getLink($id, $to_lower = false)
    {
        if (!isset($this->_entries[$id])) {
            throw new HWTHelpException('No entry for identifier "' . $id . '" found');
        }
        $html = '<a class="' . $this->_markup_flag . '"';
        $html .= ' href="' . $this->_help_page . '#' . $id . '"';
        $html .= ' target="_blank">';
        
        $label = $this->_entries[$id]['label'];
        if ($to_lower) {
            $label = strtolower($label);
        }
        
        $html .= $label;
        $html .= '</a>';
        
        return $html;
    }
    
}

?>
