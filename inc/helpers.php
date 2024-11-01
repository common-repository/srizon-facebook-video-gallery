<?php
if ( ! function_exists( 'srizon_view' ) ) {
	function srizon_view( $file, $data_array ) {
		extract( $data_array );
//		var_dump($videos);
		if ( is_file( $file ) ) {
			ob_start();
			include( $file );

			return ob_get_clean();
		}

		return '';
	}
}

if ( ! function_exists( 'dump_keys' ) ) {
	function dump_keys( $params ) {
		foreach ( $params as $key => $value ) {
			echo $key . '<br>';
		}
	}

}
