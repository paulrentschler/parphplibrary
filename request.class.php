<?php

  /**
   * Singleton object to provide access to the POST and 
   *   GET variables in a safe manner.
   * 
   * There should be NO references in the code to $_POST, $_GET,
   *   or $_REQUEST. If there are, they should be considered
   *   bugs and replaced with Request::* calls.
   * 
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 5 April 2011
   */
  class Request {
    
    /**
     * Store the values as they were originally submitted in the
     *   $_GET and/or $_POST variables.
     */
    protected static $rawValues = array();
    
    /**
     * Store HTML safe versions of the submitted values.
     */
    protected static $safeValues = array();
    
    
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __construct() {} 
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __clone() {} 
    
    
    
    /**
     * Get the values from the $_GET and $_POST super globals
     *   and store both the raw and safe versions.
     *
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function process () {
      
      // store the raw values from the super global
      self::$rawValues = $_REQUEST;
      
      // create HTML safe versions
      self::$safeValues = self::_sanitize(self::$rawValues);
      
    }  // end of function process
    
      
     
    /**
     * Get the raw values as a complete array.
     * 
     * @return array An associative array of submitted values in their original state.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function getRawValues () {
      
      return self::$rawValues;
      
    }  // end of function getRawValues
    
    
    
    /**
     * Get a single raw value from the array based on it's key.
     * 
     * @param string $key The key who's value should be retrieved in it's original state.
     * @return various The un-altered value associated with the key.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function getRawValue ($key) {
      
      $result = '';
      if (isset(self::$rawValues[$key])) {
        $result = self::$rawValues[$key];
      }
      
      return $result;
      
    }  // end of function getRawValue

    
    
    /**
     * Get the HTML save values as a complete array.
     * 
     * @return array An associative array of submitted values safe to be output on an HTML page.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function getSafeValues () {
      
      return self::$safeValues;
      
    }  // end of function getSafeValues
    
    
    
    /** 
     * Get a single HTML safe value from the array based on it's key.
     * 
     * @param string $key The key who's value should be retrieved in an HTML safe format.
     * @return various The HTML safe value associated with the key.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function getSafeValue ($key) {
      
      $result = '';
      if (isset(self::$safeValues[$key])) {
        $result = self::$safeValues[$key];
      }
      
      return $result;
      
    }  // end of function getSafeValue
    
    
    
    /**
     * Sanitize the value so it's safe to display on an HTML page.
     * 
     * Meant to be used in a recursive fashion to handle sanitizing
     *   complicated arrays.
     *   
     * @param various $value The value or array of values needing sanitized.
     * @return various The value or array of values converted to an HTML safe format.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected static function _sanitize ($value) {

      // the value provided was an array or associative array
      if (is_array($value)) {
        foreach ($value as $key => $singleValue) {
          // array of values or a value, make a recursive call to handle each array entry
          $value[$key] = self::_sanitize($singleValue);
        }
        
      // the value provided was a single value not an array
      } else {
        // odd special characters from Word 2010 with Firefox
        $value = str_replace(chr(226).chr(128).chr(152), "'", $value);
        $value = str_replace(chr(226).chr(128).chr(153), "'", $value);
        
        // escape all the HTML characters
        $value = htmlentities($value);
      }
      
      return $value;
            
    }  // end of function _sanitize
    
  }  // end of class Request
