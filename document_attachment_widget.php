<?php
/*
Plugin Name: Document Attachment Widget
Plugin URI: http://geansai.co.uk
Description: This is a plugin to add a new wiget to wordpress, which finds all media items attached to the selected page or post. 
Use [document-list-attachments] shortcode to list these attachments.

Version: 1.4
Author: Geansai .Ltd
Author URI: http://geansai.co.uk
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// load the style sheets for the document attachment widget
	include_once 'includes/load_styles.php';

// Function to determine file size of remote files
	include_once 'includes/determine_file_size.php';
	
global $args, $instance;
// extend WP_Widget class
class Attachment_Widget extends WP_Widget {
	/* constructor */
	function __construct() {
		parent::WP_Widget( /* Base ID */'document_attachments',/* Name */'Document Attachments', array( 'description' =>'A widget to display the documents, which have been attached to a page or post'));

}

	/** @see WP_Widget::widget */
	 public function widget( $args, $instance ) {
		extract($args);		
		// tmp checking please remove print_r($args);		
		global $wpdb, $post;
		// add an array to add the mime types to after we check which user options have been selected.	
		$mime_type_array = array();
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$limit = $instance['limit']; // NEW 
		
		$opt_msword = $instance['word_doc'];			
		$opt_msexcel = $instance['excel_doc'];
		$opt_pdf = $instance['pdf_doc'];
		$opt_image = $instance['image_doc'];
		$opt_flash = $instance['flash_doc'];
		$opt_size = $instance['doc_size'];
		$opt_description = $instance['doc_description'];
		$opt_hide_sidebar = $instance['hide_sidebar'];
		
		// Check to see what mime types the widget should display.
		if($opt_msword == '1'):
			array_push($mime_type_array, "'application/msword'");
		else:
			array_push($mime_type_array, "' '");
		endif;
		if($opt_msexcel == '1'):
			array_push($mime_type_array, "'application/vnd.ms-excel'");
		else:
			array_push($mime_type_array, "' '");
		endif;
		if($opt_pdf == '1'):
			array_push($mime_type_array, "'application/pdf'");
		else:
			array_push($mime_type_array, "' '");
		endif;
		if($opt_image == '1'):
			array_push($mime_type_array, "'image/gif', 'image/jpeg', 'image/png'");
		else:
			array_push($mime_type_array, "' '");
		endif;
		if($opt_flash == '1'):
			array_push($mime_type_array, "'application/x-shockwave-flash'");
		else:
			array_push($mime_type_array, "' '");
		endif;
			
		// build list of mime types as a string to use within the db query
		$mime_type_str = implode(", ", $mime_type_array);		
		
		if(isset($showresults)):
			$quick_check = count($showresults);	
				if(!$quick_check ==0){
					echo $before_widget;
				}
		endif;
		
		
		if($limit == 0):
			$limit=1;
		endif;
			
		if ( $title ):
		   $post_id = $post->ID;
			// check to see if post id is set.
			
			
			if(isset($post_id)):
				// Query the post table to find all attachment items related to the post parent ID 			
				$showresults = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE post_type = 'attachment' AND post_mime_type IN ($mime_type_str) AND post_parent = $post_id LIMIT $limit");				
			endif;
			
			
			
			// check to see if there are any attachment documents found in the database.
			if(isset($showresults)):
							
				$quick_check = count($showresults);	
				if(!$quick_check ==0){
					if(isset($args['id'])) :
						if (isset($opt_hide_sidebar)):						
							if($opt_hide_sidebar == '0'):
								echo '<section class="attachment_holder widget_document_attachments">';
								echo $before_title . $title . $after_title;
								echo '<ul class="attachment">';								
								// loop over the results
								foreach($showresults as $application):
									$application_type = explode('/', $application->post_mime_type);
									// Determine mime type and set the file type icon class
									$icon = $application_type[1];
									if ($icon =='vnd.ms-powerpoint'):
										$icon = 'powerpoint';
									endif;
									if ($icon =='vnd.ms-excel'):
										$icon = 'excel';
									endif;
						
									// check to see if the document description should be displayed
									if($opt_description == '1'):
										$description = '<span class="description">'.$application->post_content.'</span>';
									else:
										$description = '';
									endif;
						
									// print the final output to the page
									$file_url = $application->guid;										
									echo '<li class="'.$icon.'"><a title="Download the '.$application->post_title.'" href="'.$application->guid.'">'.$application->post_title.'</a><span class="filesize"> '.getfilesize($file_url, $opt_size).'</span><br />'.$description.'</li>';
								endforeach;
								echo '</ul>';
								echo '</section>';
							endif;
						endif;

						else:
						echo '<section class="attachment_holder widget_document_attachments">';
						echo $before_title . $title . $after_title;
						echo '<ul class="attachment">';								
						// loop over the results
						foreach($showresults as $application):
							$application_type = explode('/', $application->post_mime_type);
							// Determine mime type and set the file type icon class
							$icon = $application_type[1];
							if ($icon =='vnd.ms-powerpoint'):
								$icon = 'powerpoint';
							endif;
							if ($icon =='vnd.ms-excel'):
								$icon = 'excel';
							endif;
						
							// check to see if the document description should be displayed
							if($opt_description == '1'):
								$description = '<span class="description">'.$application->post_content.'</span>';
							else:
								$description = '';
							endif;
						
							// print the final output to the page
							$file_url = $application->guid;										
							echo '<li class="'.$icon.'"><a title="Download the '.$application->post_title.'" href="'.$application->guid.'">'.$application->post_title.'</a><span class="filesize"> '.getfilesize($file_url, $opt_size).'</span><br />'.$description.'</li>';
						endforeach;
						echo '</ul>';
						echo '</section>';
					endif;	
				}			
			endif;	

		endif;
		if(isset($showresults)):
			$quick_check = count($showresults);	
				if(!$quick_check ==0){
					echo $after_widget;
				}
		endif;
		
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '','limit' => '','word_doc' => '','excel_doc' => '','pdf_doc' => '','image_doc' => '','flash_doc' => '','doc_size' => '','doc_description' => '','hide_sidebar' => '') );
	
	
		$instance['title'] = strip_tags($new_instance['title']);
		
		$instance['limit'] = strip_tags($new_instance['limit']); // NEW
		
		$instance['word_doc'] = $new_instance['word_doc'] ? 1 : 0;		
		$instance['excel_doc'] = $new_instance['excel_doc'] ? 1 : 0;
		$instance['pdf_doc'] = $new_instance['pdf_doc'] ? 1 : 0;
		$instance['image_doc'] = $new_instance['image_doc'] ? 1 : 0;
		$instance['flash_doc'] = $new_instance['flash_doc'] ? 1 : 0;		
		$instance['doc_size'] = $new_instance['doc_size'] ? 1 : 0;
		$instance['doc_description'] = $new_instance['doc_description'] ? 1 : 0;
		$instance['hide_sidebar'] = $new_instance['hide_sidebar'] ? 1 : 0;
			
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {			
			$instance = wp_parse_args( (array) $instance, array( 'title' => '','limit' => '','word_doc' => '','excel_doc' => '','pdf_doc' => '','image_doc' => '','flash_doc' => '','doc_size' => '','doc_description' => '', 'hide_sidebar' => '') );
			$title = esc_attr($instance['title']);
			// NEW
			
			$limit = $instance['limit']; // NEW 
			
			$opt_msword = $instance['word_doc'] ? 'checked="checked"' : '';			
			$opt_msexcel = $instance['excel_doc'] ? 'checked="checked"' : '';
			$opt_pdf = $instance['pdf_doc'] ? 'checked="checked"' : '';
			$opt_image = $instance['image_doc'] ? 'checked="checked"' : '';
			$opt_flash = $instance['flash_doc'] ? 'checked="checked"' : '';
			$opt_size = $instance['doc_size'] ? 'checked="checked"' : '';
			$opt_description = $instance['doc_description'] ? 'checked="checked"' : '';
			$opt_hide_sidebar = $instance['hide_sidebar'] ? 'checked="checked"' : '';
		}
		else {
			$title =  'Document attachments';
			$limit = '20';
			$opt_msword = '';			
			$opt_msexcel = '';
			$opt_pdf = '';
			$opt_image = '';
			$opt_flash = '';
			$opt_size = '';
			$opt_description = '';
			$opt_hide_sidebar = '';
		}
		echo 
		'<p><label for="'.$this->get_field_id('title').'">'._e('Title:').'</label> 
		<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" /></p>
		
		<p><label for="'.$this->get_field_id('limit').'">Limit the number of items returned.</label> 
		<input class="widefat" id="'.$this->get_field_id('limit').'" name="'.$this->get_field_name('limit').'" type="text" value="'.$limit.'" /></p>
		
		<p><em>Select attachment file types,<br />which are allowed to be displayed:</em><br />
		<input class="checkbox" type="checkbox" '.$opt_msword.' id="'.$this->get_field_id('word_doc').'" name="'.$this->get_field_name('word_doc').'" /> <label for="'.$this->get_field_name('word_doc').'">MS Word</label><br />		
		<input class="checkbox" type="checkbox" '.$opt_msexcel.' id="'.$this->get_field_id('excel_doc').'" name="'.$this->get_field_name('excel_doc').'" /> <label for="'.$this->get_field_name('excel_doc').'">MS Excel</label><br />		
		<input class="checkbox" type="checkbox" '.$opt_pdf.' id="'.$this->get_field_id('pdf_doc').'" name="'.$this->get_field_name('pdf_doc').'" /> <label for="'.$this->get_field_name('pdf_doc').'">Adobe PDF</label><br />		
		<input class="checkbox" type="checkbox" '.$opt_image.' id="'.$this->get_field_id('image_doc').'" name="'.$this->get_field_name('image_doc').'" /> <label for="'.$this->get_field_name('image_doc').'">Images</label><br />		
		<input class="checkbox" type="checkbox" '.$opt_flash.' id="'.$this->get_field_id('flash_doc').'" name="'.$this->get_field_name('flash_doc').'" /> <label for="'.$this->get_field_name('flash_doc').'">Adobe Flash</label></p>
		<hr />		
		<p><em>Hide the widget from the sidebar if using short code:</em><br />
		<input class="checkbox" type="checkbox" '.$opt_hide_sidebar.' id="'.$this->get_field_id('hide_sidebar').'" name="'.$this->get_field_name('hide_sidebar').'" /> <label for="'.$this->get_field_name('hide_sidebar').'">Hide from sidebars</label></p>
		<hr />	
		<p><em>Display the file size:</em><br />
		<input class="checkbox" type="checkbox" '.$opt_size.' id="'.$this->get_field_id('doc_size').'" name="'.$this->get_field_name('doc_size').'" /> <label for="'.$this->get_field_name('doc_size').'">Show file size</label></p>
		<hr />				
		<p><em>Display the files description text:</em><br />
		<input class="checkbox" type="checkbox" '.$opt_description.' id="'.$this->get_field_id('doc_description').'" name="'.$this->get_field_name('doc_description').'" /> <label for="'.$this->get_field_name('doc_description').'">Show description text</label></p><br />';

	}

} 
// register Attachment_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget("Attachment_Widget");' ) );



function document_list_attachments() {
	// Get the array values of the widget so we can pass it back to the widget class function.
	$instance = array_shift(array_values(get_option('widget_document_attachments')));
	// Build a fake args array to pass on to the widget class function	
	$args = array(
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => ''
	);
	// Add a new widget class object
	$newWidgetObject = new Attachment_Widget();
	$newWidgetObject->widget($args,$instance);
}
// Add the shortcode action
add_shortcode('document-list-attachments','document_list_attachments'); 


// added a new function to remove the saved widget values from the database to fix version conflicts
class daw_activation {
	static function drop_daw_data() {
		global $wpdb;
		$option_title = 'widget_document_attachments';
		$force_delete = true;
		$option_name = $wpdb->get_var( "SELECT option_name FROM $wpdb->options WHERE option_name = '" . $option_title . "'" );
		delete_option($option_name);
	}
}

// Call activation hooks
register_activation_hook( __FILE__, array('daw_activation', 'drop_daw_data'));
register_deactivation_hook( __FILE__, array('daw_activation', 'drop_daw_data'));

?>
