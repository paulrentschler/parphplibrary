<?php
  /**
   * Construct and send e-mail messages.
   * 
   * @author Paul Rentschler <paul@rentschler.ws>
   * @since 6 June 2011
   */
  class Email {

    /**
     * Array of addresses to send the e-mail to.
     */
    protected $toAddresses = array();
    
    /**
     * Array of addresses to carbon copy the e-mail to.
     */
    protected $cCAddresses = array();
    
    /**
     * Array of addresses to blind carbon copy the e-mail to.
     */
    protected $bCCAddresses = array();
    
    /**
     * Address to send the e-mail from.
     */
    protected $fromAddress = '';
    
    /**
     * Address that replies should go to.
     */
    protected $replyToAddress = '';
    
    /**
     * Subject of the e-mail message.
     */
    protected $subject = '';
    
    /**
     * Body of the e-mail message.
     */
    protected $body = '';
    
    /**
     * Whether or not the anti-spam header should be included.
     */
    protected $useAntiSpamFlag = false;
    
    /**
     * Whether or not the body of the e-mail message has been provided yet.
     */
    protected $bodySet = false;
        


    /**
     * Add a TO address to the e-mail message.
     *
     * @param string $emailAddress The e-mail address to add.
     * @param string $name The name of the person (optional).
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function addToAddress ($emailAddress, $name = '') {
    	
    	if (trim($emailAddress) <> '') {
      	if (trim($name) <> '') {
      		$this->toAddresses[] = trim($name).' <'.trim($emailAddress).'>';
      	} else {
    	  	$this->toAddresses[] = trim($emailAddress);
    	  }
    	}
    	
    }  // end of function addToAddress
    
    
    
    /**
     * Add a CC address to the e-mail message.
     *
     * @param string $emailAddress The e-mail address to add.
     * @param string $name The name of the person (optional).
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function addCCAddress ($emailAddress, $name = '') {
    	
    	if (trim($emailAddress) <> '') {
      	if (trim($name) <> '') {
      		$this->cCAddresses[] = trim($name).' <'.trim($emailAddress).'>';
      	} else {
    	  	$this->cCAddresses[] = trim($emailAddress);
    	  }
    	}
    	
    }  // end of function addCCAddress
    
    
    
    /**
     * Add a BCC address to the e-mail message.
     *
     * @param string $emailAddress The e-mail address to add.
     * @param string $name The name of the person (optional).
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function addBCCAddress ($emailAddress, $name = '') {
    	
    	if (trim($emailAddress) <> '') {
      	if (trim($name) <> '') {
      		$this->bCCAddresses[] = trim($name).' <'.trim($emailAddress).'>';
      	} else {
    	  	$this->bCCAddresses[] = trim($emailAddress);
    	  }
    	}
    	
    }  // end of function addBCCAddress
    
    
    
    /**
     * Set the address the e-mail will be sent from.
     *
     * @param string $emailAddress The e-mail address being sent from.
     * @param string $name The name of the person (optional).
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setFromAddress ($emailAddress, $name = '') {
    	
    	if (trim($emailAddress) <> '') {
      	if (trim($name) <> '') {
      		$this->fromAddress = trim($name)." <".trim($emailAddress).">";
      	} else {
    	  	$this->fromAddress = trim($emailAddress);
    	  }
    	}
    	
    }  // end of function setFromAddress
    


    /**
     * Set the e-mail address that a reply should go to.
     *
     * @param string $emailAddress The e-mail address the reply should go to.
     * @param string $name The name of the person (optional).
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setReplyToAddress ($emailAddress, $name = '') {
    	
    	if (trim($emailAddress) <> '') {
      	if (trim($name) <> '') {
      		$this->replyToAddress = trim($name)." <".trim($emailAddress).">";
      	} else {
    	  	$this->replyToAddress = trim($emailAddress);
    	  }
    	}
    	
    }  // end of function setReplyToAddress


    
    /**
     * Set the subject of the e-mail message.
     *
     * @param string $subject The subject of the e-mail message.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function setSubject ($subject) {
    	
    	if (trim($subject) <> '') {
    		$this->subject = trim($subject);
    	}
    	
    }  // end of function setSubject
    
    
    
    /**
     * Indicate that we should use the anti-spam header.
     *
     * @param boolean $trueFalse Whether or not to use the anti-spam header flag.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function useAntiSpamFlag ($trueFalse) {
    	
    	$this->useAntiSpamFlag = $trueFalse;
    	
    }  // end of function useAntiSpamFlag
    
    
    
    /**
     * Set the body of the e-mail message.
     *
     * @param string $body The body text of the e-mail message (plain text, no HTML).
     */
    public function setBody ($body) {
    	
    	$this->body = $body;
  	  $this->bodySet = true;
    	
    }  // end of function setBody
    
    
    
    /**
     * Indicate what template file to use to generate the
     *   body of the e-mail message.
     * 
     * @param string $templateFilename The path and filename of the template file to use.
     * @param boolean $removeComments Whether or not comments should be removed from the template file.
     * @return boolean Whether or not reading the template in was successful.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function useTemplate ($templateFilename, $removeComments = true) {
    	
    	$result = false;
    	if ($templateFilename <> '' && file_exists($templateFilename)) {
    		if ($removeComments) {
    			$this->mBody = '';
    			$FILE = fopen($templateFilename, "r");
    			while (!feof($FILE)) {
    				$line = fgets($FILE);
    				if (substr(trim($line), 0, 2) <> '//') {
    					$this->body .= trim($line)."\n";
    				}
    			}
    			fclose($FILE);
    			$result = true;
    					
    		} else {
          $this->body = file_get_contents($templateFilename);
          $result = true;
    		}
    	}
    	
    	return $result;
    	
    }  // end of function useTemplate
    
    
    
    /**
     * If a template file is being used, populate the template with
     *   the provided values.
     *
     * @param array $valuesArray An associative array of values to be substituted into the template.
     * @return boolean Whether or not the substitution of values was successful.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function populateTemplate ($valuesArray) {
    	
    	$result = false;
    	
    	if ($this->body <> '' && is_array($valuesArray)) {
    		foreach ($valuesArray as $key => $value) {
    			$this->body = str_replace('[*'.$key.'*]', $value, $this->body);
    		}
  	    $this->bodySet = true;
    	}
    	
    	return $result; 

    }  // end of function populateTemplate
    
    
    
    /**
     * Send the e-mail message.
     *
     * @return boolean Whether or not the sending of the message was successful.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function send () {
    	
    	$result = false;
    	if ($this->_okToGenerateEmail()) {
        // get the to addresses and headers
        $to = $this->_generateToAddresses();
    		$headers = $this->_generateHeaders();
    		
    		$result = mail($to, $this->subject, $this->body, $headers);
    	}
    	
    	return $result;
    	
    }  // end of function send
    
    
    
    /**
     * Write the e-mail message out to a file.
     *
     * @param string $filename The path and filename of where to write the e-mail message to.
     * @return boolean Whether or not the e-mail was successfully written to the file.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    public function saveToFile ($filename) {
    	
    	$result = false;
    	if ($filename <> '' && !file_exists($filename)) {
      	if ($this->_okToGenerateEmail()) {
          // get the to addresses and headers
          $to = $this->_generateToAddresses();
          $headers = $this->_generateHeaders();
    		  
    		  $FILE = fopen($filename, 'w');
    		  fwrite($FILE, date('r')."\n");
    		  fwrite($FILE, 'To: '.$to."\n");
    		  fwrite($FILE, 'Subject: '.$this->mSubject."\n");
    		  fwrite($FILE, $headers);
    		  fwrite($FILE, "\n");
    		  fwrite($FILE, $this->mBody."\n");
    		  fclose($FILE);
    		  
    		  $result = true;
      	}
    	}
    	
    	return $result;
    	
    }  // end of function saveToFile
    
    
    
    /**
     * Determine if all the fields have been provided and thus it's ok
     *   to create an e-mail message.
     * 
     * @return boolean Whether or not it's ok to generate the e-mail message for sending/saving.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _okToGenerateEmail () {

      // assume we are good to go
      $result = true;
      
      // do we have a to, cc, or bcc address to send to
      if (count($this->toAddresses) == 0 && count($this->cCAddresses) == 0 && count($this->bCCAddresses) == 0) {
        $result = false;
      }
      
      // do we have a from address
      if (trim($this->fromAddress) == '') {
        $result = false;
      }
      
      // do we have a subject
      if (trim($this->subject) == '') {
        $result = false;
      }
      
      // has the body been set
      if (!$this->bodySet) {
        $result = false;
      }
      
      return $result;
      
    }  // end of function _okToGenerateEmail
    
    
    
    /**
     * Generate the TO addresses for the e-mail.
     * 
     * @return string The to addresses.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _generateToAddresses () {
      
      $to = '';
      if (count($this->toAddresses) > 0) {
        $to = implode(', ', $this->toAddresses);
      }
      
      return $to;
      
    }  // end of function _generateToAddresses
    
    
    
    /**
     * Generate the headers for the e-mail.
     * 
     * @return string The headers.
     * @author Paul Rentschler <paul@rentschler.ws>
     */
    protected function _generateHeaders () {
      
      $headers = 'From: '.$this->fromAddress."\r\n";
      if ($this->replyToAddress <> '') {
        $headers .= 'Reply-To: '.$this->replyToAddress."\r\n";
      }
      if (count($this->cCAddresses) > 0) {
        $headers .= 'CC: '.implode(', ', $this->cCAddresses)."\r\n";
      }
      if (count($this->bCCAddresses) > 0) {
        $headers .= 'BCC: '.implode(', ', $this->bCCAddresses)."\r\n";
      }
      if ($this->useAntiSpamFlag) {
        $headers .= 'X-SurfsUp: WebDesign'."\r\n";
      }
      $headers .= 'X-Mailer: php '.phpversion()."\r\n";
      
      return $headers;
          
    }  // end of function _generateHeaders
    
  }  // end of class Email
