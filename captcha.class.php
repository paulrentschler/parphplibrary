<?php
  /**
   * Singleton object to generate math CAPTCHAs
   *   (Completely Automated Public Turing tests 
   *   to tell Computers and Humans Apart) to
   *   prevent automated spam form submissions
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 21 January 2007
   */
  class Captcha {

    /**
     * The question to be asked.
     */
    protected static $question = '';
    
    /**
     * The answer to the generated question.
     */
    protected static $answer = '';
    
    /**
     * The smallest number that can be used in the math question.
     */
    protected static $minValue = 1;
    
    /**
     * The largest number that can be used in the math question.
     */
    protected static $maxValue = 20;
    
    /**
     * An array of math operators that can be used in the question.
     */
    protected static $operators = array('+', '-');
    
    
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __construct() {} 
    
    /**
     * Prevent anyone from creating an instance of this.
     */
    private function __clone() {} 



    /**
     * Set the limiting factors for generating the question.
     * 
     * @param integer $minValue The smallest number to be used in the question.
     * @param integer $maxValue The largest number to be used in the question.
     * @param array $operators An array of math operators to be used in the question.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function setLimits ($minValue, $maxValue, $operators) {
      
      // check and set the min value
      if (is_numeric($minValue) && $minValue > 0) {
        $this->minValue = $minValue;
      }
      
      // check and set the max value
      if (is_numeric($maxValue) && $maxValue > $this->minValue) {
        $this->maxValue = $maxValue;
      }
      
      // check and set the operators
      if (is_array($operators) && count($operators) > 0) {
        $this->operators = $operators;
      }
      
    }  // end of function setLimits



    /**
     * Generate a math question based on the limits and return it.
     * 
     * @return string The generated math question to be asked.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function getQuestion () {
      
      // get all the parts of the equation
      $firstNum = mt_rand(self::$minValue, self::$maxValue);
      $secondNum = mt_rand(self::$minValue, self::$maxValue);
      $operator = self::$operators[array_rand(self::$operators)];
      
      // generate the question and answer
      switch ($operator) {
        case '-':
          // don't make people deal with negative number answers
          if ($secondNum > $firstNum) {
            self::$question = $secondNum.' minus '.$firstNum;
            self::$answer = $secondNum - $firstNum;
          } else {
            self::$question = $firstNum.' minus '.$secondNum;
            self::$answer = $firstNum - $secondNum;
          }
          break;
          
        case '+':
        default:
          self::$question = $firstNum.' plus '.$secondNum;
          self::$answer = $firstNum + $secondNum;
          break;
      }
      
      // stick the answer in a session variable for later retrieval
      $_SESSION['ksht'] = self::$answer;
      
      return self::$question;
      
    }  // end of function getQuestion
    
    
    
    /**
     * Determine if the answer the user provided is correct.
     * 
     * @param integer $userAnswer The answer that the user provided.
     * @return boolean Whether or not the user's answer was correct.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public static function checkAnswer ($userAnswer) {

      // assume the worst
      $result = false;
      
      // check the provided answer
      if (isset($_SESSION['ksht']) && ((int) $userAnswer) === ((int) $_SESSION['ksht'])) {
        // the captcha is valid
        $result = true;
        unset($_SESSION['ksht']);
      }
      
      return $result;
      
    }  // end of function checkAnswer
    
  }  // end of class Captcha

  
  
  
  /**
   * A widget for displaying a CAPTCHA and collecting the
   *   response from a user as part of an HTML form.
   * 
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 6 June 2011
   */
  class CaptchaWidget extends Widget {
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * @param string $name The name assigned to the widget's input element.
     * @param string $label The label text to be displayed with the widget's input element.
     * @param string $description The descriptive text to be displayed along with the label (optional).
     * @param boolean $required Whether or not this input element must be provided (optional).
     * @param integer $tabIndex An integer representing the order of elements when the tab key is pressed (optional).
     * @param array $options An associative array of additional widget-specific options (optional).
     * @author Paul Rentschler <paul@rentschler.ws>  
     */
    public function __construct ($name, $label, $description='', $required=false, $tabIndex=-1, $options=array()) {
      
      // call the parent constructor
      parent::__construct($name, $label, $description, $required, $tabIndex, $options);

      // set the name of the widget
      $this->widgetName = 'FrameworkCaptchaWidget';
      
    }  // end of function __construct
    
    
    
    public function render ($specificTagHtml = '') {
      
      // define the input tag attributes
      $attrs = array( 'type' => 'text',
                      'name' => $this->name,
                      'id' => $this->name,
                      'value' => $this->value,
                      'size' => 10,
                      'maxlength' => 10 );
      if ($this->tabIndex <> '' and is_numeric($this->tabIndex) && $this->tabIndex >= 0) {
        $attrs['tabindex'] = $this->tabIndex;
      }
      
      // build the HTML
      $html = HTML::renderTag('div', array( 'class' => 'ksht-question' ), false, false, 1);
      $html .= 'What is '.Captcha::getQuestion().'?</div>'."\n";
      $html .= HTML::renderTag('input', $attrs, true, true, 1);
      $html = parent::render( $html );

                      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
      
  }  // end of class CaptchaWiget
