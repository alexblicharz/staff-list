<?php
/*
Plugin Name: Staff List
Version: 1.0.1
Description: Enter and manage all of your organization members or employees.
Author: Alex Blicharz and Patrick Rauland
License: GPL v3
*/


// Initialize the Custom Post Type
function create_staff_list_custom_post_type() {
	
	$labels = array(
		'name'					=> _x( 'Staff List', 'post type general name' ),
		'singular_name'			=> _x( 'Staff Member', 'post type singular name' ),
		'add_new'				=> _x( 'Add New', 'staff' ),
		'add_new_item'			=> __( 'Add New Staff Member' ),
		'edit_item'				=> __( 'Edit Staff Member' ),
		'new_item'				=> __( 'New Staff Member' ),
		'all_items'				=> __( 'All Staff Members' ),
		'view_item'				=> __( 'View Staff Member' ),
		'search_items'			=> __( 'Search Staff Members' ),
		'not_found'				=> __( 'No Staff Members found' ),
		'not_found_in_trash'	=> __( 'No Staff Members found in the Trash' ),
		'parent_item_colon'		=> '',
		'menu_name'				=> 'Staff List'
	);
	
	$args = array(
		'labels'		=> $labels,
		'description'	=> 'A list of all Staff Members',
		'public'		=> true,
		'menu_position'	=> 5,
		'supports'		=> array( 'title', 'editor', 'thumbnail'), // 'title', 'editor', 'thumbnail', 'excerpt', 'comments'
		'has_archive'	=> true,
	);
	
	register_post_type( 'staff', $args );

	// Register Taxonomy
	// Add new taxonomy, make it hierarchical (like categories)

	$labels = array(
		'name' => _x( 'Departments', 'taxonomy general name' ),
		'singular_name' => _x( 'Department', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Departments' ),
		'all_items' => __( 'All Departments' ),
		'parent_item' => __( 'Parent Department' ),
		'parent_item_colon' => __( 'Parent Department:' ),
		'edit_item' => __( 'Edit Department' ),
		'update_item' => __( 'Update Department' ),
		'add_new_item' => __( 'Add New Department' ),
		'new_item_name' => __( 'New Department Name' )
	); 

	register_taxonomy( 'departments', 'staff', array(
		'hierarchical' => true,
		'labels' => $labels, /* NOTICE: Here is where the $labels variable is used */
		'show_ui' => true,
		'query_var' => true
	));
	
}

add_action( 'init', 'create_staff_list_custom_post_type' );

// Add a custom icon to the custom post type
function staff_list_admin_icons() {
	?>
		<style type="text/css" media="screen">
			#menu-posts-staff .wp-menu-image {
				background: url(<?php echo get_bloginfo('url').'/wp-content/plugins/staff-list/assets/images/staff-list-16x16.png'; ?>) no-repeat 6px 6px !important;		
			}

			#menu-posts-staff:hover .wp-menu-image, #menu-posts-staff.wp-has-current-submenu .wp-menu-image {		
				background-position:6px -16px !important;		
			}
			
			#icon-edit.icon32-posts-staff {background: url(<?php echo get_bloginfo('url').'/wp-content/plugins/staff-list/assets/images/staff-list-32x32.png'; ?>) no-repeat;}
        </style>
    <?php	
}

add_action( 'admin_head', 'staff_list_admin_icons' );


// Modify the update messgaes to better relate to the 'staff' custom post type
function update_staff_member_messages( $messages ) {
	
	global $post, $post_ID;
	
	$messages['staff'] = array(
		0 => '', 
		1 => sprintf( __('Staff member updated. <a href="%s">View staff member</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Staff member updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Staff member restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Staff member published. <a href="%s">View staff member</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Staff member saved.'),
		8 => sprintf( __('Staff member submitted. <a target="_blank" href="%s">Preview staff member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Staff member scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview staff member</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Staff member draft updated. <a target="_blank" href="%s">Preview staff member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	
	return $messages;
	
}

add_filter( 'post_updated_messages', 'update_staff_member_messages' );


// Add Contextual Help - ADD THIS IMPROVEMENT LATER TO THE PLUGIN
/*function my_contextual_help( $contextual_help, $screen_id, $screen ) { 
	if ( 'product' == $screen->id ) {

		$contextual_help = '<h2>Products</h2>
		<p>Products show the details of the items that we sell on the website. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
		<p>You can view/edit the details of each product by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

	} elseif ( 'edit-product' == $screen->id ) {

		$contextual_help = '<h2>Editing products</h2>
		<p>This page allows you to view/modify product details. Please make sure to fill out the available boxes with the appropriate details (product image, price, brand) and <strong>not</strong> add these details to the product description.</p>';

	}
	return $contextual_help;
}
add_action( 'contextual_help', 'my_contextual_help', 10, 3 );*/


// Gather basic Staff Memeber info
function staff_info_box() {

	add_meta_box( 
		'staff_member_info',		// Unique identifier for the meta box ( does not have to match funciton name
		__('Staff Member Info'),			// Title of the meta box ( visible to users )
		'staff_info_box_content',	// The function which will display the contents of the box
		'staff',					// Post type the meta box belongs to
		'normal',						// 'normal', 'advanced', or 'side'
		'high'						// 'high', 'core', 'default' or 'low'
	);
	
}
add_action( 'add_meta_boxes', 'staff_info_box' );


// Basic Staff Member info box content
function staff_info_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'staff_info_box_content_nonce' );
	echo '';
	echo '';
	
	// Retrieve current name of the Director and Movie Rating based on review ID
	$staff_title = sanitize_text_field(get_post_meta( $post->ID, 'staff_title', true) );
	$staff_email = sanitize_email(get_post_meta( $post->ID, 'staff_email', true ) );
	$staff_phone = sanitize_text_field(get_post_meta( $post->ID, 'staff_phone', true) );
	$staff_phone_ex = sanitize_text_field(get_post_meta( $post->ID, 'staff_phone_ex', true) );
	$staff_position = absint(get_post_meta( $post->ID, 'staff_position', true) );
	?>

	<table>
		<tr valign="top">
			<th scope="row"><label for="staff-title">Title</label></th>
			<td><input name="staff_title" type="text" id="staff-title" value="<?php echo $staff_title; ?>" class="regular-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="staff-email">Email</label></th>
			<td><input name="staff_email" type="text" id="staff-email" value="<?php echo $staff_email; ?>" class="regular-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="staff-phone">Phone</label></th>
			<td><input name="staff_phone" type="text" id="staff-phone" value="<?php echo $staff_phone; ?>" class="regular-text" /> <label for="staff-phone-ex">ex</label> <input name="staff_phone_ex" type="text" id="staff-phone-ex" value="<?php echo $staff_phone_ex; ?>" class="small-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="staff-position">List Position</label></th>
			<td><input name="staff_position" type="text" id="staff-position" value="<?php echo $staff_position; ?>" class="small-text" /></td>
		</tr>
	</table>
	
	<?php
}


// Save the Staff Member info box fields
add_action( 'save_post', 'staff_info_box_save' );
function staff_info_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !wp_verify_nonce( $_POST['staff_info_box_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	
	// Grab the fields to be updated
	$staff_title = sanitize_text_field($_POST['staff_title']);
	$staff_email = sanitize_email($_POST['staff_email']);
	$staff_phone = sanitize_text_field($_POST['staff_phone']);
	$staff_phone_ex = sanitize_text_field($_POST['staff_phone_ex']);
	$staff_position = absint($_POST['staff_position']);
	
	if (empty($staff_position))
	{
		$staff_position = 0;
	}
	
	// Update the various input fields
	update_post_meta( $post_id, 'staff_title', $staff_title );
	update_post_meta( $post_id, 'staff_email', $staff_email );
	update_post_meta( $post_id, 'staff_phone', $staff_phone );
	update_post_meta( $post_id, 'staff_phone_ex', $staff_phone_ex );
	update_post_meta( $post_id, 'staff_position', $staff_position );
}

/*
	Admin List Columns
*/

	add_filter( 'manage_edit-staff_columns', 'set_custom_staff_columns' );
	
	function set_custom_staff_columns( $columns ) {
		
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Name'),
			'staff_title' => __('Title'),
			'staff_email' => __('Email'),
			'staff_phone' => __('Phone')
		);
		
		return $columns;
	}
	
	add_action( 'manage_staff_posts_custom_column', 'manage_staff_columns', 10, 2);
	
	function manage_staff_columns( $column, $post_id ) {
		global $post;
		
		switch ($column) {
			case 'staff_title':
				$staff_title = get_post_meta ($post_id, 'staff_title', true);
				echo $staff_title;
				break;
			case 'staff_email':
				$staff_email = get_post_meta ($post_id, 'staff_email', true);
				echo $staff_email;
				break;
			case 'staff_phone':
				$staff_phone = get_post_meta ($post_id, 'staff_phone', true);
				$staff_ext = get_post_meta ($post_id, 'staff_phone_ex', true);
				if(empty($staff_ext))
				{
					echo $staff_phone;
				}
				else
				{
					echo $staff_phone.' ex '.$staff_ext;	
				}
				break;
		}
		
	}

// [staff-list]
function staff_list( $atts ) {
	
	if(isset($atts['department']))
	{
		$department = $atts['department'];
	}
	
	$count = 0;
	
	$staff_list_query = array(
		'post_type' => 'staff',
		'departments' => $department,
		'showposts' => -1,
		'orderby' => 'meta_value',
		'meta_key' => 'staff_position',
		'order' => 'DESC'
	);
	
	$staff_list_items = get_posts($staff_list_query);

	// start templating logic
	$template = '<li class="%%CLASS%%">
				<div>
					%%IMAGE%%
					<div class="si-copy">
						%%NAME%%
						%%TITLE%%
						%%EMAIL%%
						%%PHONE%%
						%%PHONE_EX%%
						%%CONTENT%%
					</div>
				</div>
			</li>';
	
	// get rid of the linebreaks (we just had that there for readability)
	$template = preg_replace( '/\s+/', ' ', $template );;

	// apply filters for any modifications from other plugins or themes
	$template = apply_filters( 'staff_list_template', $template, $args );

	// apply filters for any modifications from other plugins or themes
	$heading = "h3";
	$heading = apply_filters( 'staff_list_heading', $heading );
	
	// start the outbut buffer
	ob_start();
	?>
	<ul class="staff-list">
		<?php
		foreach( $staff_list_items as $post ) :	setup_postdata($post);
			// create a copy of the template for each listing
			$tpl = $template;

			// determine the class for the li
			$class = ""; 
			switch($count) {
				case 0:
					$class = 'first';
					$count++;
					break;
				case 2:
					$class = 'last';
					$count = 0;
					break;
				default:
					$count++;
			}
			// also add the clearfix class
			$class .= " clearf";
			// insert class into template
			$tpl = str_replace( '%%CLASS%%', $class, $tpl );

			// determine the image
			$image = '<span class="si-image">' . get_the_post_thumbnail($post->ID,'medium') . '</span>';
			$tpl = str_replace( '%%IMAGE%%', $image, $tpl );
			
			// determine name
			$name = '<' . $heading . ' class="si-name">' . get_the_title($post->ID) . '</' . $heading . '>';
			$tpl = str_replace( '%%NAME%%', $name, $tpl );

			// determine title
			$title = '<span class="si-title">' . get_post_meta($post->ID, 'staff_title', true) . '</span>';
			$tpl = str_replace( '%%TITLE%%', $title, $tpl );

			// determine email
			$email = '<span class="si-email"><a href="mailto:' . get_post_meta($post->ID, 'staff_email', true) . '">'. get_post_meta($post->ID, 'staff_email', true) .'</a></span>';
			$tpl = str_replace( '%%EMAIL%%', $email, $tpl );

			// determine phone
			$phone = '<span class="si-phone">' . get_post_meta($post->ID, 'staff_phone', true) . '</span>';
			$tpl = str_replace( '%%PHONE%%', $phone, $tpl );

			// determine phone extension
			$phoneEx = '<span class="si-phone-ex">' . get_post_meta($post->ID, 'staff_phone_ex', true) . '</span>';
			// echo $phoneEx; exit();
			$tpl = str_replace( '%%PHONE_EX%%', $phoneEx, $tpl );

			// determine the content
			$content = '<div class="si-content">' . apply_filters('the_content', get_the_content()) . '</div>';
			$tpl = str_replace( '%%CONTENT%%', $content, $tpl );

			// print the template to the output buffer
			echo $tpl;
		endforeach;
		?>
	</ul>
	<?php
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
	
}
add_shortcode('staff_list','staff_list');

// enueue styles
wp_enqueue_style( 'staff-list-style', plugins_url('assets/styles/style.css', __FILE__) );


// Modify the meta boxes so the content box shows up at normal priority instead of at the top of the list
function staff_list_add_meta_boxes() {
	global $_wp_post_type_features;
	if (isset($_wp_post_type_features['staff']['editor']) && $_wp_post_type_features['staff']['editor']) {
		unset($_wp_post_type_features['staff']['editor']);
		add_meta_box(
			'description',
			__('Content'),
			'staff_list_content_metabox',
			'staff', 
			'normal', 
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'staff_list_add_meta_boxes', 0 );


// add admin styles
function staff_list_wp_admin_style() {
	wp_register_style( 'staff-list-admin-css', plugins_url('assets/styles/admin-style.css', __FILE__) );
	wp_enqueue_style( 'staff-list-admin-css' );
}
add_action( 'admin_enqueue_scripts', 'staff_list_wp_admin_style' );


// display the meta box
function staff_list_content_metabox( $post ) {
	echo '<div class="wp-editor-wrap">';
	wp_editor($post->post_content, 'content', array('dfw' => true, 'tabindex' => 1) );
	echo '</div>';
}


// change the default custom post type title
function staff_list_default_title( $title ){
	$screen = get_current_screen();
	if  ( 'staff' == $screen->post_type ) {
		$title = 'Enter staff member name here';
	}
	return $title;
}
add_filter( 'enter_title_here', 'staff_list_default_title' )

?>