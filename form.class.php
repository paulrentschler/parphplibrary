<?php
  /**
   * Represents an HTML form and provides a holder
   *   for all the widgets that will be used to
   *   make up the form.
   *   
   * @author Paul Rentschler <par117@psu.edu>
   * @since 8 June 2011
   */
  class Form {
    
    /**
     * The name of the form.
     */
    protected $name = '';
    
    /**
     * The action url for the form.
     */
    protected $actionUrl = '';
    
    /**
     * Hold all the widgets that will make up the form.
     */
    protected $schema = array();
    
    /**
     * Hold the hidden widgets.
     */
    protected $hiddenWidgets = array();
    
    /**
     * Hold the buttons that act on the form.
     */
    protected $buttons = array();
    
    
    
    /**
     * Initialize the form object.
     * 
     * @param string $name The name of the form (optional).
     * @param string $actionUrl The url to submit the form to (optional).
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function __construct ($name = '', $actionUrl = '') {
      
      // set the name and action url
      $this->setName($name);
      $this->setAction($actionUrl);
      
    }  // end of function __construct
    
    
    
    /**
     * Get the unique schematas defined in the schema.
     * 
     * @return array An array of the schematas defined in the schema.
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _getSchematas () {
      
      $schematas = array();
      
      // go through the schema and find all the unique schematas
      foreach ($this->schema as $field) {
        if ($field['schemata'] <> '' && !in_array($field['schemata'], $schematas)) {
          $schematas[] = $field['schemata'];
        }
      }
      
      // return the unique schematas
      return $schematas;
      
    }  // end of function _getSchematas
    
    
    
    /**
     * Loops through the schema and renders the widgets.
     *
     * @param string $schemata The schemata to render widgets for.
     * @param integer $indentLevel The number of levels (not spaces) to indent the code.
     * @return string HTML code for displaying the widgets for the specified schemata.
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _renderWidgets ($schemata = '', $indentLevel = 0) {
      
      $html = "\n";

      // build the list of fields
      $innerHtml = '';
      foreach ($this->schema as $field) {
        if ($field['schemata'] == $schemata) {
          // start the field
          $innerHtml .= HTML::indent('<li>', $indentLevel + 1)."\n";
          
          // render the widget
          $innerHtml .= HTML::indent($field['widget']->render(), $indentLevel + 2)."\n";
          
          // end the field
          $innerHtml .= HTML::indent('</li>', $indentLevel + 1)."\n";
        }
      }
      
      // if we have fields, the output the list of them
      if ($innerHtml <> '') {
        // start the ordered list to hold the fields
        $html .= HTML::renderTag('ol', array( 'class' => 'fieldList' ), false, false, $indentLevel)."\n";
        
        // add the fields that were already rendered
        $html .= $innerHtml;
  
        // close the ordered list
        $html .= HTML::indent('</ol>', $indentLevel)."\n";
      }
      
      // return the html
      return $html;
      
    }  // end of function _renderWidgets
    
    
    
    /**
     * Set the error text properties on the appropriate widgets
     *   based on the validation errors in the Msg singleton object.
     *   
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _setValidationErrors () {
      
      // make sure the Msg object is defined but don't auto-load.
      if (class_exists('Msg', false)) {
        // get the validation errors
        $errors = Msg::get(MSG_TYPE_VALIDATION);
        
        // go through the schema and if a schema entry has a validation error, set the widget's errorText
        foreach ($this->schema as &$field) {
          $fieldName = $field['widget']->getName();
          if (array_key_exists($fieldName, $errors)) {
            $field['widget']->setErrorText($errors[$fieldName]);
          }
        }
      }
      
    }  // end of function _setValidationErrors
    
    
    
    /**
     * Set the value properties on the appropriate widgets based
     *   on the form submission values in the Request singleton object.
     *   
     * @author Paul Rentschler <par117@psu.edu>
     */
    protected function _setWidgetValues () {
      
      // make sure the Request object is defined but don't auto-load.
      if (class_exists('Request', false)) {
        // get all the safe values from the form (if they exist)
        /*** NOTE: this is a critical step because otherwise Request::getSafeValue('x') paves over default values */
        $safeValues = Request::getSafeValues();
        
        // go through the schema and if a schema entry has a safe value, set the widget's value
        foreach ($this->schema as &$field) {
          // get the field and widget name
          $fieldName = $field['widget']->getName();
          $widgetName = $field['widget']->getWidgetName();
          
          // store the values based on the type of widget
          switch ($widgetName) {
            case 'FrameworkDateTimeWidget':
              // make sure the values exist and are not blank
              if (isset($safeValues[$fieldName.'-year']) && $safeValues[$fieldName.'-year'] <> '' && 
                  isset($safeValues[$fieldName.'-month']) && $safeValues[$fieldName.'-month'] <> '' &&
                  isset($safeValues[$fieldName.'-day']) && $safeValues[$fieldName.'-day'] <> '' &&
                  isset($safeValues[$fieldName.'-hour']) && $safeValues[$fieldName.'-hour'] <> '' &&
                  isset($safeValues[$fieldName.'-minute']) && $safeValues[$fieldName.'-minute'] <> '' &&
                  isset($safeValues[$fieldName.'-ampm']) && $safeValues[$fieldName.'-ampm'] <> '') {
                
                // put the values together into a date/time string
                $dateTime = $safeValues[$fieldName.'-year'].'-'.$safeValues[$fieldName.'-month'].'-'.$safeValues[$fieldName.'-day'].' ';
                $dateTime .= $safeValues[$fieldName.'-hour'].':'.$safeValues[$fieldName.'-minute'].' '.$safeValues[$fieldName.'-ampm'];
                $field['widget']->setValue($dateTime);
              }
              break;
              
            case 'FrameworkDateWidget':
              // make sure the values exist and are not blank
              if (isset($safeValues[$fieldName.'-year']) && $safeValues[$fieldName.'-year'] <> '' && 
                  isset($safeValues[$fieldName.'-month']) && $safeValues[$fieldName.'-month'] <> '' &&
                  isset($safeValues[$fieldName.'-day']) && $safeValues[$fieldName.'-day'] <> '') {
                
                // put the values together into a date string
                $date = $safeValues[$fieldName.'-year'].'-'.$safeValues[$fieldName.'-month'].'-'.$safeValues[$fieldName.'-day'];
                $field['widget']->setValue($date);
              }
              break;
              
            case 'FrameworkTimeWidget':
              // make sure the values exist and are not blank
              if (isset($safeValues[$fieldName.'-hour']) && $safeValues[$fieldName.'-hour'] <> '' &&
                  isset($safeValues[$fieldName.'-minute']) && $safeValues[$fieldName.'-minute'] <> '' &&
                  isset($safeValues[$fieldName.'-ampm']) && $safeValues[$fieldName.'-ampm'] <> '') {
                
                // put the values together into a time string
                $time = $safeValues[$fieldName.'-hour'].':'.$safeValues[$fieldName.'-minute'].' '.$safeValues[$fieldName.'-ampm'];
                $field['widget']->setValue($time);
              }
              break;
              
            default:
              // determine if a value exists for this field
              if (isset($safeValues[$fieldName])) {
                $field['widget']->setValue($safeValues[$fieldName]);
              }
              break;
          }
        }
      }
      
    }  // end of function _setWidgetValues

    
    
    /**
     * Add a button to the form.
     * 
     * @param string $label The label that will be shown on the button.
     * @param string $name The name of the button (if blank, will be generated from the label).
     * @param boolean $primary Whether or not the button is the primary choice (default is NO).
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function addButton ($label, $name = '', $primary = false) {
      
      // generate the name if needed
      if ($name == '') {
        $name = $label;
      }
      
      // sanitize the name field
      $name = str_replace(' ', '-', str_replace('_', '-', strtolower(trim($name))));
      
      // add the button
      $this->buttons[] = array( 'label' => $label,
                                'name' => $name,
                                'primary' => $primary );
      
    }  // end of function addButton
    
    
    
    /**
     * Add a widget object to the form's schema.
     * 
     * @param object $widget A widget object to add to the schema.
     * @param string $schemata The schemata to add the widget to (optional).
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function addWidget ($widget, $schemata = '') {
      
      // make sure the getName and getWidgetName methods exists in the widget object
      if (method_exists($widget, 'getName') && method_exists($widget, 'getWidgetName')) {
        // add hidden widgets to a special array
        if ($widget->getWidgetName() == 'FrameworkHiddenWidget') {
          $this->hiddenWidgets[] = $widget;
          
        // add the widget to the form schema
        } else {
          $this->schema[] = array( 'widget' => $widget,
                                   'schemata' => $schemata );
        }
      }
      
    }  // end of function addWidget
    
    
    
    /**
     * Disable the widget or widgets specified.
     * 
     * @param string|array $names A single widget name or an array of widget names to be disabled.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function disableWidgets ($names) {
      
      // if a single widget name is provided, convert it to an array
      if (!is_array($names)) {
        $names = array($names);
      }
      
      // loop through all the widgets and disable the ones specified
      foreach ($this->schema as &$entry) {
        if (in_array($entry['widget']->getName(), $names)) {
          $entry['widget']->disable(true);
        }
      }
      
    }  // end of function disableWidgets
    
    
    
    /**
     * Enable the widget or widgets specified.
     * 
     * @param string|array $names A single widget name or an array of widget names to be enabled.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function enableWidgets ($names) {
      
      // if a single widget name is provided, convert it to an array
      if (!is_array($names)) {
        $names = array($names);
      }
      
      // loop through all the widgets and enable the ones specified
      foreach ($this->schema as &$entry) {
        if (in_array($entry['widget']->getName(), $names)) {
          $entry['widget']->disable(false);
        }
      }
      
    }  // end of function enableWidgets
    
    
    
    /**
     * Get the widgets in a particular schemata or all the widgets.
     * 
     * @param string $schemata The schemata to get the widgets for (optional).
     * @return array An associative array of all or certain widgets in a schemata.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getWidgets ($schemata = '') {
      
      $widgets = array();
      
      // make sure the schemata is lowercase
      $schemata = strtolower(trim($schemata));
      
      // go through the schema and pull out the appropriate widgets
      foreach ($this->schema as $field) {
        if ($schemata == '' || ($schemata <> '' && $field['schemata'] == $schemata)) {
          $widgets[] = $field['widget'];
        }
      }
      
      // return the widgets
      return $widgets;
      
    }  // end of function getWidgets
    
    
    
    /**
     * Remove the specified widget or widgets from the schema.
     * 
     * @param string|array $names A single widget name or an array of widget names to be removed.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function removeWidgets ($names) {
      
      // if a single widget name is provided, convert it to an array
      if (!is_array($names)) {
        $names = array($names);
      }
      
      // convert the names array to an associative array so we can track success
      $widgetsToRemove = array();
      foreach ($names as $name) {
        $widgetsToRemove[$name] = false;
      }
      
      // because the schema is an unordered array, we have to completely rebuild it to remove a widget
      $newSchema = array();
      foreach ($this->schema as $entry) {
        // see if we should remove this widget
        if (in_array($entry['widget']->getName(), $names)) {
          // this widget should be removed, indicate success
          $widgetsToRemove[$entry['widget']->getName()] = true;
          
        } else {
          // keep the widget, add it to the new schema
          $newSchema[] = $entry;
        }
      }
      
      // replace the schema
      $this->schema = $newSchema;
      
      
      // see if there are any widgets that have not been removed yet
      $remainingNames = array();
      foreach ($widgetsToRemove as $name => $success) {
        if (!$success) {
          $remainingNames[] = $name;
        }
      }
      
      // if widgets remain, look at the hidden widgets
      if (count($remainingNames) > 0) {
        $newHiddenWidgets = array();
        foreach ($this->hiddenWidgets as $entry) {
          // see if we should remove this widget
          if (in_array($entry->getName(), $remainingNames)) {
            // this widget should be removed, indicate success
            $widgetsToRemove[$entry->getName()] = true;
            
          } else {
            // keep the widget, add it to the new hidden widgets list
            $newHiddenWidgets[] = $entry;
          }
        }
        
        // replace the hidden widgets list
        $this->hiddenWidgets = $newHiddenWidgets;
      }
      
    }  // end of function removeWidgets
    
    
    
    /**
     * Render the form.
     * Populates the values of each widget with any
     *   values submitted to the form and also updates
     *   any error messages due to validation errors.
     * 
     * @param integer $indentLevel The number of levels (not spaces) to indent the form code.
     * @return string HTML that creates the form.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function render ($indentLevel = 0) {
      
      // update the values of the widgets based on any values submitted
      $this->_setWidgetValues();
      
      // add any validation errors to the widgets
      $this->_setValidationErrors();
      
      $html = "\n";
      
      // render the form tag
      $html .= HTML::renderTag( 'form', 
                                array( 'action' => $this->actionUrl,
                                       'name' => $this->name,
                                       'id' => 'form-'.$this->name,
                                       'method' => 'post',
                                       'enctype' => 'multipart/form-data' ), 
                                false, 
                                true, 
                                $indentLevel )."\n";

      // get the schematas to turn into fieldsets
      $schematas = $this->_getSchematas();
      
      // render the widgets with no schemata
      $html .= $this->_renderWidgets('', $indentLevel + 1);
        
      // render the widgets with a schemata as fieldsets
      foreach ($schematas as $schemata) {
        // format the schemata into the various output formats
        $schemataId = strtolower(str_replace(' ', '-', str_replace('_', '-', trim($schemata))));
        $schemataLabel = ucwords(str_replace('-', ' ', str_replace('_', ' ', trim($schemata))));
          
        // start the fieldset and output the legend
        $html .= HTML::renderTag('fieldset', array('id' => 'fieldset-'.$schemataId), false, false, $indentLevel + 1)."\n";;
        $html .= HTML::indent('<legend>'.$schemataLabel.'</legend>', $indentLevel + 2)."\n";
          
        // render the widgets for this schemata
        $html .= $this->_renderWidgets($schemata, $indentLevel + 2);
          
        // close the fieldset
        $html .= HTML::indent('</fieldset>', $indentLevel + 1)."\n\n";
      }
      
      // start the buttons container 
      $html .= HTML::renderTag('div', array('class' => 'formControls'), false, false, $indentLevel + 1)."\n";
      
      // insert the hidden widgets
      foreach ($this->hiddenWidgets as $hiddenWidget) {
        $html .= HTML::indent($hiddenWidget->render(), $indentLevel + 2);
      }
      
      // insert a hidden field that can be used to track that the form was submitted
      $html .= HTML::renderTag( 'input',
                                array( 'type' => 'hidden',
                                       'name' => 'submitted',
                                       'value' => 'ok' ),
                                true,
                                false,
                                $indentLevel + 2)."\n";
                                
      // render the buttons
      foreach ($this->buttons as $button) {
        // figure out the classes for this button
        $classes = array();
        if ($button['primary']) {
          $classes[] = 'primary';     // ideal tag
          $classes[] = 'context';     // what Plone uses
        } else {
          $classes[] = 'secondary';   // ideal tag
          $classes[] = 'standalone';  // what Plone uses
        }
        
        // render the button
        $html .= HTML::renderTag( 'input',
                                  array( 'type' => 'submit',
                                         'value' => $button['label'],
                                         'name' => $button['name'],
                                         'id' => $button['name'],
                                         'class' => implode(' ', $classes) ),
                                  true,
                                  false,
                                  $indentLevel + 2)."\n";
      }
      
      // close the buttons container
      $html .= HTML::indent('</div>', $indentLevel + 1)."\n";
      
      // close the form
      $html .= HTML::indent('</form>', $indentLevel)."\n";
                                 
      // return the rendered HTML
      return $html;
      
    }  // end of function render
    
    
    
    /**
     * Replace an existing widget in the schema with a new widget.
     * 
     * @param string $existingName The name of the existing widget that will be replaced.
     * @param object $widget A widget object to replace the existing one with.
     * @param string $newSchemata The schemata to assign this new widget to (optional).
     * @return boolean Whether or not the widget was replaced.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function replaceWidget ($existingName, $widget, $newSchemata = '') {
      
      // assume the worst
      $result = false;

      // make sure the getName and getWidgetName methods exists in the widget object
      if (method_exists($widget, 'getName') && method_exists($widget, 'getWidgetName')) {
        // hidden widgets are in a special array
        if ($widget->getWidgetName() == 'FrameworkHiddenWidget') {
          // find the hidden widget
          foreach ($this->hiddenWidgets as &$entry) {
            if ($entry->getName() == $existingName) {
              // found it, now replace it
              $entry = $widget;
              $result = true;
            }
          }
          
        // other widgets are in the schema
        } else {
          // find the existing widget
          foreach ($this->schema as &$entry) {
            if ($entry['widget']->getName() == $existingName) {
              // found it, now replace it
              $entry['widget'] = $widget;
              $entry['schemata'] = $newSchemata;
              $result = true;
            }
          }
        }
      }
      
      return $result;
      
    }  // end of function replaceWidget
    
    
    
    /**
     * Set the action url of the form.
     * 
     * @param string $actionUrl The url to submit the form to.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function setAction ($actionUrl) {
      
      $this->actionUrl = trim($actionUrl);
      
    }  // end of function setAction
    
    
    
    /**
     * Set the name of the form.
     * 
     * @param string $name The name of the form.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function setName ($name) {
      
      $this->name = trim($name);
      
    }  // end of function setName
    
    
    
    /**
     * Set the value of a specified widget.
     * 
     * @param string $widgetName The name of the widget to set the value for.
     * @param various $value The value to set for the widget.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function setValue ($widgetName, $value) {
      
      // find the widget
      foreach ($this->schema as &$entry) {
        if ($entry['widget']->getName() == $widgetName) {
          // found it.
          $entry['widget']->setValue($value);
        }
      }
      
    }  // end of function setValue
    
  }  // end of class Form
