<?php
  require_once('html.class.php');

  /**
   * A base widget for collecting input from a user as part
   *   of an HTML form.
   * 
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class Widget {

    /**
     * The name of the widget.
     */
    protected $widgetName = 'FrameworkWidget';
    
    /**
     * The name of the form item that this specific widget is generating.
     */
    protected $name = '';
    
    /**
     * The label that should be displayed for this input item.
     */
    protected $label = '';
    
    /**
     * A short, optional description that goes with the input item label.
     */
    protected $description = '';
    
    /**
     * An indicator as to whether or not this input item must be entered.
     */
    protected $required = false;
    
    /**
     * The tab order for this input item.
     */
    protected $tabIndex = -1;
    
    /**
     * The value of the input item.
     */ 
    protected $value = '';
    
    /**
     * Whether or not the widget is disabled (no input allowed).
     */
    protected $disabled = false;
    
    /**
     * The error text that should be displayed with this input item.
     */
    protected $errorText = '';
    
    /**
     * Indicate that the widget should be rendered as a fieldset because
     *   there are multiple input objects within the widget. This is
     *   typical of a selection widget with individual check boxes.
     */
    protected $widgetIsFieldset = false;
    
    /**
     * Indicate that the label tag should be rendered as a div tag.
     */
    protected $renderLabelAsDiv = false;
    
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     disabled = true/false
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
      
      // set the properties based on the provided values
      $this->name = $name;
      $this->label = $label;
      $this->description = $description;
      $this->required = $required;
      $this->tabIndex = $tabIndex;
      
      // If $options contains a "value" entry, we will call setValue.
      if (isset($options['value'])) {
        $this->setValue($options['value']);
      }
      
      // if $options contains a "disabled" entry, we will call disable.
      if (isset($options['disabled']) && is_bool($options['disabled'])) {
        $this->disable($options['value']);
      }
      
    }  // end of function __construct
    
        
    
    /**
     * Build the HTML for the widget and assign it to the htmlCode property.
     * 
     * Each subclassed widget will need to define the $specificTagHtml value
     *   appropriately for the type of HTML input that is being generated and
     *   then call this parent function to put everything together.
     * 
     * @param string $specificTagHtml The HTML code needed to generate a specific input element.
     * @return string The HTML code necessary to display this widget.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function render ($specificTagHtml = '') {
      
      // start with a carriage return so that all the indents will be correct
      $html = "\n";
      
      // determine the type of tag
      $tag = 'div';
      if ($this->widgetIsFieldset) {
        $tag = 'fieldset';
      }
      
      // define the main tag attributes
      $classes = array( 'field', $this->widgetName );
      if ($this->errorText <> '') {
        $classes[] = 'error';
      }
      if ($this->disabled) {
        $classes[] = 'disabled';
      }
      $attrs = array( 'class' => implode(' ', $classes),
                      'id' => 'framework-fieldname-'.$this->name );
      
      // build the HTML
      $html .= HTML::renderTag($tag, $attrs, false, false, 0)."\n";
      $html .= $this->_renderLabel()."\n";
      $html .= $this->_renderDescription()."\n";
      $html .= $this->_renderErrorText()."\n";
      $html .= $specificTagHtml."\n";  // no carriage return needed here
      
      // close the main tag
      $html .= '</'.$tag.">\n";
      
      // return the HTML code
      return $html;
            
    }  // end of function render
    
    
    
    /**
     * Get the name of the widget.
     * 
     * @return string The name of the widget.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getWidgetName () {
      
      return $this->widgetName;
      
    }  // end of function getWidgetName
    
    
    
    /**
     * Get the name of the form item's name.
     * 
     * @return string The name of the form item being rendered.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getName () {
      
      return $this->name;
      
    }  // end of function getName
    
    
    
    /**
     * Get the widget's label field.
     * 
     * @return string The widget's label field.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getLabel () {
      
      return $this->label;
      
    }  // end of function getLabel
    
    
    
    /**
     * Get the widget's description field.
     * 
     * @return string The widget's description field.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getDescription () {
      
      return $this->description;
      
    }  // end of function getDescription
    
    
    
    /**
     * Get the value of this widget's input element.
     * 
     * @return various The value of the widget's input element.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getValue () {
      
      return $this->value;
      
    }  // end of function getValue
    
    
    
    /**
     * Set the value of this widget's input element.
     * 
     * @param integer|string $value The value of this widget's input element.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setValue ($value) {
      
      $this->value = $value;
      
    }  // end of function setValue
    
    
    
    /**
     * Set the error message that is associated with this widget's input element.
     * 
     * @param string $text The error message to be displayed.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setErrorText ($text) {
      
      $this->errorText = $text;
      
    }  // end of function setErrorText
    
    
    
    /**
     * Set the disabled indicator which determines whether or not the widget's 
     *   input element will accept user input.
     * 
     * @param boolean $disabled Whether or not to disable the widget. Default is true.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function disable ($disabled = true) {
      
      if (is_bool($disabled)) {
        $this->disabled = $disabled;
      }
      
    }  // end of function disable
    
    
    
    /**
     * Generate the HTML to display the label element.
     * 
     * @return string The HTML to display the label element.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderLabel () {
      
      // determine what type of HTML tag to use
      $tag = 'label';
      if ($this->widgetIsFieldset) {
        $tag = 'legend';
      } elseif ($this->renderLabelAsDiv) {
        $tag = 'div';
      }
      
      // define the tag attributes
      $attrs = array( 'class' => 'formLabel' );
      if (!$this->widgetIsFieldset) {
        $attrs['for'] = $this->name;
      }
      
      // build the HTML
      $html = HTML::renderTag($tag, $attrs, false, false, 1);
      $html .= $this->label;
      if ($this->widgetIsFieldset && $this->required) {
        $html .= ' '.$this->_renderRequired();
      }
      $html .= '</'.$tag.'>';
      if (!$this->widgetIsFieldset && $this->required) {
        $html .= "\n".HTML::indent($this->_renderRequired(), 1);
      }
      
      // return the resulting HTML
      return $html;
      
    }  // end of function _renderLabel
    
    
    
    /**
     * Generate the HTML to display the description that helps
     *   explain or elaborate on the label element.
     *   
     * @return string The HTML to display the description.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderDescription () {
      
      // define the tag attributes
      $attrs = array( 'class' => 'formDescription',
                      'id' => $this->name.'-description' );
      
      // build the HTML
      $html = HTML::renderTag('div', $attrs, false, false, 1);
      $html .= $this->description.'</div>';
      
      // return the resulting HTML
      return $html;
      
    }  // end of function _renderDescription
    
    
    
    /**
     * Generate the HTML to dipslay the required indicator.
     * 
     * @return string The HTML to display the required indicator.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderRequired () {
      
      // define the tag attributes
      $attrs = array( 'class' => 'fieldRequired',
                      'title' => 'Required' );
      
      // build the HTML
      $html = HTML::renderTag('span', $attrs, false, false, 0);
      $html .= '(Required)</span>';
      
      // return the resulting HTML
      return $html;
      
    }  // end of function _renderRequired
    
    
    
    /**
     * Generate the HTML to display the error associated with this
     *   input widget.
     *   
     * @return string The HTML to display the error message.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderErrorText () {
      
      // define the tag attributes
      $attrs = array( 'class' => 'fieldErrorBox' );
      
      // build the HTML
      $html = HTML::renderTag('div', $attrs, false, false, 1);
      $html .= $this->errorText.'</div>';
      
      // return the resulting HTML
      return $html;
      
    }  // end of function _renderErrorText
   
  }  // end of class Widget
  
  
  
  
  /**
   * A widget for including hidden data as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 6 July 2011
   */
  class HiddenWidget extends Widget {
    
    /**
     * The id attribute for the input tag.
     */
    protected $id = '';
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     id = the id attribute for the input tag (auto-generated from the name if not specified)
     *     
     * @param string $name The name assigned to the widget's input element.
     * @param string $label The label text to be displayed with the widget's input element (ignored).
     * @param string $description The descriptive text to be displayed along with the label (ignored).
     * @param boolean $required Whether or not this input element must be provided (ignored).
     * @param integer $tabIndex An integer representing the order of elements when the tab key is pressed (ignored).
     * @param array $options An associative array of additional widget-specific options (optional).
     * @author Paul Rentschler <paul@rentschler.ws>  
     */
    public function __construct ($name, $label, $description='', $required=false, $tabIndex=-1, $options=array()) {
      
      // label, description, required, and tabIndex all have no meaning, so they will be blanked
      $label = '';
      $description = '';
      $required = false;
      $tabIndex = -1;
      
      // call the parent constructor
      parent::__construct($name, $label, $description, $required, $tabIndex, $options);

      // set the name of the widget
      $this->widgetName = 'FrameworkHiddenWidget';
      
      // set the remaining values
      if (isset($options['id']) && is_string($options['id']) && $options['id'] <> '') {
        $this->id = $options['id'];
      }
      
    }  // end of function __construct
    
    
    
    public function render ($specificTagHtml = '') {
      
      // determine what the id attribute should be
      if ($this->id == '') {
        $this->id = $this->name;
      }
      $this->id = str_replace('[', '', str_replace(']', '', $this->id));
      
      // define the input tag attributes
      $attrs = array( 'type' => 'hidden',
                      'name' => $this->name,
                      'id' => $this->id,
                      'value' => $this->value );
      
      // build the HTML
      $html = HTML::renderTag('input', $attrs, true, false, 0)."\n";

                      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
    
  }  // end of class HiddenWidget


  
  
  /**
   * A widget for displaying any HTML as part of a form.
   *   
   * @author Paul Rentschler <par117@psu.edu>
   * @since 18 July 2011
   */
  class HTMLWidget extends Widget {
    
    /**
     * The id attribute for widget tag.
     */
    protected $id = '';
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = the HTML content to display
     *     id = the id attribute for the widget tag
     *     
     * @param string $name The name assigned to the widget's input element.
     * @param string $label The label text to be displayed with the widget's input element (ignored if blank).
     * @param string $description The descriptive text to be displayed along with the label (ignored if blank).
     * @param boolean $required Whether or not this input element must be provided (ignored).
     * @param integer $tabIndex An integer representing the order of elements when the tab key is pressed (ignored).
     * @param array $options An associative array of additional widget-specific options (optional).
     * @author Paul Rentschler <par117@psu.edu>  
     */
    public function __construct ($name, $label, $description='', $required=false, $tabIndex=-1, $options=array()) {
      
      // required and tabIndex have no meaning, so they will be blanked
      $required = false;
      $tabIndex = -1;
      
      // call the parent constructor
      parent::__construct($name, $label, $description, $required, $tabIndex, $options);

      // set the name of the widget
      $this->widgetName = 'FrameworkHTMLWidget';

      // set the remaining values
      if (isset($options['id']) && is_string($options['id']) && $options['id'] <> '') {
        $this->id = $options['id'];
      }
      
    }  // end of function __construct
    
    
    
    public function render ($specificTagHtml = '') {
      
      // start with a carriage return so that all the indents will be correct
      $html = "\n";
      
      // indicate that the label should be rendered as a div
      $this->renderLabelAsDiv = true;
      
      // determine what id will be used
      $id = $this->id;
      if ($id == '') {
        $id = $this->name;
      }
      
      // define the main tag attributes
      $classes = array( 'field', $this->widgetName );
      if ($this->errorText <> '') {
        $classes[] = 'error';
      }
      $attrs = array( 'class' => implode(' ', $classes),
                      'id' => 'framework-fieldname-'.$id );
      
      // build the HTML
      $html .= HTML::renderTag('div', $attrs, false, false, 0)."\n";
      if ($this->label <> '') {
        $html .= $this->_renderLabel()."\n";
        $html .= $this->_renderDescription()."\n";
      }
      $html .= $this->_renderErrorText()."\n";
      $html .= HTML::indent($this->value, 1)."\n";
      
      // close the main tag
      $html .= "</div>\n";
      
      // return the HTML code
      return $html;
      
    }  // end of function render
    
  }  // end of class HTMLWidget
  
  
  
  /**
   * A widget for displaying a paragraph of text as part of a form.
   *   
   * @author Paul Rentschler <par117@psu.edu>
   * @since 18 July 2011
   */
  class ParagraphWidget extends HTMLWidget {
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     text = the paragraph text to display
     *     id = the id attribute for the widget tag
     *     
     * @param string $name The name assigned to the widget's input element.
     * @param string $label The label text to be displayed with the widget's input element.
     * @param string $description The descriptive text to be displayed along with the label.
     * @param boolean $required Whether or not this input element must be provided (ignored).
     * @param integer $tabIndex An integer representing the order of elements when the tab key is pressed (ignored).
     * @param array $options An associative array of additional widget-specific options (optional).
     * @author Paul Rentschler <par117@psu.edu>  
     */
    public function __construct ($name, $label, $description='', $required=false, $tabIndex=-1, $options=array()) {
      
      // required and tabIndex have no meaning, so they will be blanked
      $required = false;
      $tabIndex = -1;
      
      // call the parent constructor
      parent::__construct($name, $label, $description, $required, $tabIndex, $options);

      // set the name of the widget
      $this->widgetName = 'FrameworkParagraphWidget';

      // set the remaining values
      if (isset($options['id']) && is_string($options['id']) && $options['id'] <> '') {
        $this->id = $options['id'];
      }
      if (isset($options['text']) && is_string($options['text']) && $options['text'] <> '') {
        $this->value = '<p>'.str_replace("\n", '</p><p>', $options['text']).'</p>';
      }
      
    }  // end of function __construct
      
  }  // end of class ParagraphWidget
  
  
  
  
  /**
   * A widget for collecting fixed-length string input 
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class StringWidget extends Widget {
    
    /**
     * The size of the text box.
     */
    protected $size = 30;
    
    /**
     * The maximum length of the string that can be entered in the text box.
     */
    protected $maxLength = 255;
    
    /**
     * The type of input tag that will be rendered.
     * Used so that the PasswordWidget can inherit from this one.
     */
    protected $inputType = 'text';
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     size = integer
     *     maxlength = integer
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
      $this->widgetName = 'FrameworkStringWidget';
      
      // set the remaining values
      if (isset($options['size']) && is_numeric($options['size']) && $options['size'] > 0) {
        $this->size = $options['size'];
      }
      if (isset($options['maxlength']) && is_numeric($options['maxlength']) && $options['maxlength'] > 0) {
        $this->maxLength = $options['maxlength'];
      }
      
    }  // end of function __construct
    
    
    
    public function render ($specificTagHtml = '') {
      
      // define the input tag attributes
      $attrs = array( 'type' => $this->inputType,
                      'name' => $this->name,
                      'id' => $this->name,
                      'value' => $this->value,
                      'size' => $this->size,
                      'maxlength' => $this->maxLength );
      if ($this->tabIndex <> '' and is_numeric($this->tabIndex) && $this->tabIndex >= 0) {
        $attrs['tabindex'] = $this->tabIndex;
      }
      if ($this->disabled) {
        $attrs['disabled'] = 'disabled';
      }

      // build the input HTML
      $html = HTML::renderTag('input', $attrs, true, true, 1);
      
      // if the widget is disabled, render a hidden input that also contains the value so it's submitted back to the server
      if ($this->disabled) {
        $attrs = array( 'type' => 'hidden',
                        'name' => $this->name,
                        'value' => $this->value );
        $html .= "\n".HTML::renderTag('input', $attrs, true, false, 1);
      }
      
      // build the widget HTML
      $html = parent::render($html);

                      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
    
  }  // end of class StringWidget
    

  
  
  /**
   * A widget for collecting fixed-length string password
   *   input from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class PasswordWidget extends StringWidget {
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     size = integer
     *     maxlength = integer
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
                                  
      // override the input type and widget name
      $this->inputType = 'password';
      $this->widgetName = 'FrameworkPasswordWidget';
      
    }  // end of function __construct
    
  }  // end of class PasswordWidget
    

  
  
  /**
   * A widget for collecting multi-line string input 
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class TextAreaWidget extends Widget {
    
    /**
     * The number of rows of text to display (height of text box).
     */
    protected $rows = 5;
    
    /**
     * The number of columns of text to display (width of text box).
     */
    protected $cols = 40;
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     rows = integer
     *     cols = integer
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
      $this->widgetName = 'FrameworkTextAreaWidget';
            
      // set the remaining values
      if (isset($options['rows']) && is_numeric($options['rows']) && $options['rows'] > 0) {
        $this->rows = $options['rows'];
      }
      if (isset($options['cols']) && is_numeric($options['cols']) && $options['cols'] > 0) {
        $this->cols = $options['cols'];
      }
      
    }  // end of function __construct
    
    
    
    public function render ($specificTagHtml = '') {
      
      // define the textarea tag attributes
      $attrs = array( 'name' => $this->name,
                      'id' => $this->name,
                      'rows' => $this->rows,
                      'cols' => $this->cols );
      if ($this->tabIndex <> '' and is_numeric($this->tabIndex) && $this->tabIndex >= 0) {
        $attrs['tabindex'] = $this->tabIndex;
      }
      if ($this->disabled) {
        $attrs['disabled'] = 'disabled';
      }
      
      // build the textarea HTML
      $html = HTML::renderTag('textarea', $attrs, false, true, 1).$this->value.'</textarea>';
      
      // if the widget is disabled, render a hidden input that also contains the value so it's submitted back to the server
      if ($this->disabled) {
        $attrs = array( 'type' => 'hidden',
                        'name' => $this->name,
                        'value' => $this->value );
        $html .= "\n".HTML::renderTag('input', $attrs, true, false, 1);
      }
      
      // build the widget HTML
      $html = parent::render($html);
      
      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
    
  }  // end of class TextAreaWidget
    

  
  
  /**
   * A widget for collecting date and time input 
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class DateTimeWidget extends Widget {
    
    /**
     * The month that is currently selected.
     */
    protected $month = 0;
    
    /**
     * The day of the month that is currently selected.
     */
    protected $day = 0;
    
    /**
     * The year that is currently selected.
     */
    protected $year = 0;
    
    /** 
     * The oldest year to show in the year drop-down.
     */
    protected $minYear = 2000;
    
    /**
     * The newest year to show in the year drop-down.
     */
    protected $maxYear = 2100;
    
    /**
     * The hour that is currently selected.
     */
    protected $hour = 0;
    
    /**
     * The month that is currently selected.
     */
    protected $minute = -1;
    
    /**
     * The meridiem that is currently selected.
     */
    protected $ampm = '';
    
    /**
     * Whether or not to use 24-hour time.
     */
    protected $twentyFourHour = false;
    
    /**
     * Whether or not to include the date in the widget.
     */
    protected $includeDate = true;
    
    /**
     * Whether or not to include the time in the widget.
     */
    protected $includeTime = true;
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     minyear = integer
     *     maxyear = integer
     *     twentyfourhour = boolean
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
      $this->widgetName = 'FrameworkDateTimeWidget';
                  
      // set the remaining values
      if (isset($options['minyear']) && is_numeric($options['minyear']) && $options['minyear'] > 0) {
        $this->minYear = $options['minyear'];
      }
      if (isset($options['maxyear']) && is_numeric($options['maxyear']) && $options['maxyear'] > 0) {
        $this->maxYear = $options['maxyear'];
      }
      if (isset($options['twentyfourhour']) && is_bool($options['twentyfourhour'])) {
        $this->twentyFourHour = $options['twentyfourhour'];
      }
      
    }  // end of function __construct
    
    
    
    /**
     * Set the value of this widget's input element by breaking the
     *   date/time value into it's components.
     * 
     * @param string $value The date/time string value of this widget's input element.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setValue ($value) {
      
      // convert the value to individual date/time values
      if ($value) {
        if (is_string($value) || is_numeric($value)) {
          // if a string was given, make it into a timestamp and carefully extract values
          if (is_numeric($value)) {
            $timestamp = $value;
          } else {
            // make sure that a single zero minute is changed to a double 0 cause strtotime doesn't work otherwise.
            $value = str_replace(':0 ', ':00 ', $value);
            $value = str_replace(':0a', ':00a', $value);
            $value = str_replace(':0p', ':00p', $value);
  
            // convert the value to a timestamp
            $timestamp = strtotime($value);
          }
          
          if ($timestamp > 0) {
            // break out the timestamp into it's components and store them
            $this->month = date('n', $timestamp);
            $this->day = date('j', $timestamp);
            $this->year = date('Y', $timestamp);
            $this->minute = $this->_roundToFiveMinIncrement((int) date('i', $timestamp));
            
            // handle 24 hour time or not
            if ($this->twentyFourHour) {
              $this->hour = date('G', $timestamp);
              $this->ampm = '';
            } else {
              $this->hour = date('g', $timestamp);
              $this->ampm = date('a', $timestamp);
            }
          }
        
        // if an array was given, use its values.
        } elseif (is_array($value)) {
          $keys = array('month', 'day', 'year', 'ampm', 'hour', 'minute');
          foreach($keys as $key) {
            if (isset($value[$key])) {
              $this->$key = $value[$key];
            }
          }
        }
      }
      
    }  // end of function setValue
    
    
    
    public function render ($specificTagHtml = '') {
      
      // start the HTML code for the date/time selectors
      $html = HTML::renderTag('div', array( 'class' => 'datetime-selectors' ), false, false, 1)."\n";
      
      // include the date selectors
      if ($this->includeDate) {
        $html .= $this->_renderDateSelectors(2);
      }
      
      // provide a spacer between the date and time selectors
      if ($this->includeDate && $this->includeTime) {
        $html .= HTML::indent('<span class="datetime-spacer">&nbsp;</span>', 2);
      }
      
      // include the time selectors
      if ($this->includeTime) {
        $html .= $this->_renderTimeSelectors(2);
      }
      
      // close the HTML code for the date/time selectors
      $html .= HTML::indent('</div>', 1);
      
      
      // if the widget is disabled, render a hidden input that also contains the value so it's submitted back to the server
      if ($this->disabled) {
        // determine which fields we need to render hidden inputs for
        $fieldsToRender = array();
        if ($this->includeDate) {
          $fieldsToRender['year'] = $this->year;
          $fieldsToRender['month'] = $this->month;
          $fieldsToRender['day'] = $this->day;
        }
        if ($this->includeTime) {
          $fieldsToRender['hour'] = $this->hour;
          $fieldsToRender['minute'] = $this->minute;
          $fieldsToRender['ampm'] = $this->ampm;
        }
        
        // render the appropriate hidden fields
        foreach ($fieldsToRender as $field => $value) {
          $attrs = array( 'type' => 'hidden',
                          'name' => $this->name.'-'.$field,
                          'value' => $value );
          $html .= "\n".HTML::renderTag('input', $attrs, true, false, 1);
        }
      }

      
      // build the widget HTML
      $html = parent::render($html);
      
      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
    
    
    
    /**
     * Generate the HTML to display the date dropdown selectors.
     * 
     * @param integer $indentLevel The level at which these tags should be indented.
     * @return string The HTML to display dropdown selectors for month, day, and year.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderDateSelectors ($indentLevel) {
      
      // build an array of month number = month name
      $monthNames = explode(',', 'January,February,March,April,May,June,July,August,September,October,November,December');
      $months = array();
      $i = 1;
      foreach ($monthNames as $monthName) {
        $months[$i] = $monthName;
        $i++;
      }
      unset($monthNames);
      
      // define the dropdown selectors that need to be created
      $selectors = array( array( 'name' => $this->name.'-year',
                                 'optionStart' => $this->minYear,
                                 'optionEnd' => $this->maxYear,
                                 'selectedValue' => $this->year,
                                 'separator' => '/' ),
                          array( 'name' => $this->name.'-month',
                                 'optionValues' => $months,
                                 'selectedValue' => $this->month,
                                 'separator' => '/' ),
                          array( 'name' => $this->name.'-day',
                                 'optionStart' => 1,
                                 'optionEnd' => 31,
                                 'selectedValue' => $this->day,
                                 'separator' => '' )
                        );

                        
      // create the dropdown selectors and return the resulting HTML
      return $this->_renderDropDowns($selectors, $indentLevel);
      
    }  // end of function _renderDateSelectors
    
    
    
    /**
     * Generate the HTML to display the time dropdown selectors.
     * 
     * @param integer $indentLevel The level at which these tags should be indented.
     * @return string The HTML to display dropdown selectors for hour, minute, and meridiem.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderTimeSelectors ($indentLevel) {
      
      // define the dropdown selectors that need to be created
      if ($this->twentyFourHour) {
        $selectors = array( array( 'name' => $this->name.'-hour',
                                   'optionStart' => 0,
                                   'optionEnd' => 23,
                                   'selectedValue' => $this->hour,
                                   'separator' => ':' ) );
      } else {
        $hours = array( '12' => '12' );
        for ($i = 1; $i < 12; $i++) {
          $hours[$i] = $i;
        }
        $selectors = array( array( 'name' => $this->name.'-hour',
                                   'optionValues' => $hours,
                                   'selectedValue' => $this->hour,
                                   'separator' => ':' ) );
      }
      
      $minutes = array( '0' => '00', '5' => '05' );
      for ($i = 10; $i < 60; $i += 5) {
        $minutes[$i] = $i;
      }
      $selectors[] = array( 'name' => $this->name.'-minute',
                            'optionValues' => $minutes,
                            'selectedValue' => $this->minute,
                            'separator' => '' );
      
      if (!$this->twentyFourHour) {
        $ampms = array( 'am' => 'AM', 'pm' => 'PM' );
        $selectors[] = array( 'name' => $this->name.'-ampm',
                              'optionValues' => $ampms,
                              'selectedValue' => $this->ampm,
                              'separator' => '' );
      }

                        
      // create the dropdown selectors and return the resulting HTML
      return $this->_renderDropDowns($selectors, $indentLevel);
            
    }  // end of function _renderTimeSelectors
    
    
    
    /**
     * Round the $minutes value to the closest and lowest 5 minute increment.
     * 
     * @param integer $minutes The number of minutes in the timestamp. Ranges from 0 to 59.
     * @return integer The number of minutes rounded down to the nearest 5 minute increment.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _roundToFiveMinIncrement ($minutes) {
      
      $roundedMinutes = ((int) ($minutes / 5)) * 5;
      if ($roundedMinutes > 60) {
        $roundedMinutes = 59;
      }
      
      return $roundedMinutes;
      
    }  // end of function _roundToFiveMinIncrement

    
    
    /**
     * Generate the HTML to display a series of dropdown box.
     * 
     * Values for each associative array in $dropdowns:
     *     name = the name of this dropdown HTML element
     *     optionStart = the numeric value to start the list of options with
     *     optionEnd = the numeric value to stop the list of options with
     *     optionValues = an associative array of value/label pairs to be used as the options
     *     selectedValue = the value that should be currently selected
     *     separator = the text that should be added after the dropdown box
     * 
     * @param array $dropdowns An array of associative arrays that provide the values necessary to generate each dropdown box.
     * @param integer $indentLevel The level at which these tags should be indented.
     * @return string The HTML to display a series of dropdown boxes.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _renderDropDowns ($dropdowns, $indentLevel=0) {
      
      // write the HTML for the drop downs specified in $dropdowns
      $html = '';
      foreach ($dropdowns as $dropdown) {               
        // span tag
        $html .= HTML::indent('<span>', $indentLevel)."\n";
        
        // select tag
        $attrs = array( 'name' => $dropdown['name'],
                        'id' => $dropdown['name'],
                        'size' => 1 );
        if ($this->disabled) {
          $attrs['disabled'] = 'disabled';
        }
        $html .= HTML::renderTag('select', $attrs, false, true, $indentLevel + 1)."\n";
        
        // option tags
        $options = '';
        $valueSelected = false;
        if (isset($dropdown['optionStart']) && isset($dropdown['optionEnd']) && 
            is_numeric($dropdown['optionStart']) && is_numeric($dropdown['optionEnd']) &&
            $dropdown['optionStart'] < $dropdown['optionEnd']) {
          for ($i = $dropdown['optionStart']; $i <= $dropdown['optionEnd']; $i++) {
            $attrs = array( 'value' => $i );
            if ($i == $dropdown['selectedValue']) {
              $attrs['selected'] = 'selected';
              $valueSelected = true;
            }
            $options .= HTML::renderTag('option', $attrs, false, false, $indentLevel + 2).$i."</option>\n";
          }
          
        } elseif (isset($dropdown['optionValues'])) {
          foreach ($dropdown['optionValues'] as $value => $label) {
            $attrs = array( 'value' => $value );
            if ($value == $dropdown['selectedValue']) {
              $attrs['selected'] = 'selected';
              $valueSelected = true;
            }
            $options .= HTML::renderTag('option', $attrs, false, false, $indentLevel + 2).$label."</option>\n";
          }
        }

        // add the default option first
        $attrs = array( 'value' => '' );
        if (!$valueSelected) {
          $attrs['selected'] = 'selected';
        }
        $html .= HTML::renderTag('option', $attrs, false, false, $indentLevel + 2)."--</option>\n";
        $html .= $options;
        
        // end select tag
        $html .= HTML::indent('</select>', ($indentLevel + 1))."\n";
        
        // end span tag
        $html .= HTML::indent('</span>', $indentLevel)."\n";
        
        
        // separator
        if ($dropdown['separator'] <> '') {
          $html .= HTML::indent('<span>'.$dropdown['separator'].'</span>', $indentLevel)."\n";
        }
      }
      
      
      // return the HTML
      return $html;
      
    }  // end of function _renderDropDowns
    
  }  // end of class DateTimeWidget


  
  
  /**
   * A widget for collecting date input 
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class DateWidget extends DateTimeWidget {
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     minyear = integer
     *     maxyear = integer
     *     twentyfourhour = boolean
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
      $this->widgetName = 'FrameworkDateWidget';
                  
      // configure the widget to only do the date component
      $this->includeDate = true;
      $this->includeTime = false;
      
    }  // end of function __construct
  
  }  // end of class DateWidget
  
  
  
  
  /**
   * A widget for collecting time input 
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class TimeWidget extends DateTimeWidget {

    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     minyear = integer
     *     maxyear = integer
     *     twentyfourhour = boolean
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
      $this->widgetName = 'FrameworkTimeWidget';
                  
      // configure the widget to only do the time component
      $this->includeDate = false;
      $this->includeTime = true;
      
    }  // end of function __construct
  
  }  // end of class TimeWidget

  
  
  
  /**
   * A widget for collecting a choice
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class SelectionWidget extends Widget {
    
    /**
     * The choices that are presented to be selected.
     */
    protected $choices = array();
    
    /**
     * The format of this selection widget.
     * Options are: flex, list, or individual.
     */
    protected $format = 'flex';
    
    /**
     * Allow more than one choice to be selected at one time.
     */
    protected $multiSelect = false;
    
    /**
     * What value will be used for the default entry that is inserted.
     */
    protected $defaultValue = '';
    
    /**
     * What label will be used for the default entry that is inserted.
     *   If this is blank, no default entry will be created.
     */
    protected $defaultLabel = '--';
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     choices = an associative array of value/label pairs.
     *     format = string (either flex, list, or individual).
     *     defaultValue = various, the value assigned to the default option in a list.
     *     defualtLabel = string, the label for the default option in a list (if this is blank, no default option will be included).
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
      $this->widgetName = 'FrameworkSelectionWidget';
                  
      // set the remaining values
      if (isset($options['choices']) && is_array($options['choices'])) {
        $this->choices = $options['choices'];
      }
      if (isset($options['format']) && is_string($options['format'])) {
        $this->setFormat($options['format']);
      } else {
        $this->setFormat($this->format);
      }
      if (isset($options['defaultValue'])) {
        $this->defaultValue = $options['defaultValue'];
      }
      if (isset($options['defaultLabel']) && is_string($options['defaultLabel'])) {
        $this->defaultLabel = $options['defaultLabel'];
      }
        
    }  // end of function __construct
    
    
    
    /**
     * Set the format for the selection widget.
     * 
     * Options are:
     *     flex - determined based on the number of choices
     *     list - displayed in a list box
     *     individual - displayed as radio / checkboxes
     * 
     * @param string $format The format to be used for this selection widget.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setFormat ($format) {
      
      // validate the provided format
      $validFormats = array( 'flex', 'list', 'individual' );
      if (in_array(strtolower($format), $validFormats)) {
        $this->format = strtolower($format);
      } else {
        $this->format = 'flex';
      }
        
      // determine the format
      if ($this->format == 'flex') {
        if (count($this->choices) > 5) {
          $this->format = 'list';
        } else {
          $this->format = 'individual';
        }
      }
      
      // indicate if the widget should be rendered as a fieldset
      $this->widgetIsFieldset = false;
      if ($this->format == 'individual') {
        $this->widgetIsFieldset = true;
      }
      
    }  // end of function setFormat
    
    
    
    public function render ($specificTagHtml = '') {
      
      $html = '';
      
      // determine the input tag's name based on whether we are allowing multiple selections
      $inputName = $this->name;
      if ($this->multiSelect) {
        $inputName .= '[]';
      }
      
      // set the base indent level
      $indentLevel = 1;
      
      
      /************************************************************************
       ********************** LIST/DROPDOWN BOX OUTPUT ************************
       ************************************************************************/
      if ($this->format == 'list') {
        // define the select tag attributes
        $attrs = array( 'name' => $inputName,
                        'id' => $this->name );
        if ($this->tabIndex <> '' and is_numeric($this->tabIndex) && $this->tabIndex >= 0) {
          $attrs['tabindex'] = $this->tabIndex;
        }
        if ($this->disabled) {
          $attrs['disabled'] = 'disabled';
        }
        if ($this->multiSelect) {
          $attrs['multiple'] = 'multiple';
          if (count($this->choices) > 10) {
            $attrs['size'] = 10;
          } else {
            $attrs['size'] = count($this->choices);
          }
        }
        $html .= HTML::renderTag('select', $attrs, false, true, $indentLevel)."\n";
      
        // option tags
        $options = '';
        $valueSelected = false;
        foreach ($this->choices as $choice) {
          $attrs = array( 'value' => $choice['value'] );
          if ($this->multiSelect && is_array($this->value) && in_array($choice['value'], $this->value)) {
            $attrs['selected'] = 'selected';
            $valueSelected = true;
            
          } elseif (!is_array($this->value) && $choice['value'] === $this->value) {
            $attrs['selected'] = 'selected';
            $valueSelected = true;
          }
          $options .= HTML::renderTag('option', $attrs, false, false, $indentLevel + 1).$choice['name']."</option>\n";
        }

        // add the default option first (if the defaultLabel is not blank)
        if (!$this->multiSelect && $this->defaultLabel <> '') {
          $attrs = array( 'value' => $this->defaultValue );
          if (!$valueSelected) {
            $attrs['selected'] = 'selected';
          }
          $html .= HTML::renderTag('option', $attrs, false, false, $indentLevel + 1).$this->defaultLabel."</option>\n";
        }
        
        // add the options to the main HTML code
        $html .= $options;
        
        // end select tag
        $html .= HTML::indent('</select>', $indentLevel);
        

        
      /************************************************************************
       ******************* CHECKBOX / RADIO BUTTON OUTPUT *********************
       ************************************************************************/
        
      } elseif ($this->format == 'individual') {
        // determine if we are using radio buttons or check boxes
        $inputType = (($this->multiSelect) ? 'checkbox' : 'radio');
        
        // create an ordered list to hold the choices
        $html .= HTML::renderTag('ol', array(), false, false, $indentLevel)."\n";
        
        // render all the choices
        foreach ($this->choices as $choice) {
          // start the list item
          $html .= HTML::renderTag('li', array('class' => 'select-field'), false, false, $indentLevel + 1)."\n";
          
          /** NOTE: There are several different ways to render the checkbox/radio button with
           *         a label. The method used below is compliant with the HTML specification,
           *         is consistent with all other uses of input and label tags, complies with
           *         the WCAG 2.0 specification and was found to be very usable in testing
           *         with a visually challenged user using the JAWS and VoiceOver screen readers.
           */
          
          // checkbox/radio button
          $attrs = array( 'type' => $inputType,
                          'name' => $inputName,
                          'id' => $this->name.'-'.$choice['value'],
                          'value' => $choice['value'] );
          if ($this->disabled) {
            $attrs['disabled'] = 'disabled';
          }
          if ($this->multiSelect && is_array($this->value) && in_array($choice['value'], $this->value)) {
            $attrs['checked'] = 'checked';
            
          } elseif (!is_array($this->value) && $choice['value'] === $this->value) {
            $attrs['checked'] = 'checked';
          }
          $html .= HTML::renderTag('input', $attrs, true, true, $indentLevel + 2)."\n";
          
          // the label tag
          $html .= HTML::renderTag( 'label', 
                                    array('for' => $this->name.'-'.$choice['value']), 
                                    false, 
                                    false, 
                                    $indentLevel + 2 );
          $html .= $choice['name']."</label>\n";
          
          // end the list item
          $html .= HTML::indent('</li>', $indentLevel + 1)."\n";
        }
        
        // end the ordered list
        $html .= HTML::indent('</ol>', $indentLevel);
      }
      
      
      // if the widget is disabled, render a hidden input that also contains the value so it's submitted back to the server
      if ($this->disabled) {
        // make sure we have an array of selected values
        $selectedValues = $this->value;
        if (!is_array($selectedValues)) {
          $selectedValues = array($this->value);
        }
        
        // create a hidden input for each value
        foreach ($selectedValues as $selectedValue) {
          $attrs = array( 'type' => 'hidden',
                          'name' => $inputName,
                          'value' => $selectedValue );
          $html .= "\n".HTML::renderTag('input', $attrs, true, false, $indentLevel);
        }
      }
      
      // build the widget HTML
      $html = parent::render($html);

                      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
    
  }  // end of class SelectionWidget

  
  
  
  /**
   * A widget for collecting more than one choice
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 27 June 2010
   */
  class MultiSelectionWidget extends SelectionWidget {
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     choices = an associative array of value/label pairs.
     *     format = string (either flex, list, or individual).
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
      $this->widgetName = 'FrameworkMultiSelectionWidget';
                  
      // allow for multiple selections
      $this->multiSelect = true;
      
    }  // end of function __construct
    
  }  // end of class MultiSelectionWidget

  
  
  /**
   * A widget for collecting a yes or no answer
   *   from a user as part of an HTML form.
   *   
   * @author Paul Rentschler <par117@psu.edu>
   * @since 11 April 2011
   */
  class YesNoWidget extends SelectionWidget {
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
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
      $this->widgetName = 'FrameworkMultiSelectionWidget';
                  
      // define the choices
      $this->choices = array( array( 'name' => 'Yes',   'value' => 'Y' ),
                              array( 'name' => 'No',    'value' => 'N' ),
                            );
                            
      // fix the format as radio buttons
      $this->setFormat('individual');
      
    }  // end of function __construct
    
    
    
    /**
     * Get the value of this widget's input element.
     * 
     * Converts a Y/N value to true/false.
     * 
     * @return boolean The value of the widget's input element expressed as a boolean value.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getValue () {
      
      $result = '';
      if (strtoupper($this->value) == 'Y') {
        $result = true;
      } elseif (strtoupper($this->value) == 'N') {
        $result = false;
      }
      
      return $result;
      
    }  // end of function getValue
    
    
    
    /**
     * Set the value of this widget's input element.
     * 
     * Converts a true/false value to a Y/N.
     * 
     * @param boolean $value The value of this widget's input element.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setValue ($value) {
      
      if (is_bool($value)) {
        if ($value) {
          $this->value = 'Y';
        } else {
          $this->value = 'N';
        }
      } elseif (strtoupper($value) == 'Y' || strtoupper($value) == 'N') {
        $this->value = strtoupper($value);
      }
      
    }  // end of function setValue
    
  }  // end of class YesNoWidget
  
  
  
  /**
   * A widget for allowing a user to select from a list of colors.
   * 
   * @author Andrew Pierce <ajp5103@psu.edu>
   * @since 11 April 2011
   */
  class ColorSelectionWidget extends SelectionWidget {
    
    /**
     * The possible color choices as associative arrays.
     */
    protected $availableColors = array();
    
    
    
    /**
     * Constructor used to setup some basic properties of the widget.
     * 
     * Valid entries for $options are:
     *     value = various
     *     colors = an array of associative arrays in the format:
     *               array( 'id' => #, 'bg' => 000000, 'fg' => FFFFFF' )
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
      $this->widgetName = 'FrameworkColorSelectionWidget';
      
      // assume list
      $this->setFormat('list');
      
      // assign the list of available colors
      if (isset($options['colors'])) {
        $this->availableColors = $options['colors'];
      }
        
    }  // end of function __construct
    


    /**
     * Create the drop-box of colors, using CSS bg/fg colors.
     * 
     * @see SelectionWidget::render()
     * @author Andrew Pierce <ajp5103@psu.edu>
     */
    public function render ($specificTagHtml = '') {
      
      // starting div tag
      $html = HTML::indent('<div>', 1)."\n";
      
      // define the select tag attributes
      $attrs = array( 'name' => $this->name,
                      'id' => $this->name );
      if ($this->tabIndex <> '' && is_numeric($this->tabIndex) && $this->tabIndex >= 0) {
        $attrs['tabindex'] = $this->tabIndex;
      }
      $html .= HTML::renderTag('select', $attrs, false, true, 2)."\n";
    
      // option tags
      $options = '';
      $valueSelected = false;
      foreach ($this->availableColors as $choice) {
        $attrs = array( 'value' => $choice['id'],
                        'style' => 'background-color: #'.$choice['bg'].'; '.
                                   'color: #'.$choice['fg'].';' );
        if ($this->disabled) {
          $attrs['disabled'] = 'disabled';
        }
        if (!is_array($this->value) && $choice['id'] == $this->value) {
          $attrs['selected'] = 'selected';
          $valueSelected = true;
        }
        $options .= HTML::renderTag('option', $attrs, false, false, 3).'Text'."</option>\n";
      }

      // add the default option first
      $attrs = array( 'value' => '' );
      if (!$valueSelected) {
        $attrs['selected'] = 'selected';
      }
      $html .= HTML::renderTag('option', $attrs, false, false, 3)."--</option>\n";
      
      // add the options to the main HTML code
      $html .= $options;
      
      // end select tag
      $html .= HTML::indent('</select>', 2)."\n";
    
      // ending div tag
      $html .= HTML::indent('</div>', 1);
      
      
      // if the widget is disabled, render a hidden input that also contains the value so it's submitted back to the server
      if ($this->disabled) {
        $attrs = array( 'type' => 'hidden',
                        'name' => $this->name,
                        'value' => $this->value );
        $html .= "\n".HTML::renderTag('input', $attrs, true, false, 1);
      }
      
      // build the widget HTML
      $html = Widget::render($html);

                      
      // return the resulting HTML
      return $html;
      
    }  // end of function render
    
  } // end of class ColorSelectionWidget
  