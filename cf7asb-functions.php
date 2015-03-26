<?php

function wpcf7asb_plugin_url( $path = '' ) {
	$url = CF7ASB_PLUGIN_URL;

	if ( ! empty( $path ) && is_string( $path ) && false === strpos( $path, '..' ) )
		$url .= '/' . ltrim( $path, '/' );

	return $url;
}

// key generate
function wpcf7asb_keygen(){
	
    $ret = '';
    $ipoct_arr = explode('.',$_SERVER['REMOTE_ADDR']);
    // a-seg
    $value_a=wpcf7asb_make_seg_a();

    // b-seg
    $value_b=substr(time(), -5);
    
    // c-seg
    $value_c=wpcf7asb_make_seg_c($value_a, $value_b);

    $ret = $value_a.SZMSF_KEYSEP.$value_b.SZMSF_KEYSEP.$value_c;

    return $ret;

}

// make key of segment a 
function wpcf7asb_make_seg_a(){
	$ipoct_arr = explode('.',$_SERVER['REMOTE_ADDR']);
    // a-seg
    $value_a=0;
    foreach($ipoct_arr as $ipoct){
    	$value_a+=intval($ipoct);
    }
    return $value_a;
}

// make key of segment c 
function wpcf7asb_make_seg_c($value_a, $value_b){
	global $wpcf7asb_settings;
	return substr( md5($value_a+$value_b+$wpcf7asb_settings['allow_trackbacks']), -3);
}

// key check
function wpcf7asb_keychk($chk_key){
	$ret = false;
	if( ! empty($chk_key) ){
		$arr = explode(SZMSF_KEYSEP, trim($chk_key));
		if(count($arr)==3){
			$req_a=$arr[0];
			$req_b=$arr[1];
			$req_c=$arr[2];
			if( $req_c==wpcf7asb_make_seg_c($req_a, $req_b) ){
				if($req_a==wpcf7asb_make_seg_a()){
					$keytm = time();
					$keytm = intval( substr($keytm, 0, (-1)*strlen($req_b) ).$req_b );
					if( ( $keytm >= time() - 180 ) // before gen 180 sec
						 &&
						( $keytm <= time() )
					  ){
						$ret = true;
					}
				}
			}
		}
	}
    return $ret;
}

// regist log.
function wpcf7asb_reglog($spam_req) {


	$wpcf7asb_data = get_option('wpcf7asb_data', array());
	
	// logdata set
	if (array_key_exists('next_log_idx', $wpcf7asb_data)){
		$next_idx = intval($wpcf7asb_data['next_log_idx']);
	} else {
		$next_idx = 0;
	}
	$wpcf7asb_data['logdat_'.strval($next_idx)] = serialize($spam_req);
	
	// next idx set
	$next_idx++;
	if($next_idx>9){
		$next_idx=0;
	}
	$wpcf7asb_data['next_log_idx'] = $next_idx;

	// count set
	if (array_key_exists('blocked_count', $wpcf7asb_data)){
		$wpcf7asb_data['blocked_count']++;
	} else {
		$wpcf7asb_data['blocked_count'] = 1;
	}
	// update
	update_option('wpcf7asb_data', $wpcf7asb_data);
}

function wpcf7asb_get_logcount() {
	$wpcf7asb_data = get_option('wpcf7asb_data', array());
	if ( array_key_exists('blocked_count', $wpcf7asb_data) ){
		$blocked_count = $wpcf7asb_data['blocked_count'];
	} else {
		$blocked_count = 0;
	}
	return $blocked_count;
}

function wpcf7asb_get_loglist() {
	$wpcf7asb_data = get_option('wpcf7asb_data', array());
	
	$ret_array = array();

	if (	( ! array_key_exists('next_log_idx', $wpcf7asb_data) )
			||
			( ! array_key_exists('blocked_count', $wpcf7asb_data) )
			){
		return $ret_array;
	}
	$blkcnt = intval($wpcf7asb_data['blocked_count']);
	$nxlidx = intval($wpcf7asb_data['next_log_idx']);


	$idx=0;
	if( 10 > $blkcnt  ){
		for($datidx=$blkcnt-1;$datidx>=0;$datidx--){
			$keyname = 'logdat_'.strval($datidx);
			if( array_key_exists($keyname, $wpcf7asb_data) ){
				$ret_array[$idx]=unserialize($wpcf7asb_data[$keyname]);
				$idx++;
			}
		}
	} else {
		$datidx = $nxlidx;
		do {
			$datidx--;
			if($datidx<0){
				$datidx=9;
			}

			$keyname = 'logdat_'.strval($datidx);
			if( array_key_exists($keyname, $wpcf7asb_data) ){
				$ret_array[$idx]=unserialize($wpcf7asb_data[$keyname]);
				$idx++;
			}
		} while($datidx!=$nxlidx);
	}

	return $ret_array;
	
}

function wpcf7asb_clear_logdata() {
	$wpcf7asb_data = get_option('wpcf7asb_data', array());
	
	$wpcf7asb_data['next_log_idx']=0;
	$wpcf7asb_data['blocked_count']=0;
	
	for($datidx=0;$datidx<=9;$datidx++){
		$keyname = 'logdat_'.strval($datidx);
		if( array_key_exists($keyname, $wpcf7asb_data) ){
			unset($wpcf7asb_data[$keyname]);
		}
	}

	// update
	update_option('wpcf7asb_data', $wpcf7asb_data);

}
