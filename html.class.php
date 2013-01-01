<?php
  /**
   * Singleton object for outputting pretty HTML.
   * 
   * Provides for generating HTML tags and indenting
   *   lines of HTML properly.
   * 
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 31 March 2011
   */
  class HTML {

    /**
     * The number of spaces each level of code is indented.
     */
    private static $indentSpaces = 2;
    
    
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __construct() {} 
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __clone() {} 
        
        
    
    /**
     * Generate the HTML for an element tag and its associated attributes.
     * 
     * @param string $tag The HTML element tag to be generated.
     * @param array $attributes An associative array of attributes for this element tag.
     * @param boolean $selfClose Should this tag be closed or is there a separate closing tag.
     * @param boolean $multiLine Should the tag and it's attributed be distributed over multiple lines or all on one line.
     * @param integer $indentLevel The number of levels this tag should be indented.
     * @return string The HTML to display the specified element tag with it's specified attributes.
     * @author Paul Rentschler <paul@rentschler.ws>
     */ 
    public static function renderTag ($tag, $attributes, $selfClose=true, $multiLine=false, $indentLevel=0) {
      
      $html = '';
      
      // build a tag that spans multiple lines
      if ($multiLine) {
        // determine the indent levels
        $mainIndent = $indentLevel * self::$indentSpaces;
        $subIndent = $mainIndent + strlen($tag) + 2;
        
        // build an array of each line of HTML
        $lines = array();
        foreach ($attributes as $attribute => $value) {
          $lines[] = self::_prefixWithSpaces($attribute.'="'.$value.'"', $subIndent);
        }
        
        // update the first line to include the tag
        $lines[0] = self::_prefixWithSpaces('<'.$tag.' '.trim($lines[0]), $mainIndent);
        
        // update the last line to close the tag
        $lastIndex = count($lines) - 1;
        if ($selfClose) {
          $lines[$lastIndex] .= ' />';
        } else {
          $lines[$lastIndex] .= '>';
        }
        
        // combine the lines together with carriage returns
        $html = implode("\n", $lines);
        
      // build a single line tag
      } else {
        $html = '<'.$tag;
        foreach ($attributes as $attribute => $value) {
          $html .= ' '.$attribute.'="'.$value.'"';
        }
        if ($selfClose) {
          $html .= ' />';
        } else {
          $html .= '>';
        }
        
        // properly indent the line
        $html = self::_prefixWithSpaces($html, ($indentLevel * self::$indentSpaces));
      }
      
      // return the resulting HTML
      return $html;
      
    }  // end of function renderTag
    
    
    
    /**
     * Indents a line or multiple lines of HTML by the specified
     *   $indentLevel.
     *   
     * @param string $html The HTML code to be indented.
     * @param integer $indentLevel The number of levels this tag should be indented.
     * @return string The indented HTML code.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public static function indent ($html, $indentLevel) {
      
      // worst case, return what we got
      $indentedHtml = $html;
      
      // determine if we are doing a single line or multiline string
      if (strpos($html, "\n") !== false) {
        // multiline string
        $lines = explode("\n", $html);
        foreach ($lines as &$line) {
          $line = self::_prefixWithSpaces($line, $indentLevel * self::$indentSpaces);
        }
        
        $indentedHtml = implode("\n", $lines);
        
      } else {
        // single line string
        $indentedHtml = self::_prefixWithSpaces($html, $indentLevel * self::$indentSpaces);
      }
      
      return $indentedHtml;
      
    }  // end of function indent
    
    
    
    /**
     * Set the number of spaces per indent level.
     * 
     * @param integer $spaces Number of spaces for each indent level
     * @author Paul Rentschler <par117@psu.edu>
     */
    public static function setIndentSpaces ($spaces) {
      
      if (is_numeric($spaces) && $spaces > 0) {
        self::$indentSpaces = ((int) $spaces);
      }
      
    }  // end of function setIndentSpaces
    
    
    
    /**
     * Add the specified number of spaces to the beginning of the provides string.
     * 
     * @param string $text The text to prefix with spaces.
     * @param integer $spaces The number of spaces to prefix $text with.
     * @return string The space prefixed text string.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected static function _prefixWithSpaces ($text, $spaces) {
      
      for ($i = $spaces; $i > 0; $i--) {
        $text = ' '.$text;
      }
      
      return $text;
      
    }  // end of function _prefixWithSpaces
    
  }  // end of class HTML
