<?php
  /**
   * Define the validation error types.
   * 
   * @author Paul Rentschler <par117@psu.edu>
   */
  define('VALIDATION_ERROR_NOERROR',       0);
  define('VALIDATION_ERROR_LENGTH',       10);
  define('VALIDATION_ERROR_TOOLONG',      12);
  define('VALIDATION_ERROR_TOOSHORT',     14);
  define('VALIDATION_ERROR_PATTERN',      20);
  define('VALIDATION_ERROR_BLANK',        30);
  define('VALIDATION_ERROR_OUTOFRANGE',   40);
  define('VALIDATION_ERROR_TOOBIG',       42);
  define('VALIDATION_ERROR_TOOSMALL',     44);
  define('VALIDATION_ERROR_WRONGTYPE',    50);

  
  /**
   * Validate all forms of user input.
   * 
   * filename: /includes/validate.class.php
   * 
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 18 January 2006
   */
  class Validate {

    /**
     * Store the error message to be retrieved by the calling code.
     */
    protected $errorMessage = '';
    
    /**
     * Indicates what the generic field name is in the error message so
     *   it can be replaced by the getErrorMessage method.
     */
    protected $errorGenericFieldName = '';
    
    /**
     * The type of validation error that occured.
     */
    protected $errorType = VALIDATION_ERROR_NOERROR;
    
    /**
     * The result of a validation call. Assumed to be true unless an problem is found.
     */
    protected $validated = true;
    
    /**
     * Define the valid symbols for strings that can be used in creating RegEx patterns.
     * 
     * This value is referenced as: self::validStringSymbols
     */
    const validStringSymbols = '!@#~\$%\^&\*\-_\+=()\[\]{}\\\\\/:;\'\",\.\?';
    
    /**
     * Define the valid symbols for strings as text that can be used in error messages.
     *
     * This value is referenced as: self::validStringSymbolsLabel
     */
    const validStringSymbolsLabel = '!@#~$%^&*-_+=()[]{}\\/:;\'",.?';
    

    
    /**
     * Validate a string of text for valid characters.
     * 
     * Validate a string to ensure that it only contains valid characters and
     *   optionally is within certain length criteria and/or starts with
     *   certain types of characters.
     *   
     * @param string $text The text string to validate.
     * @param integer $minLength The minimum length of the string, defaults to zero (optional).
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @param boolean $startWithAlpha Indicates if the string must start with an alphabetical character (optional).
     * @param boolean $startWithNumber Indicates if the string must start with a numeric character (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    function validateString ($text, $minLength = 0, $maxLength  = 0, $startWithAlpha = false, $startWithNumber = false) {

      // set the pattern to test the string with
      $pattern = '[a-z0-9\x20'.self::validStringSymbols.']*';
      $pattern = $this->_prefixPatternWithStartingCondition($pattern, $startWithAlpha, $startWithNumber);
      
      // use the validateStringCustomPattern to do the heavy lifting
      if (!$this->validateStringCustomPattern($text, $pattern, $minLength, $maxLength)) {
        // update the error message if the validation error was because of the pattern
        if ($this->errorType == VALIDATION_ERROR_PATTERN) {
          $msg = 'string can contain letters, numbers, spaces and the ';
          $msg .= 'following symbols: '.self::validStringSymbolsLabel;
          $this->_updatePatternErrorMessage($msg, $startWithAlpha, $startWithNumber);
        }
      }
            
      // return the result
      return $this->validated;

    }  // end of function validateString
    
    
    
    /**
     * Validate a string of text for valid characters.
     * 
     * Validate a string to ensure that it only contains letters and
     *   optionally is within certain length criteria.
     *   
     * @param string $text The text string to validate.
     * @param integer $minLength The minimum length of the string, defaults to zero (optional).
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateAlphaString ($text, $minLength = 0, $maxLength = 0) {

      // set the pattern to test the string with
      $pattern = '[a-z\x20]*';

      // use the validateStringCustomPattern to do the heavy lifting
      if (!$this->validateStringCustomPattern($text, $pattern, $minLength, $maxLength)) {
        // update the error message if the validation error was because of the pattern
        if ($this->errorType == VALIDATION_ERROR_PATTERN) {
          $msg = 'string can contain only letters and spaces.';
          $this->_updatePatternErrorMessage($msg, false, false);
        }
      }
            
      // return the result
      return $this->validated;
      
    }  // end of function validateAlphaString
    
    
    
    /**
     * Validate a string of text for valid characters.
     * 
     * Validate a string to ensure that it only contains letters or numbers and 
     *   optionally is within certain length criteria and/or starts with
     *   certain types of characters.
     *   
     * @param string $text The text string to validate.
     * @param integer $minLength The minimum length of the string, defaults to zero (optional).
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @param boolean $startWithAlpha Indicates if the string must start with an alphabetical character (optional).
     * @param boolean $startWithNumber Indicates if the string must start with a numeric character (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateAlphaNumericString ($text, $minLength = 0, $maxLength = 0, $startWithAlpha = false, $startWithNumber = false) {

      // set the pattern to test the string with
      $pattern = '[a-z0-9\x20]*';
      $pattern = $this->_prefixPatternWithStartingCondition($pattern, $startWithAlpha, $startWithNumber);
      
      // use the validateStringCustomPattern to do the heavy lifting
      if (!$this->validateStringCustomPattern($text, $pattern, $minLength, $maxLength)) {
        // update the error message if the validation error was because of the pattern
        if ($this->errorType == VALIDATION_ERROR_PATTERN) {
          $msg = "string can contain letters, numbers, and spaces.";
          $this->_updatePatternErrorMessage($msg, $startWithAlpha, $startWithNumber);
        }
      }
      
      // return the result
      return $this->validated;
      
    }  // end of function validateAlphaNumericString
    
    
    
    /**
     * Validate a string of text for valid characters.
     * 
     * Validate a string to ensure that it only contains valid characters and
     *   optionally is within certain length criteria and/or starts with
     *   certain types of characters.
     * This method differs from validateString by allowing for carriage
     *   return characters in the string.
     *   
     * @param string $text The text string to validate.
     * @param integer $minLength The minimum length of the string, defaults to zero (optional).
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @param boolean $startWithAlpha Indicates if the string must start with an alphabetical character (optional).
     * @param boolean $startWithNumber Indicates if the string must start with a numeric character (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateStringBlock ($text, $minLength = 0, $maxLength = 0, $startWithAlpha = false, $startWithNumber = false) {

      // set the pattern to test the string with
      $pattern = '[a-z0-9\s'.self::validStringSymbols.']*';
      $pattern = $this->_prefixPatternWithStartingCondition($pattern, $startWithAlpha, $startWithNumber);
      
      // use the validateStringCustomPattern to do the heavy lifting
      if (!$this->validateStringCustomPattern($text, $pattern, $minLength, $maxLength)) {
        // update the error message if the validation error was because of the pattern
        if ($this->errorType == VALIDATION_ERROR_PATTERN) {
          $msg = 'string can contain letters, numbers, spaces and the ';
          $msg .= 'following symbols: '.self::validStringSymbolsLabel;
          $this->_updatePatternErrorMessage($msg, $startWithAlpha, $startWithNumber);
        }
      }
        
      // return the result
      return $this->validated;
      
    }  // end of function validateStringBlock
    
    
    
    /**
     * Validate a string of text for valid characters.
     * 
     * Validate a string to ensure that it only contains valid characters and
     *   optionally is within certain length criteria and/or starts with
     *   certain types of characters.
     * The valid characters are provided via a regular expression specified
     *   by $pattern. That will be prepended with /^ and postpended with $/Di
     *   
     * @param string $text The text string to validate.
     * @param string $pattern The regular expression to test the string against.
     * @param integer $minLength The minimum length of the string, defaults to zero (optional).
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateStringCustomPattern ($text, $pattern, $minLength = 0, $maxLength = 0) {

      // initialize the return values
      $this->_reset();

      // set the pattern to test the string with
      $pattern = '/^'.$pattern.'$/Di';

      $text = $this->_deMicrosoft(stripslashes($text));
      if ($maxLength > 0 && strlen($text) > $maxLength) {
        $this->errorType = VALIDATION_ERROR_TOOLONG;
        $msg = "The string is greater than the maximum length (".$maxLength.") specified.";
        
      } elseif ($minLength > 0 && strlen($text) < $minLength) {
        $this->errorType = VALIDATION_ERROR_TOOSHORT;
        $msg = "The string is not longer than the minimum length (".$minLength.") specified.";

      } elseif (!preg_match($pattern, $text)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
        $msg = "The string contains one or more invalid characters.";
      }
      
      // set the error if there was one
      if (isset($msg) && $msg <> '') {
        $this->_setErrorMessage($msg, 'string');
      }
      
      // return the result
      return $this->validated;
      
    }  // end of function validateStringCustomPattern
    

    
    /**
     * Validate a string as a valid name for a person.
     * 
     * Validate a string to ensure that it only contains valid characters and
     *   optionally is within certain length.
     *   
     * @param string $text The text string to validate.
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validatePersonName ($text, $maxLength = 0) {

      // initialize the return values
      $this->_reset();

      // set the pattern to test the string with
      $pattern = '[a-z0-9\x20\-\'\.]*';
      $pattern = $this->_prefixPatternWithStartingCondition($pattern, true, false);
      
      // use the validateStringCustomPattern to do the heavy lifting
      if (!$this->validateStringCustomPattern($text, $pattern, 0, $maxLength)) {
        // update the error message if the validation error was because of the pattern
        if ($this->errorType == VALIDATION_ERROR_PATTERN) {
          $msg = 'string can contain letters, numbers, spaces and the ';
          $msg .= 'following symbols: -\'.';
          $this->_updatePatternErrorMessage($msg, true, false);
        }
      }
        
      // return the result
      return $this->validated;
      
    }  // end of function validatePersonName
    
    
    
    /**
     * Validate a string as a valid phone number.
     * 
     * Validate a string to ensure that it contains only numbers and
     *   is in the format of ###-###-#### with an optional 8 digit
     *   extension prefixed by an x or an X.
     *   
     * @param string $phoneNumber The phone number string to validate.
     * @param boolean $allowExtension Whether or not to allow the phone number to have an extension.
     * @return boolean Whether or not the phone number is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validatePhoneNumber ($phoneNumber, $allowExtension) {
      
      // initialize the return values
      $this->_reset();

      // set the pattern to test the phone number with
      $pattern = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}';
      if ($allowExtension) {
        $pattern .= '(\x20[xX][0-9]{1,8})?';
      }
      $pattern .= '$/D';
      
      $phoneNumber = $this->_deMicrosoft(stripslashes($phoneNumber));
      if (!preg_match($pattern, $phoneNumber)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
        
        $msg = 'The phone number contains one or more invalid characters or ';
        $msg .= 'is not in the correct format. The phone number should be ';
        if ($allowExtension) {
          $msg .= 'in the format: ###-###-####[ x#[#######]]. The square ';
          $msg .= 'brackets [] indicate optional items in the format.';
        } else {
          $msg .= 'in the format: ###-###-####.';
        }
        $this->_setErrorMessage($msg, 'phone number');
      }
      
      // return the result
      return $this->validated;

    }  // end of function validatePhoneNumber
    
    
    
    /**
     * Validate a string as a valid e-mail address.
     * 
     * Plus addressing in the username portion of the e-mail address is allowed.
     * Good explanation of e-mail RegExs: http://www.regular-expressions.info/email.html
     *   
     * @param string $emailAddress The e-mail address string to validate.
     * @return boolean Whether or not the e-mail address is valid.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateEmailAddress ($emailAddress) {
      
      // initialize the return values
      $this->_reset();

      // set the pattern to test the e-mail address with
      $pattern = '/^[a-z0-9._%+-]+@([-a-z0-9]+\.)+[a-z]{2,4}$/Di';

      $emailAddress = stripslashes($emailAddress);
      if (!preg_match($pattern, $emailAddress)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
        
        $msg = 'The e-mail address contains one or more invalid ';
        $msg .= 'characters or is not in the correct format. ';
        $msg .= 'The e-mail address should be in the format: ';
        $msg .= 'username@domain.tld (example: jsmith@yahoo.com).';
        $this->_setErrorMessage($msg, 'e-mail address');
      }
      
      // return the result
      return $this->validated;

    }  // end of function validateEmailAddress
    
    
    
    /**
     * Validate a string as a valid password.
     * 
     * Validate a string to ensure that it only contains valid characters and
     *   optionally is within certain length criteria and/or starts with
     *   certain types of characters.
     *   
     * @param string $text The text string to validate.
     * @param integer $minLength The minimum length of the string, defaults to zero.
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @param boolean $startWithAlpha Indicates if the string must start with an alphabetical character (optional).
     * @param boolean $startWithNumber Indicates if the string must start with a numeric character (optional).
     * @return boolean Whether or not the string is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    function ValidatePassword ($text, $minLength, $maxLength = 0, $startWithAlpha = false, $startWithNumber = false) {
      
      // initialize the return values
      $this->Validate();
      
      // set the pattern to test the password with
      $pattern = '[a-z0-9!@#\$%\^&\*\-_\+=:;\'\",\.\?]*';
      $pattern = $this->_prefixPatternWithStartingCondition($pattern, $startWithAlpha, $startWithNumber);
      
      // see if the password is blank
      if ($text == '') {
        $this->errorType = VALIDATION_ERROR_BLANK;
        $this->_setErrorMessage('The string is blank.', 'string');

      } else {
        // use the validateStringCustomPattern to do the heavy lifting
        $this->validateStringCustomPattern($text, $pattern, $minLength, $maxLength);
      }
        
      // update the error message if the validation error was because of the pattern or blank
      if ($this->errorType == VALIDATION_ERROR_PATTERN || $this->errorType == VALIDATION_ERROR_BLANK) {
        $msg = 'string can contain letters, numbers, spaces and the ';
        $msg .= 'following symbols: !@#$%^&*-_+=:;\'",.?';
        $this->_updatePatternErrorMessage($msg, $startWithAlpha, $startWithNumber);
      }
            
      // return the result
      return $this->validated;
      
    }  // end of function validatePassword
    


    /**
     * Validate a string as a valid 5 or 9 digit postal code.
     * 
     * This only works on U.S. formatted postal codes (commmonly called
     *   zip codes). The plus four (+4) part of the postal code is optional.
     *   
     * @param string $postalCode The postal code string to validate.
     * @return boolean Whether or not the postal code is valid.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validatePostalCode ($postalCode) {
      
      // initialize the return values
      $this->_reset();

      // set the pattern to test the postal code with
      $pattern = '/^\d{5}(-\d{4})?$/D';

      $postalCode = stripslashes($postalCode);
      if (!preg_match($pattern, $postalCode)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
        
        $msg = "The postal code contains one or more invalid characters or ";
        $msg .= "is not in the correct format. The postal code should be ";
        $msg .= "in the format: #####[-####]. The square ";
        $msg .= "brackets [] indicate optional items in the format.";
        $this->_setErrorMessage($msg, 'postal code');
      }
      
      // return the result
      return $this->validated;

    }  // end of function validatePostalCode



    /**
     * Validate a string as a valid date.
     * 
     * Dates must be in the format of m/d/yyyy where a one or two
     *   digit month and day are allowed but a four digit year
     *   is required.
     *   
     * @param string $date The date string to validate.
     * @return boolean Whether or not the date is valid.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateDate ($date) {
      
      // initialize the return values
      $this->_reset();

      // set the pattern to test the date with
      $pattern = '/^\d{1,2}\/\d{1,2}\/\d{4}$/D';    

      $date = stripslashes($date);
      if (!preg_match($pattern, $date)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
        
        $msg = 'The date contains one or more invalid characters or ';
        $msg .= 'is not in the correct format. The date should be ';
        $msg .= 'in the format: m/d/yyyy.';
        $this->_setErrorMessage($msg, 'date');
      }
      
      // return the result
      return $this->validated;

    }  // end of function validateDate



    /**
     * Validate a string as a valid web site url.
     * 
     * Url's are not checked for the protocol (http://) but can have
     *   a port number (:8080). The Url can contain any number of
     *   subdirectories, a filename, and url parameters.
     *   
     * @param string $url The url string to validate.
     * @param integer $maxLength The maximum length of the string, defaults to zero (any length) (optional).
     * @return boolean Whether or not the url is valid.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateWebUrl ($url, $maxLength = 0) {
      
      // set the pattern to test the url with
      $pattern = '/^[A-Za-z0-9%&\/\-_\+=:\.\#?]*$/Di';

      // use the validateStringCustomPattern to do the heavy lifting
      if (!$this->validateStringCustomPattern($url, $pattern, 0, $maxLength)) {
        // update the error message if the validation error was because of the pattern
        if ($this->errorType == VALIDATION_ERROR_PATTERN) {
          $msg .= 'The string can contain letters, numbers, and the ';
          $msg .= 'following symbols: #%&-_+=:/.?';
          $this->_updatePatternErrorMessage($msg, true, false);
        }
      }
      
      // update the error message so it uses "url" as the field name instead of "string"
      $msg = str_replace('string', 'url', $this->errorMessage);
      $this->_setErrorMessage($msg, 'url');
      
      // return the result
      return $this->validated;

    }  // end of function validateWebUrl
    
    
    
    /**
     * Validate a string as a valid IP address.
     * 
     * @param string $ipAddress The ip address string to validate.
     * @return boolean Whether or not the ip address is valid.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateIPAddress ($ipAddress) {
      
      // initialize the return values
      $this->_reset();
      
      // set the pattern to test the IP with
      $pattern = '/^(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))$/Di';
      
      $ipAddress = stripslashes($ipAddress);
      if (!preg_match($pattern, $ipAddress)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
              
        $msg = 'The IP address contains one or more invalid characters ';
        $msg .= 'or is not in the correct format. The ip address should be ';
        $msg .= 'in the format #.#.#.# where each # ranges from 0 to 255 ';
        $msg .= '(ex: 127.0.0.1).';
        $this->_setErrorMessage($msg, 'IP address');
      }
      
      // return the result
      return $this->validated;
      
    }  // end of function validateIPAddress
    
    
    
    /**
     * Validate a string as a valid HTML color code.
     * 
     * HTML color codes are 6 character hexidecimal numbers where the
     *   first two characters represent the red color, the second two
     *   characters represent the green color, and the third two
     *   characters represent the blue color.
     * 
     * @param string $colorCode The HTML color code string to validate.
     * @return boolean Whether or not the HTML color code is valid.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function validateHTMLColorCode ($colorCode) {
      
      // initialize the return values
      $this->_reset();
      
      // set the pattern to test the color code with
      $pattern = '/^([0-9A-F]{6})$/Di';
      
      $colorCode = strtoupper($colorCode);
      if (strlen($colorCode) <> 6) {
        $this->errorType = VALIDATION_ERROR_LENGTH;
        $msg = 'The color code is not six characters in length. ';
        
      } elseif (!preg_match($pattern, $colorCode)) {
        $this->errorType = VALIDATION_ERROR_PATTERN;
        $msg = 'The color code contains one or more invalid characters ';
        $msg .= 'or is not in the correct format. ';
      }

      // finish the error message and set it
      if (isset($msg) && $msg <> '') {
        $msg .= 'The color code should be ';
        $msg .= 'in the format RRGGBB where each character is either 0-9 or A-F ';
        $msg .= 'and RR = red color code, GG = green color code, and BB = ';
        $msg .= 'blue color code. White is FFFFFF and Black is 000000.';
        $this->_setErrorMessage($msg, 'color code');
      }
      
      // return the result
      return $this->validated;
      
    }  // end of function validateHTMLColorCode


    
    /**
     * Validate a number.
     * 
     * Validate a number to ensure that it is numeric and optionally is within 
     *   certain min/max constrants.
     *   
     * @param string $value The number to validate.
     * @param integer $minimum The minimum value of the number, defaults to '' (no minimum) (optional).
     * @param integer $maximum The maximum value of the number, defaults to '' (no maximum) (optional).
     * @return boolean Whether or not the number is valid based on the criteria provided.
     *                   Use getErrorMessage() to retrieve the error message if false is returned.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function validateNumber ($value, $minimum = '', $maximum = '') {

      // initialize the return values
      $this->_reset();

      if (!is_numeric($value)) {
        $this->errorType = VALIDATION_ERROR_WRONGTYPE;
        $msg = 'The number provided is not numeric.';
        
      } elseif (is_numeric($minimum) && $value < $minimum) {
        $this->errorType = VALIDATION_ERROR_TOOSMALL;
        $msg = 'The number is smaller than the minimum allowed value ('.$minimum.').';
        
      } elseif (is_numeric($maximum) && $value > $maximum) {
        $this->errorType = VALIDATION_ERROR_TOOBIG;
        $msg = 'The number is larger than the maximum allowed value ('.$maximum.').';
      }
      
      // set the error if there was one
      if (isset($msg) && $msg <> '') {
        $this->_setErrorMessage($msg, 'number');
      }
      
      // return the result
      return $this->validated;
      
    }  // end of function validateNumber
    

    
    /**
     * Sanitize an array of values for displaying to the end user.
     * 
     * Recursively processes an associative array of values and
     *   applies the htmlentities() function to each value to
     *   ensure that it's safe to display to the end user.
     * Common usage is to apply it to $_GET, $_POST, and/or $_REQUEST.
     * Can also santitize a single value instead of an array.
     * 
     * @param array $values An associative array of values that need sanitizing.
     * @return array An associative array containing web-safe values.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function sanitizeFormValues ($formValues) {
      
      if (is_array($formValues)) {
        foreach ($formValues as $key => $value) {
          // array of values or a value, make a recursive call to handle each array entry
          $formValues[$key] = $this->sanitizeFormValues($value);
        }
        
      // the formValues provided was a single value not an array
      } else {
        // odd special characters from Word 2010 with Firefox
        $formValues = str_replace(chr(226).chr(128).chr(152), "'", $formValues);
        $formValues = str_replace(chr(226).chr(128).chr(153), "'", $formValues);
        
        // escape all the HTML characters
        $formValues = htmlentities($formValues);
      }
      
      return $formValues;
      
    }  // end of function sanitizeFormValues

    
    
    /**
     * Get the validation error message.
     * 
     * Optionally lets you replace the field name's placeholder
     *   with the $label text provided.
     *   
     * @param string $label The name of the field to be substituted for in the error message (optional).
     * @return string The error message generated by the validation.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getErrorMessage ($label = '') {
      
      // set the default return value
      $result = $this->errorMessage;
      
      // see if we need to replace the field name's placeholder
      if ($label <> '' && $this->errorGenericFieldName <> '') {
        $result = str_replace($this->errorGenericFieldName, $label, $result);
      }
      
      return $result;
      
    }  // end of function getErrorMessage

    
    
    /**
     * Update the RegEx pattern to handle starting letters/numbers.
     * 
     * Add a prefix to the provided regular expression to require
     *   that the string start with a letter, number, or either.
     *   
     * @param string $pattern The regular expression to update.
     * @param boolean $startWithAlpha Indicates if the string must start with an alphabetical character.
     * @param boolean $startWithNumber Indicates if the string must start with a numeric character.
     * @return string The updated regular expression pattern with the necessary prefix.
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _prefixPatternWithStartingCondition ($pattern, $startWithAlpha, $startWithNumber) {
      
      if ($startWithAlpha && $startWithNumber) {
        $result = '[a-z0-9]'.$pattern;
      } elseif ($startWithAlpha) {
        $result = '[a-z]'.$pattern;
      } elseif ($startWithNumber) {
        $result = '[0-9]'.$pattern;
      } else {
        $result = $pattern;
      }
      
      return $result;
      
    }  // end of function _prefixPatternWithStartingCondition
    
    
    
    /**
     * Updates the error message with details about pattern errors.
     * 
     * Updates the error message when the validation error is caused 
     *   by the string not matching the regular expression pattern and
     *   the pattern might have required a starting alphabetical or
     *   numeric character.
     * Also provides more details to the end user about what characters
     *   are valid via the $validCharactersLabel parameter.
     * 
     * @param string $validCharactersLabel Text explaining to the user what characters are valid.
     * @param boolean $startWithAlpha Indicates if the string must start with an alphabetical character.
     * @param boolean $startWithNumber Indicates if the string must start with a numeric character.
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _updatePatternErrorMessage ($validCharactersLabel, $startWithAlpha, $startWithNumber) {
      
      if ($this->errorType == VALIDATION_ERROR_PATTERN) {
        if ($startWithAlpha && $startWithNumber) {
          $msg = 'The string must start with a letter or a number. ';
          $msg .= 'The rest of the ';
        } elseif ($startWithAlpha) {
          $msg = 'The string must start with a letter. ';
          $msg .= 'The rest of the ';
        } elseif ($startWithNumber) {
          $msg = 'The string must start with a number. ';
          $msg .= 'The rest of the ';
        } else {
          $msg = 'The ';
        }
                    
        // update the error message
        $this->errorMessage .= ' '.$msg.$validCharactersLabel;
      }
      
    }  // end of function _updatePatternErrorMessage
    
    
    
    /**
     * Reset the validation and error message values
     * 
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _reset () {
      
      $this->validated = true;
      $this->errorMessage = '';
      
    }  // end of function _reset
    
    
    
    /**
     * Set the error message and the validation value
     * 
     * @param string $msg The error message to store.
     * @param string $genericFieldName The generic field name used in the error message.
     *                                 Replace it using the getErrorMessage() method.
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _setErrorMessage ($msg, $genericFieldName) {
      
      $this->errorMessage = $msg;
      $this->errorGenericFieldName = $genericFieldName;
      $this->validated = false;
      
    }  // end of function _setErrorMessage
    

    
    /**
     * Replace fancy characters with normal ones.
     * 
     * Applications like Microsoft Word have a tendency to use special "fancy"
     *   characters when there are standard characters that work just fine.
     *   This is true of things like single (') and double (") quotes which
     *   have an angle to indicate openning and closing quotes in Microsoft
     *   Word. These "fancy" characters are incredibly hard to validate, so this
     *   function is used to identify them and replace them with the "standard"
     *   characters.
     *   
     * @param string $text The text to check for fancy characters.
     * @return string The text provided with the fancy characters replaced with standard ones.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    function _deMicrosoft ($text) {
      
      // fix the special characters when submitted with Opera      
      $result = htmlspecialchars($text, ENT_NOQUOTES);

      $result = str_replace('&amp;#8208;', '-', $result);
      $result = str_replace('&amp;#8209;', '-', $result);
      $result = str_replace('&amp;#8210;', '-', $result);
      $result = str_replace('&amp;#8211;', '-', $result);
      $result = str_replace('&amp;#8212;', '-', $result);
      $result = str_replace('&amp;#8213;', '-', $result);

      $result = str_replace('&amp;#8216;', "'", $result);
      $result = str_replace('&amp;#8217;', "'", $result);
      $result = str_replace('&amp;#8218;', ",", $result);
      $result = str_replace('&amp;#8219;', "`", $result);
      
      $result = str_replace('&amp;#8220;', '"', $result);
      $result = str_replace('&amp;#8221;', '"', $result);
      $result = str_replace('&amp;#8223;', '"', $result);

      $result = str_replace('&amp;#8230;', '...', $result);
      
      $result = str_replace('&amp;#8242;', "'", $result);
      $result = str_replace('&amp;#8243;', '"', $result);
      $result = str_replace('&amp;#8245;', "`", $result);
      $result = str_replace('&amp;#8246;', '"', $result);

      $result = str_replace('&amp;#8259;', '-', $result);


      // fix the special characters when submitted with IE or Firefox
      $result = str_replace(chr(128), '', $result);
      $result = str_replace(chr(133), "...", $result);
      $result = str_replace(chr(145), "'", $result);
      $result = str_replace(chr(146), "'", $result);
      $result = str_replace(chr(147), '"', $result);
      $result = str_replace(chr(148), '"', $result);
      $result = str_replace(chr(149), '*', $result);
      $result = str_replace(chr(150), '-', $result);
      $result = str_replace(chr(151), '-', $result);
      $result = str_replace(chr(152), '~', $result);
      $result = str_replace(chr(153), '', $result);
      $result = str_replace(chr(160), ' ', $result);
      $result = str_replace(chr(226), "'", $result);
      for ($i = 161; $i < 256; $i++) {
        $result = str_replace(chr($i), '&#'.$i.';', $result);
      }
      
      return $result;
      
    }  // end of function _deMicrosoft

  }  // end of class Validate

?>