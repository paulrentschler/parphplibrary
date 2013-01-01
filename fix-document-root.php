<?php

// see if an alternate document root has been specified
if (isset($_SERVER['DOC_ROOT']) && $_SERVER['DOC_ROOT'] <> '') {
    // for security, make sure the new document root contains the base of the default document root
    if (strpos($_SERVER['DOC_ROOT'], dirname(dirname($_SERVER['DOCUMENT_ROOT']))) !== false) {
        // use the alternate document root
        $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOC_ROOT'];
    }
}

// include the setup.php file if one can be found
$setupFilenames = array(
    dirname($_SERVER['DOCUMENT_ROOT']).'/php/setup.php.local',
    dirname($_SERVER['DOCUMENT_ROOT']).'/php/setup.php',
    dirname($_SERVER['DOCUMENT_ROOT']).'/includes/setup.php.local',
    dirname($_SERVER['DOCUMENT_ROOT']).'/include/setup.php.local',
    dirname($_SERVER['DOCUMENT_ROOT']).'/includes/setup.php',
    dirname($_SERVER['DOCUMENT_ROOT']).'/include/setup.php',
);
foreach ($setupFilenames as $filename) {
    if (file_exists($filename)) {
        require_once($filename);
        break;
    }
}

?>

