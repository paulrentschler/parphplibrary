<?php
  /**
   * Provide support for getting information from the Plone
   *   powered site that runs along side this application.
   * 
   * @author Paul Rentschler <par117@psu.edu>
   * @since 24 May 2011
   */
  class Plone {

    /**
     * The url of the plone site.
     */
    protected $url = 'http://home.huck.psu.edu';
    
    
    
    /**
     * Constructor to setup the Plone object.
     *
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function __construct () {
      
      // update the url of the site based on whether or not we are using SSL
      if ($_SERVER['SERVER_PORT'] == 443) {
        $this->url = str_replace('http:', 'https:', $this->url);
      }
      
    }  // end of function __construct


    
    /**
     * Get the current global navigation tabs from the Plone site.
     * 
     * @return array An associative array of global navigation tab information.
     * @author Paul Rentschler <par117@psu.edu>
     */
    public function getGlobalNavigation () {
  
      // assume the worst
      $globalNav = array();
      
  
      // open a connection to the application server
      $url = $this->url.'/getglobalnav';
      $ch = curl_init($url);
  
      // set the various connection options
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_COOKIESESSION, false);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, false);
      // if a use is currently logged in via WebAccess pass that information to the Plone
      //   site so we get the current list of global nav entries for the current user
      if (isset($_SERVER['REMOTE_USER']) && $_SERVER['REMOTE_USER'] <> '') {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'X_REMOTE_USER: '.$_SERVER['REMOTE_USER'] ));
      }
  
      // send the command and get the response from the Plone site
      $content = curl_exec($ch);
      $curlResponse = curl_getinfo($ch);
  
      // make sure we got a response
      if ($curlResponse['http_code'] == 200) {
        // decode the response (in JSON)
        if ($content <> '') {
          $globalNav = json_decode($content, true);
        }
      }
      
      // return the result
      return $globalNav;
      
    }  // end of function getGlobalNavigation
    
  }  // end of class Plone
  