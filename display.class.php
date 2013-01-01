<?php
  /**
   * Singleton object that provides methods for 
   *   formatting and displaying data in a 
   *   consistent way to site visitors.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 31 January 2006
   */
  class Display {

    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __construct() {} 
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __clone() {} 
    

    
    /**
     * Display a timestamp in one of many formats.
     * 
     * Valid values for $type:
     *   - datetime
     *   - date
     *   - time
     *   
     * Valid values for $format:
     *   - long (fully spelled out words where applicable)
     *   - medium (abbreviated words where applicable)
     *   - short (all numbers, no words)
     *   - any string that follows the PHP Date syntax (http://us2.php.net/manual/en/function.date.php)
     * 
     * @param integer $timestamp The unix timestamp to format.
     * @param string $type The combination of date and time to output.
     * @param string $format The format to use for outputting the date/time.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function dateTime ($timestamp, $type, $format) {
      
      // define all the formats
      $formats = array(
                        'date-short' => 'n/j/Y',
                        'date-medium' => 'D M j, Y',
                        'date-long' => 'l F j, Y',
                        'time-short' => 'g:ia',
                        'time-long' => 'g:i a',
                        'datetime-short' => 'n/j/Y g:ia',
                        'datetime-medium' => 'D M j, Y g:ia',
                        'datetime-long' => 'l F j, Y g:ia',
                      );

      // define the valid types and formats
      $validTypes = array( 'date', 'time', 'datetime' );
      $validFormats = array( 'short', 'medium', 'long' );
      
      // assume the worst
      $result = 'n/a';
      
      // see if we have a valid timestamp
      if (is_numeric($timestamp) && $timestamp > 0) {
        // see if we have a valid type
        if (in_array($type = strtolower($type), $validTypes)) {
          // see if we have a predefined format and the format exists
          if (in_array($format = strtolower($format), $validFormats) 
            && isset($formats[$type.'-'.$format])) {
            $result = date($formats[$type.'-'.$format], $timestamp);
          } else {
            // use the format provided
            $result = date($format, $timestamp);
          }
        }
      }
        
      return $result;
      
    }  // end of function dateTime
    
    
    
    /**
     * Display a Y/N value as a yes or no string.
     * 
     * @param string $value A string containing a 'y' or an 'n' which will be displayed as a 'Y','N','Yes', or 'No'.
     * @param boolean $useSingleLetter Determines if Y/N or Yes/No is output.
     * @return string Either a Y/N or a Yes/No string.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function yesNo ($value, $useSingleLetter = false) {

      $result = '';
      if (is_bool($value)) {
        $result = (($value) ? 'Yes' : 'No');
        
      } else {
        $value = strtoupper($value);
        if ($value == 'Y' || $value == 'YES') {
          $result = 'Yes';

        } elseif ($value == 'N' || $value == 'NO') {
          $result = 'No';
        }
      }
      
      if ($useSingleLetter) {
        $result = substr($result, 0, 1);
      }
      
      return $result;

    }  // end of function yesNo
    
    
    
    /**
     * Format text with carriage returns to use HTML line breaks
     *   or paragraphs.
     * 
     * @param string $text The text with carriage returns to display.
     * @param boolean $useParagraphs Whether or not to use HTML paragraph tags.
     * @return string An HTML formatted multi-line string.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function multilineText ($text, $useParagraphs = false) {
      
      // remove any of the \r codes
      $result = str_replace("\r", "", $text);
      
      // format the string with or without paragraph tags
      if ($useParagraphs) {
        // remove multiple carriage returns
        $result = str_replace("\n\n\n", "\n", $result);
        $result = str_replace("\n\n", "\n", $result);
        
        // replace the carriage returns with paragraph tags
        $result = str_replace("\n", '</p><p>', $result);
        $result = '<p>'.$result.'</p>';
        
      } else {
        $result = str_replace("\n\n", '<br />&nbsp;<br />', $result);
        $result = str_replace("\n", '<br />', $result);
      }
      
      return $result;
      
    }  // end of function multilineText

    
    
    /**
     * Display a truncated version of the text provided.
     * 
     * @param string $text The string of text to truncate and display.
     * @param integer $length The length to truncate the text to.
     * @param boolean $breakOnSpace Whether or not to truncate the text on a space.
     * @param boolean $includeEllipsis Whether or not to add the three dots (ellipsis) to the end of the string.
     * @return string A truncated form of the original text.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function truncatedText ($text, $length, $breakOnSpace = false, $includeEllipsis = true) {
      
      if (strlen($text) > $length) {
        if ($breakOnSpace) {
          $result = substr($text, 0, $length);
          $pos = strrpos($result, ' ');
          if ($pos !== false) {
            $result = substr($text, 0, $pos);
          }
        } else {
          $result = substr($text, 0, $length);
        }
        
        if ($includeEllipsis) {
          $result .= ' ...';
        }
      } else {
        $result = $text;
      }
      
      return $result;
      
    }  // end of function truncatedText
    
    
    
    /**
     * Add a span tag and appropriate class to mark the provided
     *   text as deleted.
     *   
     * @param string $text The text that is to be displayed as deleted.
     * @param boolean $deleted Whether or not the text is deleted.
     *                          Can be either Y/F or true/false.
     * @param string $cssClass the CSS class that styles deleted text.
     * @return string The text wrapped in an HTML span tag with the deleted CSS class applied.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function deletedText ($text, $deleted, $cssClass = 'deleted') {
      
      // determine the deleted status
      $isDeleted = false;
      if (is_bool($deleted) && $deleted) {
        $isDeleted = true;
      } elseif (is_string($deleted) && strtoupper($deleted) == 'Y') {
        $isDeleted = true;
      }

      // determine the right text to return based on the deleted status
      if ($isDeleted) {
        $result = '<span class="'.$cssClass.'">';
        $result .= $text.'</span>';
      
      } else {
        $result = $text;
      }
      
      return $result;
      
    }  // end of function deletedText
    
    
    
    /* METHODS FOR GENERATING OPTIONS FOR SELECT TAGS */
    
    /**
     * Generate a string of HTML option tags to be used with a 
     *   HTML select tag to create a drop down list.
     * 
     * @param array $arrayOfValues An associative array of name/value pairs to be turned into options.
     * @param string $nameField The field, or comma-delimited list of fields, to use from $arrayOfValues
     *                           for the name attribute of the option tag.
     * @param string $valueField The field, or comma-delimited list of fields, to use from $arrayOfValues
     *                            for the value attribute of the option tag.
     * @param various $currentValue The value that is currently selected in the list of option tags (optional).
     * @param string $initialName The name attribute to use for the initial item in the list of options (optional).
     * @param various $initialValue The value attribute to use for the initial item in the list of options (optional).
     * @return string The option tags carriage-return separated.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function dropDownOptions ($arrayOfValues, $nameField, $valueField, $currentValue = '', $initialName = '', $initialValue = '') {
      
      // generate the options list
      list($itemSelected, $options) = self::_generateOptionTags($arrayOfValues, $nameField, $valueField, $currentValue);
      
      // build the initial line if necessary
      $line = '';
      if ($initialName <> '') {
        $line = '<option value="'.$initialValue.'"';
        if ($itemSelected) {
          $line .= ' selected="selected"';
        }
        $line .= '>'.$initialName.'</option>'."\n";
      }

      // combine the initial line and the generated options and return it
      $result = $line.$options;
      return $result;
      
    }  // end of function dropDownOptions
    
    
    
    /**
     * Generate a string of HTML option tags to be used with a 
     *   HTML select tag to create a single or multi-select list.
     * 
     * @param array $arrayOfValues An associative array of name/value pairs to be turned into options.
     * @param string $nameField The field, or comma-delimited list of fields, to use from $arrayOfValues
     *                           for the name attribute of the option tag.
     * @param string $valueField The field, or comma-delimited list of fields, to use from $arrayOfValues
     *                            for the value attribute of the option tag.
     * @param various $currentValues The array of values that are currently selected in the
     *                                list of option tags.
     * @return string The option tags carriage-return separated.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function listOptions ($arrayOfValues, $nameField, $valueField, $currentValues) {
      
      // generate the options list
      list($itemSelected, $options) = self::_generateOptionTags($arrayOfValues, $nameField, $valueField, $currentValues);
      
      // return the resulting options
      return $options;
            
    }  // end of function listOptions
    
    
    
    /**
     * Generate the HTML option tags to be used with a HTML select tag.
     * 
     * @param array $arrayOfValues An associative array of name/value pairs to be turned into options.
     * @param string $nameField The field, or comma-delimited list of fields, to use from $arrayOfValues
     *                           for the name attribute of the option tag.
     * @param string $valueField The field, or comma-delimited list of fields, to use from $arrayOfValues
     *                            for the value attribute of the option tag.
     * @param various $currentValues The value, or array of values, that are currently selected in the
     *                                list of option tags.
     * @return array A two value array where the first value is whether or not one of the options was
     *                selected and the second value is the string of option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected static function _generateOptionTags ($arrayOfValues, $nameField, $valueField, $currentValues) {
      
      // create the option template
      $template = '<option value="[value]" [selected]>[name]</option>';
      
      // build the list of options
      $result = '';
      $itemSelected = false;
      foreach ($arrayOfValues as $record) {
        // handle the name field if it is more than one field
        if (strpos($nameField, ',') === false) {
          $nameValue = $record[$nameField];
        } else {
          $names = explode(',', $nameField);
          $nameValue = '';
          foreach ($names as $name) {
            if (strpos($name, "'") === false) {
              $nameValue .= $record[trim($name)].' ';
            } else {
              $nameValue .= substr(trim($name), 1, -1).' ';
            }
          }
          $nameValue = trim($nameValue);
        }
        
        // handle the value field if it is more than one field
        if (strpos($valueField, ',') === false) {
          $valueValue = ((string) $record[$valueField]);
        } else {
          $values = explode(',', $valueField);
          $valueValue = '';
          foreach ($values as $value) {
            if (strpos($value, "'") === false) {
              $valueValue .= $record[trim($value)].' ';
            } else {
              $valueValue .= substr(trim($value), 1, -1).' ';
            }
          }
          $valueValue = ((string) str_replace(' ', '_', trim($valueValue)));
        }

        // determine if this option should be selected
        if (is_array($currentValues) && isset($currentValues[$valueValue])) {
          $selected = 'selected="selected"';
          $itemSelected = true;
          
        } elseif ($currentValues == $valueValue) {
          $selected = 'selected="selected"';
          $itemSelected = true;
          
        } else {
          $selected = '';
        }
        
        // create the option line
        $line = str_replace('[value]', $valueValue, $template);
        $line = str_replace('[name]', $nameValue, $line);
        $line = str_replace('[selected]', $selected, $line);
        
        // add the line to the results
        $result .= $line."\n"; 
      }
      
      
      // return both the itemSelected and options string
      return array($itemSelected, $result);
      
    }  // end of function _generateOptionTags
    
    
    
    /* METHODS FOR GENERATING CALENDAR AND DATE/TIME RELATED ITEMS */
    
    /**
     * Generate an HTML table to represent a calendar using the smallest sized
     *   cells possible for each day.
     *   
     * @param integer $month The month in integer format (1-12) to display.
     * @param integer $year The year in 4-digit format to display.
     * @param boolean $markToday Whether or not to tag today's date with the "today" CSS class.
     * @param array $highlightDates An array of integer days of the month to mark as highlighted by
     *                               applying a special CSS class.
     * @param string $highlightCssClass The CSS class to apply to $highlightDates.
     * @param array $altHighlightDates An array of integer dates of the month to mark as highlighted
     *                                  in a different way by applying a special CSS class.
     * @param string $altHighlightCssClass The CSS class to apply to $altHighlightDates.
     * @return string The HTML necessary to display a table-based calendar.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function miniCalendar ( $month, $year, $markToday = false, 
                                   $highlightDates = array(), $highlightCssClass = 'calHighlightDate', 
                                   $altHighlightDates = array(), $altHighlightCssClass = 'calAltHighlightDate' ) {

      // determine today
      $today = strtotime(date('Y-m-d'));
      
      
      // start the table and add the header
      $cal = '<div class="miniCalendar">'."\n";
      $cal .= '  <table cellpadding="0" cellspacing="0" border="0">'."\n";
      $cal .= '    <caption>'.date('F', strtotime($month.'/1/'.$year)).' '.$year.'</caption>'."\n";
      $cal .= '    <thead>'."\n";
      $cal .= '      <tr>'."\n";
      $cal .= '        <th>S</th>'."\n";
      $cal .= '        <th>M</th>'."\n";
      $cal .= '        <th>T</th>'."\n";
      $cal .= '        <th>W</th>'."\n";
      $cal .= '        <th>T</th>'."\n";
      $cal .= '        <th>F</th>'."\n";
      $cal .= '        <th>S</th>'."\n";
      $cal .= '      </tr>'."\n";
      $cal .= '    </thead>'."\n";
      $cal .= '    <tbody>'."\n";
      
      // add the body of the table
      for ($i = 1; $i <= self::daysInMonth($month, $year); $i++) {
        $thisDate = mktime(0, 0, 0, $month, $i, $year);

        // see if we are doing the first week
        if ($i == 1) {
          $cal .= '      <tr>'."\n";
          
          // determine how many blanks we have for last month's dates
          $blankDays = date('w', $thisDate);
          if ($blankDays == 1) {
            $cal .= '        <td class="calOtherMonth">&nbsp;</td>'."\n";
          } elseif ($blankDays > 1) {
            $cal .= '        <td class="calOtherMonth" colspan="'.$blankDays.'">&nbsp;</td>'."\n";
          }
          
        // is this the start of a new week, then start a new row
        } elseif (date("w", $thisDate) == 0) {
          $cal .= '      <tr>'."\n";
        }

        
        // determine the CSS classes for this date
        $cssClasses = array();
        
        // see if this is today
        if ($thisDate == $today) {
          $cssClasses[] = 'calToday';
        }
        
        // see if this is a highlighted date
        if (isset($highlightDates) && is_array($highlightDates) && in_array(date('n/j/Y', $thisDate), $highlightDates)) {
          $cssClasses[] = $highlightCssClass;
        }
        
        // see if this is an alternate highlighted date
        if (isset($altHighlightDates) && is_array($altHighlightDates) && in_array(date('n/j/Y', $thisDate), $altHighlightDates)) {
          $cssClasses[] = $altHighlightCssClass;
        }
        
        
        // render the cell for this date.
        $cal .= '        <td';
        if (count($cssClasses) > 0) {
          $cal .= ' class="'.implode(' ', $cssClasses).'"';
        }
        $cal .= '>'.$i.'</td>'."\n";

        
        // if this is the last day of the week, end the row
        if (date('w', $thisDate) == 6) {
          $cal .= '      </tr>'."\n";
        }
      }

      
      // if this is not the last day of the week, then we need some
      //   blank cells to hold the place of next month's dates
      if (date('w', $thisDate) < 6) {
        $blankDays = 6 - date('w', $thisDate);
        $cal .= '        <td class="calOtherMonth" colspan="'.$blankDays.'">&nbsp;</td>'."\n";
        $cal .= '      </tr>'."\n";
      }
      
      
      // close out the table
      $cal .= '    </tbody>'."\n";
      $cal .= '  </table>'."\n";
      $cal .= '</div>'."\n";
 
      
      // return the calendar code
      return $cal;

    }  // end of function DisplayMiniCalendar



    /**
     * Return the number of days in a given month/year pairing.
     * 
     * @param integer $month The month of they year in integer form (1-12).
     * @param integer $year The year in 4-digit format.
     * @return integer The number of days in the given month/year pairing.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function daysInMonth ($month, $year) {

      /* checkdate() returns true if the month/day/year combination is valid
       *   so we check from greatest possible number of days until least
       *   till we get a hit.
       */
      if (checkdate($month, 31, $year)) return 31;
      if (checkdate($month, 30, $year)) return 30;
      if (checkdate($month, 29, $year)) return 29;
      if (checkdate($month, 28, $year)) return 28;
      return 0; // error

    }  // end of function daysInMonth
    
    
    
    /**
     * Generate the HTML option tags for an HTML select tag that is used
     *   to select a month.
     *   
     * @param integer $currentValue The currently selected month as an integer (1-12).
     * @param string $initialName The text to display for the first option that is not a month.
     * @param boolean $useNumbers Whether or not to display the numbers or the words for each month selection.
     * @return string The HTML text for the option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function monthDropDownOptions ($currentValue, $initialName, $useNumbers = false) {
      
      // define the months array
      $months = array();
      if ($useNumbers) {
        for ($i = 1; $i <= 12; $i++) {
          $months[] = array( 'monthid' => $i,  'monthname' => ((string) str_pad($i, 2, ' ', STR_PAD_LEFT)) );
        }
        
      } else {
        $monthText = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );

        for ($i = 0; $i < count($monthText); $i++) {
          $months[] = array( 'monthid' => $i + 1,  'monthname' => $monthText[$i] );
        }
      }
      
      
      // generate the options
      $results = self::dropDownOptions($months, 'monthname', 'monthid', $currentValue, $initialName);
      return $results;

    }  // end of function monthDropDownOptions
    
    
    
    /**
     * Generate the HTML option tags for an HTML select tag that is used
     *   to select a year.
     *   
     * Starting year defaults to 5 years before today.
     * Ending year defaults to 5 years after today.
     *   
     * @param integer $currentValue The currently selected year as an integer.
     * @param string $initialName The text to display for the first option that is not a year.
     * @param integer $startYear The year to start the list with, defaults to 5 years ago.
     * @param integer $endYear The year to end the list with, defaults to 5 years from now.
     * @return string The HTML text for the option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function yearDropDownOptions ($currentValue, $initialName, $startYear = 0, $endYear = 0) {
      
      // set the defaults for start/end year if necessary
      $thisYear = date('Y');
      if ($startYear == 0) {  $startYear = $thisYear - 5;  }
      if ($endYear == 0) {  $endYear = $thisYear + 5;  }
      
      // generate the years to display
      $years = array();
      for ($i = $startYear; $i <= $endYear; $i++) {
        $years[] = array( 'yearid' => $i, 'yearname' => $i );
      }
                       
      
      // generate options
      $results = self::dropDownOptions($years, 'yearname', 'yearid', $currentValue, $initialName);
      return $results;
      
    }  // end of function yearDropDownOptions
    
    
    
    /**
     * Generate the HTML option tags for an HTML select tag that is used
     *   to select a day of the month.
     *   
     * @param integer $currentValue The currently selected day of the month as an integer (1-31).
     * @param string $initialName The text to display for the first option that is not a day of the month.
     * @param integer $useMonth The integer of the month to use to determine the number of days (optional).
     * @param integer $useYear the integer of the year to use to determine the number of days (optional).
     * @return string The HTML text for the option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function dayDropDownOptions ($currentValue, $initialName, $useMonth = 0, $useYear = 0) {
      
      // determine the last day to use
      $lastDay = 31;
      if (is_numeric($useMonth) && $useMonth > 0 && is_numeric($useYear) && $useYear > 0) {
        $lastDay = self::daysInMonth($useMonth, $useYear);
      }
      
      // define the days array
      $days = array();
      for ($i = 1; $i <= $lastDay; $i++) {
        $days[] = array( 'dayid' => $i, 'day' => ((string) str_pad($i, 2, ' ', STR_PAD_LEFT)) );
      }
      
      // generate options
      $results = self::dropDownOptions($days, 'day', 'dayid', $currentValue, $initialName);
      return $results;
      
    }  // end of function dayDropDownOptions
    
    
    
    /**
     * Generate the HTML option tags for an HTML select tag that is used
     *   to select an hour.
     *   
     * @param integer $currentValue The currently selected hour as an integer (1-12 or 0-23).
     * @param string $initialName The text to display for the first option that is not an hour.
     * @param boolean $use24HourTime Whether or not to use 24 hour time to generate the hours.
     * @return string The HTML text for the option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function hourDropDownOptions ($currentValue, $initialName, $use24HourTime = false) {
      
      // define the hours arrays
      $hours = array();
      if ($use24HourTime) {
        for ($i = 0; $i < 24; $i++) {
          $hours[] = array( 'hourid' => $i, 'hour' => ((string) str_pad($i, 2, ' ', STR_PAD_LEFT)) );
        }
              
      } else {
        for ($i = 1; $i <= 12; $i++) {
          $hours[] = array( 'hourid' => $i, 'hour' => ((string) str_pad($i, 2, ' ', STR_PAD_LEFT)) );
        }
      }
      
      // generate options
      $results = self::dropDownOptions($hours, 'hour', 'hourid', $currentValue, $initialName);
      return $results;
      
    }  // end of function hourDropDownOptions



    /**
     * Generate the HTML option tags for an HTML select tag that is used
     *   to select a minute.
     *   
     * @param integer $currentValue The currently selected minute as an integer (0-59).
     * @param string $initialName The text to display for the first option that is not a minute.
     * @param integer $interval The number of minutes apart each option should be.
     * @return string The HTML text for the option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function minuteDropDownOptions ($currentValue, $initialName, $interval = 5) {
      
      // define the minutes array
      $minutes = array();
      for ($i = 0; $i < 60; $i += $interval) {
        $minutes[] = array( 'minuteid' => $i, 'minute' => ((string) str_pad($i, 2, '0', STR_PAD_LEFT)) );
      }

      // generate options
      $results = self::dropDownOptions($minutes, 'minute', 'minuteid', $currentValue, $initialName, -1);
      return $results;
      
    }  // end of function minuteDropDownOptions
    
    
    
    /**
     * Generate the HTML option tags for an HTML select tag that is used
     *   to select a meridiem (am/pm).
     *   
     * @param string $currentValue The currently selected meridiem (am/pm).
     * @param string $initialName The text to display for the first option that is not a meridiem.
     * @return string The HTML text for the option tags.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function meridiemDropDownOptions ($currentValue, $initialName) {
      
      // define the meridiem array
      $meridiems = array( array( 'meridiemid' => 'am', 'meridiem' => 'am' ),
                          array( 'meridiemid' => 'pm', 'meridiem' => 'pm' ) );
      
      // generate options
      $results = self::dropDownOptions($meridiems, 'meridiem', 'meridiemid', $currentValue, $initialName);
      return $results;
      
    }  // end of function meridiemDropDownOptions
    
  }  // end of class Display
