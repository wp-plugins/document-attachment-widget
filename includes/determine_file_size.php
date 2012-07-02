<?php
/* Description: Include script is used to determine the size of a remote file. 
* Version: 1.0
* Author: Geansai .Ltd
* Author URI: http://geansai.co.uk
* License: GPLv2 or later
*/

function getfilesize($file_url, $opt_size) {
	// get the file path of the attachment
	$file_path = ereg_replace(get_bloginfo('url'), $_SERVER['DOCUMENT_ROOT'], $file_url);
	
	// Format the file size
	$size = filesize($file_path) / 1024; 
    if($size < 1024) { 
        $size = number_format($size, 2); 
        $size .= ' KB'; 
    } else { 
    if($size / 1024 < 1024) { 
		$size = number_format($size / 1024, 2); 
        $size .= ' MB'; 
    } else if ($size / 1024 / 1024 < 1024) { 
        $size = number_format($size / 1024 / 1024, 2); 
        $size .= ' GB'; 
    }  
   } 
  // Check to see is the widget should display the file size.
		if($opt_size == '1'):
			return $size; 
		endif;
}

?>