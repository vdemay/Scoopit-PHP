<?php 

if ( !function_exists( 'property_exists' ) ) {
	function property_exists( $class, $property ) {
		if ( is_object( $class ) ) {
			$vars = get_object_vars( $class );
		} else {
			$vars = get_class_vars( $class );
		}
		return array_key_exists( $property, $vars );
	}
}

if ( !function_exists( 'getDomain' ) ) {
	function getDomain($url)
	{
		$nowww = ereg_replace('www\.','',$url);
		$domain = parse_url($nowww);
		if(!empty($domain["host"]))
		{
			return $domain["host"];
		} else
		{
			return $domain["path"];
		}
	
	}
}

if ( !function_exists( 'mySerialize' ) ) {
	function mySerialize( $obj ) {
		return base64_encode(gzcompress(serialize($obj)));
	}
}


if ( !function_exists( 'myUnserialize' ) ) {
	function myUnserialize( $txt ) {
		return unserialize(gzuncompress(base64_decode($txt)));
	}
}

?>