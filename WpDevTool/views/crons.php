<?php
/**
 * WpDevTool Crons Page
 *
 * @since 0.0.1
 */
function wpdevtool_menu_crons_page() {

	$page = add_submenu_page( 'wpdevtool_admin', __( 'WpDevTool Crons', 'wpdevtool' ), __( 'Crons Table', 'wpdevtool' ), 'manage_options', 'wpdevtool_crons', 'wpdevtool_crons_page' );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_crons_page_styles' );
	
}
add_action( 'admin_menu', 'wpdevtool_menu_crons_page' );

/**
 * WpDevTool Crons Page Styles
 *
 * @since 0.1.0
 */
function wpdevtool_crons_page_styles() {
	
	wp_enqueue_style( 'WpDevToolStylesheet' );
	
}

/**
 * WpDevTool Crons Table Class
 *
 * @since 0.0.1
 */
class Wpdevtool_Cron_Table extends WDT_Table {
	
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
		
		echo '<form class="wpdevtool_search" action="'.remove_query_arg( 'paged' ).'" method="post"><input type="text" name="wpdevtool_search" value="'. $value .'"> <input type="hidden" name="wpdevtool_search_field" value="name"><input type="submit" value="'.__('Search').'" class="button-primary"> <input type="button" onclick="parent.location=\'' . remove_query_arg( array( 'paged' ) ) . '\'" class="button-secondary" value="'.__('Reset').'"></form>';
		
	}
	
	/**
	 * Get crons data
	 * 
	 * @since 0.1.0
	 */
	function wpdevtool_get_data() {
		$crons = _get_cron_array();
		$crons_table = array();
		if ( !empty( $crons ) ){
			foreach ( $crons as $data => $cron_by_date ) {
				foreach ( $cron_by_date as $cron_name => $single_cron ) {		
					foreach ( $single_cron as $cron ) {
						$crons_table[] = array_merge( array( 'date' => date_i18n( 'd/m/Y H:i:s', $data ), 'name' => $cron_name ), $cron );
					}
				}
			}
		}
		return $crons_table;
	}
	
	/**
	 * Define the columns that are going to be used in the table
	 *
	 * @since 0.1.0
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns = array(
			'col_cron_date'			=>	__( 'Date', 'wpdevtool' ),
			'col_cron_name'			=>	__( 'Name', 'wpdevtool' ),
			'col_cron_schedule'		=>	__( 'Schedule', 'wpdevtool' ),
			'col_cron_args'			=>	__( 'Arguments', 'wpdevtool' ),
			'col_cron_interval'		=>	__( 'Interval', 'wpdevtool' ),
			'col_cron_delete'		=>	__( 'Delete Cron', 'wpdevtool' )
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
		        echo '<tr id="record_' . $rec['name'] . '">';
		 		foreach ( $columns as $column_name => $column_display_name ) {
		 			$class = "class='$column_name column-$column_name'";
		 			switch ( $column_name ) {
		 				case "col_cron_date": 
		 					echo '<td ' . $class . '>' . $rec['date'] . '</td>'; 
		 					break;
		 				case "col_cron_name": 
		 					echo '<td ' . $class . '><strong>' . $rec['name'] . '</strong></td>'; 
		 					break;
		 				case "col_cron_schedule":
		 					if ( isset( $rec['schedule'] ) && !empty( $rec['schedule'] ) ){
		 						$schedule = $rec['schedule'];
		 					} else {
		 						$schedule = __( 'once', 'wpdevtool' );
		 					}
		 					echo '<td ' . $class . '>' . $schedule . '</td>'; 
		 					break;
		 				case "col_cron_args":
		 					if ( empty( $rec['args'] ) ){
		 						$args = '-';
		 					} else {
		 						$args = '';
		 						foreach ( $rec['args'] as $key => $arg ) {
		 							$args .= $arg . ', ';
		 						}
		 						$args = substr( $args, 0, -2 );
		 					}
		 					echo '<td '. $class . '>' . $args . '</td>'; 
		 					break;
		 				case "col_cron_interval":
		 					if ( isset( $rec['interval'] ) && is_int( $rec['interval'] ) ){
		 						$interval = ( $rec['interval'] / 3600 ) . ' ' . __( 'hours', 'wpdevtool' );
		 					} else {
		 						$interval = '-';
		 					}
		 					echo '<td ' . $class . '>' . $interval . ' </td>'; 
		 					break;
		 				case "col_cron_delete":
		 					$nonce = wp_create_nonce( 'wdt_cron_delete' );
		 					echo '<td ' . $class . '><a href="' . add_query_arg( array( 'wpdevtool_cron_to_delete' => $rec['name'], 'wpdevtool_cron_args_to_delete' => $rec['args'], 'wdt_nonce' => $nonce ) ) . '" class="button button-primary">' . $column_display_name . '</a></td>'; 
		 					break;
		 			}
		 		}
	 		echo'</tr>';
			}
		}
	}
	
}

/**
 * WpDevTool Crons Admin Page
 *
 * @since 0.1.0
 */
function wpdevtool_crons_page() {

	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	?>
	
	<!-- Crons page -->
	<div class="wrap wpdevtool">
		
		<div class="icon32 icon-wpdevtool-32"><br></div>
		<h2><strong class="wpdevtool_logo">WpDevTool</strong> - <?php _e( 'Table of programmed Crons', 'wpdevtool' ); ?></h2>
		
		<!-- Container -->
		<div id="wpdevtool_container">
		
			<!-- Left column -->
			<div id="wpdevtool_left_column">
				<?php  
				$wpdevtool_cron_table = new Wpdevtool_Cron_Table();
				$wpdevtool_cron_table->prepare_items();
				$wpdevtool_cron_table->display();
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
