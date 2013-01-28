<?php
/**
 * Include WP_List_Table Class to be used inside admin pages
 * 
 * @since 0.1.0
 */
if( !class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * Add some useful methods to WP_List_Table
 * 
 * @since 0.1.0
 */
class WDT_Table extends WP_List_Table {
	
	/**
	 * Table pagination
	 * 
	 * @since 0.1.0
	 * @param array $data Array data to count
	 * @param int $perpage Number of rows per page
	 * @return int offset
	 */
	function wpdevtool_paginate( $data, $perpage ) {
		
		$totalitems = count( $data );
		
		$paged = !empty( $_GET["paged"] ) ? mysql_real_escape_string( $_GET["paged"] ) : '';
		
		if ( empty( $paged ) || !is_numeric( $paged ) || $paged <= 0 ) 
			$paged = 1;
			
		$totalpages = ceil( $totalitems / $perpage );
		
		$this->set_pagination_args( array(
			"total_items"	=> 	$totalitems,
			"total_pages" 	=> 	$totalpages,
			"per_page" 		=> 	$perpage,
		) );
		
		if ( !empty( $paged ) && !empty( $perpage ) )
			return ( $paged - 1 ) * $perpage; // offset
		
		return 0;
		
	}
	 
	 /**
	  * Prepare the items to be displayed
	  * 
	  * @since 0.1.0
	  */
	 function prepare_items() {
	 	global $_column_headers;
	 
	 	$data = $this->wpdevtool_get_data(); 
	 	
	 	// Search in a given field inside the array
	 	if ( isset( $_POST['wpdevtool_search'] ) && !empty( $_POST['wpdevtool_search'] ) && isset( $_POST['wpdevtool_search_field'] ) && !empty( $data ) && isset( $data[0][ $_POST['wpdevtool_search_field'] ] ) ){
	 		$search_result = array();
	 		foreach ( $data as $value ) {
	 			if ( false !== strrpos( $value[ $_POST['wpdevtool_search_field'] ], $_POST['wpdevtool_search'] ) )
	 				$search_result[] = $value;
	 		}
	 		$data = $search_result;
	 	}
	 		
	 	$perpage = 20;
		$offset = $this-> wpdevtool_paginate( $data, $perpage );
 		
 		$this->_column_headers[0] = $this->get_columns();
 		$this->_column_headers[1] = array();
 		$this->_column_headers[2] = array();
		
 		$this->items = array_slice( $data, $offset, $perpage );
	 }
	 
}