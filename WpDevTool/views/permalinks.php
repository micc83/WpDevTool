<?php
/**
 * WpDevTool Permalinks Page
 *
 * @since 0.0.1
 */
function wpdevtool_menu_permalinks_page() {
	
	if ( !get_option( 'permalink_structure' ) )
		return;
	
	$page = add_submenu_page( 'wpdevtool_admin', __( 'WpDevTool Permalinks', 'wpdevtool' ), __( 'Permalinks', 'wpdevtool' ), 'manage_options', 'wpdevtool_permalinks', 'wpdevtool_permalinks_page' );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_permalinks_page_styles' );
	
}
add_action( 'admin_menu', 'wpdevtool_menu_permalinks_page' );

/**
 * WpDevTool Permalinks Page Styles
 *
 * @since 0.1.0
 */
function wpdevtool_permalinks_page_styles() {
	
	wp_enqueue_style( 'WpDevToolStylesheet' );
	
}

/**
 * WpDevTool Permalinks Table Class
 *
 * @since 0.0.1
 */
class Wpdevtool_Permalinks_Table extends WDT_Table {
	
	/**
	 * Constructor
	 * 
	 * @since 0.1.0
	 */
	function __construct() {
		 parent::__construct( array(
			'singular'	=> 	'wpdevtool_cron_field',
			'plural' 	=> 	'wpdevtool_cron_fields',
			'ajax'		=> 	false
		) );
	}
	
	/**
	 * Add search field at the top of the table
	 *
	 * @param string $position, helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $position ) {
		
		if ( $position != "top" )
			return;
		
		$value = '';
		if ( isset( $_POST['wpdevtool_search'] ) )
			$value = stripslashes( esc_attr( $_POST['wpdevtool_search'] ) ) ;
		
		echo '<form class="wpdevtool_search" action="'.remove_query_arg( 'paged' ).'" method="post"><input type="text" name="wpdevtool_search" value="'. $value .'"> <input type="hidden" name="wpdevtool_search_field" value="rule"><input type="submit" value="'.__('Search').'" class="button-primary"> <input type="button" onclick="parent.location=\'' . remove_query_arg( array( 'paged' ) ) . '\'" class="button-secondary" value="'.__('Reset').'"></form>';
		
	}
	
	/**
	 * Get permalinks data
	 * 
	 * @since 0.1.0
	 */
	function wpdevtool_get_data() {
	
		if( !class_exists( 'WP_List_Table' ) )
			require_once( ABSPATH . 'wp-includes/rewrite.php' );
			
		global $wp_rewrite;
		
		$wp_rewrite->rewrite_rules();
		
		$permalinks_table = array();
		if ( !empty( $wp_rewrite->rules ) ) {
			foreach ( $wp_rewrite->rules as $rule => $rewrite ) {
				$permalinks_table[] = array( 'rule' => $rule, 'rewrite' => $rewrite );
			}
		}
		return $permalinks_table;
	}
	
	/**
	 * Define the columns that are going to be used in the table
	 *
	 * @since 0.1.0
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns = array(
			'col_permalink_name'	=>	__( 'Rule', 'wpdevtool' ),
			'col_permalink_value'	=>	__( 'Rewrite', 'wpdevtool' )
		);
	}
	
	/**
	 * Display the rows of the table
	 *
	 * @since 0.1.0
	 * @return string, echo the markup of the rows
	 */
	function display_rows() {
	
		$records = $this->items;
		$columns = $this->get_columns();
		
		if( !empty( $records ) ){
			foreach( $records as $rec ){
		        echo '<tr class="record">';
		 		foreach ( $columns as $column_name => $column_display_name ) {
		 			$class = "class='$column_name column-$column_name'";
		 			switch ( $column_name ) {
		 				case "col_permalink_name": 
		 					echo '<td ' . $class . '>' . $rec['rule'] . '</td>'; 
		 					break;
		 				case "col_permalink_value": 
		 					echo '<td ' . $class . '><strong>' . $rec['rewrite'] . '</strong></td>'; 
		 					break;
		 			}
		 		}
	 		echo'</tr>';
			}
		}
	}
	
}

/**
 * WpDevTool Main Admin Page
 *
 * @since 0.1.0
 */
function wpdevtool_permalinks_page() {

	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	?>
	
	<!-- Crons page -->
	<div class="wrap wpdevtool">
		
		<div class="icon32 icon-wpdevtool-32"><br></div>
		<h2><strong class="wpdevtool_logo">WpDevTool</strong> - <?php _e( 'Permalinks Table', 'wpdevtool' ); ?></h2>
		
		<!-- Container -->
		<div id="wpdevtool_container">
		
			<!-- Left column -->
			<div id="wpdevtool_left_column">
				<?php  
				$wpdevtool_permalinks_table = new Wpdevtool_Permalinks_Table();
				$wpdevtool_permalinks_table->prepare_items();
				$wpdevtool_permalinks_table->display();
				?>
			</div>
			
			<!-- Right column -->
			<div id="wpdevtool_right_column">
				<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ) ?>
			</div>
			
		</div>
		
	</div>
	<?php

}