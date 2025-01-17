<?php

/* Recorded file */
function formatFiles($row) {
	global $system_monitor_dir, $system_fax_archive_dir, $system_audio_format, $system_arch_audio_format;

	/* File name formats, please specify: */
	
	/* 
		caller-called-timestamp.wav 
	*/
	/* 
	$recorded_file = $row['src'] .'-'. $row['dst'] .'-'. $row['call_timestamp']
	*/
	/* ============================================================================ */	

	/* 
		ends at the uniqueid.wav, for example: 
												date-time-uniqueid.wav 
	
		thanks to Beto Reyes
	*/
	/*
	$recorded_file = glob($system_monitor_dir . '/*' . $row['uniqueid'] . '.' . $system_audio_format);
	if (count($recorded_file)>0) {
		$recorded_file = basename($recorded_file[0],".$system_audio_format");
	} else {
		$recorded_file = $row['uniqueid'];
	}
	*/
	/* ============================================================================ */

	/*      This example for multi-directory archive without uniqueid, named by format:
			<date>/<time>_<caller>-<destination>.<filetype>

			example: (tree /var/spool/asterisk/monitor)

		|-- 2012.09.12
		|   |-- 10-37_4952704601-763245.ogg
		|   `-- 10-43_106-79236522173.ogg
		`-- 2012.09.13
			|-- 11-42_101-79016410692.ogg
			|-- 12-43_104-671554.ogg
			`-- 15-49_109-279710.ogg

		Added by BAXMAH (pcm@ritm.omsk.ru)
	*/
	/*
	   $record_datetime = DateTime::createFromFormat('Y-m-d G:i:s', $row['calldate']);

	   $recorded_file = date_format($record_datetime, 'Y.m.d/G-i') .'_'. $row['src'] .'-'. $row['dst'];
	*/
	/* ============================================================================ */

	/*
		This is a multi-dir search script for filenames like "/var/spool/asterisk/monitor/dir1/dir2/dir3/*uniqueid*.*"
		Doesn't matter, WAV, MP3 or other file format, only UNIQID  is  required at the end of the filename 
		;---------------------------------------------------------------------------  
	   example: (tree /var/spool/asterisk/monitor)

    |-- in
    |   |-- 4951234567
    |   |   `-- 20120101_234231_4956401234_to_74951234567_1307542950.0.wav
    |   `-- 4997654321
    |       `-- 20120202_234231_4956401234_to_74997654321_1303542950.0.wav
    `-- out
        |-- msk
        |   `-- 20120125_211231_4956401234_to_74951234567_1307542950.0.wav
        `-- region
            `-- 20120112_211231_4956405570_to_74952210533_1307542950.0.wav

      6 directories, 4 files
		;----------------------------------------------------------------------------
	   added by Dein admin@sadmin.ru         
	*/
	
	/*
	//
	// Set $cdr_suppress_download_links = 1 in config.inc.php if you don't want to find files, because 
	// of some performance issue with this method. To show file you shall to select checkbox in GUI.
	//
	if ( isset($cdr_suppress_download_links) and $cdr_suppress_download_links ) {
		if ( empty($_REQUEST['show_download_links']) || $_REQUEST['show_download_links'] != 'true' ) {
			echo "<td>&nbsp;</td>\n";
			return;
		}
	}

	//************ Get a list of subdirectories as array to search by glob function  **************
	if (!function_exists('get_dir_list')) {
		function get_dir_list($dir){
			global $dirlist;			
			$dirlist=array();
			if (!function_exists('find_dirs_recursive')) {
				function find_dirs_recursive($sdir) {
					global $dirlist;
					foreach(glob($sdir) as $filename) {
						//echo $filename;
						if(is_dir($filename)) {
							$dirlist[]=$filename;
							find_dirs_recursive($filename."/*");
						};//endif
					};//endforeach
				}; //endfunc                                                                                               
			};//endif exists
			find_dirs_recursive($dir."/*");
		};//endfunc
	}

	//*************** Main function  ************
	if (!function_exists('find_record_by_uniqid')) {
		function find_record_by_uniqid($path,$uniqid){
			global $dirlist;
			if (sizeof($dirlist) == 0 ){
				get_dir_list($path);
			};//endif size==0

			if (sizeof($dirlist) == 0 ) {return "SOME ERROR, dirlist is empty";};

			$found = "NOTHING FOUND";
			foreach ($dirlist as $curdir) {
				$res=glob($curdir."/*".$uniqid.".*");
				if ($res) {$found=$res[0]; break;};
			};//endforeach

			$res=str_replace($path,"",$found);	//cut $path from full filename 
			
			return $res;			//to be compartable with func. formatFiles($row)

		};//endfunc
	}
	
	$recorded_file = find_record_by_uniqid($system_monitor_dir,$row['uniqueid']);
	
	*/
	/* ============================================================================ */

	/* 
		uniqueid.wav 
	*/
	$recorded_file = $row['uniqueid'];
	//field added by Issabel and FreePbx
	$path_recorded_file=$row['recordingfile'];
	if($path_recorded_file!=""){
		//	recordingfile into freepbx PBX
		//	in-052241710-952662636-20230701-112836-1688228915.14.wav
		//
		// 	recordingfile into issabel PBX
		// 	/var/spool/asterisk/monitor/in-052241710-952662636-20230701-112836-1688228915.14.wav
		$tmp=explode($system_monitor_dir,$path_recorded_file);
	
		if(count($tmp)==1){//freepbx
			$p=explode("-",$path_recorded_file);
			$anio=substr($p[3],0,4);
			$mes=substr($p[3],4,2);
			$dia=substr($p[3],6,2);
			$path_recorded_file="$system_monitor_dir/$anio/$mes/$dia/$path_recorded_file";
		}
		else{//issabel
		}		
	}
	/* ============================================================================ */	

	if (file_exists("$system_monitor_dir/$recorded_file.$system_audio_format")) {
		echo "    <td class=\"record_col\"><a href=\"download.php?audio=$recorded_file.$system_audio_format\" title=\"Listen to call recording\"><img src=\"templates/images/sound.png\" alt=\"Call recording\" /></a></td>\n";
	} elseif ( isset($system_arch_audio_format) and file_exists("$system_monitor_dir/$recorded_file.$system_audio_format.$system_arch_audio_format")) {
		echo "    <td class=\"record_col\"><a href=\"download.php?arch=$recorded_file.$system_audio_format.$system_arch_audio_format\" title=\"Download archive\"><img src=\"templates/images/compressed.png\" alt=\"Call recording\" /></a></td>\n";
	} elseif (file_exists("$system_fax_archive_dir/$recorded_file.tif")) {
		echo "    <td class=\"record_col\"><a href=\"download.php?fax=$recorded_file.tif\" title=\"View FAX image\"><img src=\"templates/images/text.png\" alt=\"FAX image\" /></a></td>\n";
	} elseif (file_exists("$system_monitor_dir/$recorded_file")) {
		echo "    <td class=\"record_col\"><a href=\"download.php?audio=$recorded_file\" title=\"Listen to call recording\"><img src=\"templates/images/sound.png\" alt=\"Call recording\" /></a></td>\n";
	} elseif (file_exists("$path_recorded_file")) {
		echo "    <td class=\"record_col\"><img onclick=\"clickImageAudio(this,'".$path_recorded_file."')\" src=\"templates/images/sound.png\" alt=\"Call recording\" /></td>\n";
	}
	else {
		echo "    <td class=\"record_col\"></td>\n";
	}
}

/* CDR Table Display Functions */
function formatCallDate($calldate,$uniqueid) {
	echo "    <td class=\"record_col\"><abbr title=\"UniqueID: $uniqueid\">$calldate</abbr></td>\n";
}

function formatChannel($channel) {
	$trunk_name = preg_replace('/(.*)-[^-]+$/','$1',$channel);
	echo "    <td class=\"record_col\"><abbr title=\"Channel: $channel\">$trunk_name</abbr></td>\n";
}

function formatClid($clid) {
	$clid_only = explode(' <', $clid, 2);
	$clid = htmlspecialchars($clid_only[0]);
	echo "    <td class=\"record_col\">$clid</td>\n";
}

function formatSrc($src,$clid) {
	if (empty($src)) {
		echo "    <td class=\"record_col\">UNKNOWN</td>\n";
	} else {
		$src = htmlspecialchars($src);
		$clid = htmlspecialchars($clid);
		echo "    <td class=\"record_col\"><abbr title=\"Caller*ID: $clid\">$src</abbr></td>\n";
	}
}

function formatApp($app, $lastdata) {
	echo "    <td class=\"record_col\"><abbr title=\"Application: $app($lastdata)\">$app</abbr></td>\n";
}

function formatDst($dst, $dcontext) {
	global $rev_lookup_url;
	if (strlen($dst) == 11 and strlen($rev_lookup_url) > 0 ) {
		$rev = str_replace('%n', $dst, $rev_lookup_url);
		echo "    <td class=\"record_col\"><abbr title=\"Destination Context: $dcontext\"><a href=\"$rev\" target=\"reverse\">$dst</a></abbr></td>\n";
	} else {
		echo "    <td class=\"record_col\"><abbr title=\"Destination Context: $dcontext\">$dst</abbr></td>\n";
	}
}

function formatDisposition($disposition, $amaflags) {
	switch ($amaflags) {
		case 0:
			$amaflags = 'DOCUMENTATION';
			break;
		case 1:
			$amaflags = 'IGNORE';
			break;
		case 2:
			$amaflags = 'BILLING';
			break;
		case 3:
		default:
			$amaflags = 'DEFAULT';
	}
	if ( $disposition == 'ANSWERED' ) {
		echo "    <td class=\"record_col\"><abbr title=\"AMA Flag: $amaflags\"><span class='green'>$disposition</span></abbr></td>\n";
	} else {
		echo "    <td class=\"record_col\"><abbr title=\"AMA Flag: $amaflags\"><span class='red'>$disposition</span></abbr></td>\n";
	}
}

function formatDuration($duration, $billsec) {
	$duration = sprintf('%02d', intval($duration/60)).':'.sprintf('%02d', intval($duration%60));
	$billduration = sprintf('%02d', intval($billsec/60)).':'.sprintf('%02d', intval($billsec%60));
	echo "    <td class=\"record_col\"><abbr title=\"Billing Duration: $billduration\">$duration</abbr></td>\n";
}

function formatBillSec($billsec) {
	$sec = sprintf('%02d', intval($billsec/60)).':'.sprintf('%02d', intval($billsec%60));
	echo "    <td class=\"record_col\">$sec</td>\n";
}

function formatUserField($userfield) {
	echo "    <td class=\"record_col\">$userfield</td>\n";
}

function formatAccountCode($accountcode) {
	echo "    <td class=\"record_col\">$accountcode</td>\n";
}

/* Asterisk RegExp parser */
function asteriskregexp2sqllike( $source_data, $user_num ) {
	$number = $user_num;
	if ( strlen($number) < 1 ) {
		$number = $_REQUEST[$source_data];
	}
	if ( '__' == substr($number,0,2) ) {
		$number = substr($number,1);
	} elseif ( '_' == substr($number,0,1) ) {
		$number_chars = preg_split('//', substr($number,1), -1, PREG_SPLIT_NO_EMPTY);
		$number = '';
		foreach ($number_chars as $chr) {
			if ( $chr == 'X' ) {
				$number .= '[0-9]';
			} elseif ( $chr == 'Z' ) {
				$number .= '[1-9]';
			} elseif ( $chr == 'N' ) {
				$number .= '[2-9]';
			} elseif ( $chr == '.' ) {
				$number .= '.+';
			} elseif ( $chr == '!' ) {
				$_REQUEST[ $source_data .'_neg' ] = 'true';
			} else {
				$number .= $chr;
			}
		}
		$_REQUEST[ $source_data .'_mod' ] = 'asterisk-regexp';
	}
	return $number;
}

/* empty() wrapper. Thanks to Mikael Carlsson. */
function is_blank($value) {
	return empty($value) && !is_numeric($value);
}

/* 
	Money format

	thanks to Shiena Tadeo
*/ 
function formatMoney($number, $cents = 2) { // cents: 0=never, 1=if needed, 2=always
	global $callrate_currency;
	if (is_numeric($number)) { // a number
		if (!$number) { // zero
			$money = ($cents == 2 ? '0.00' : '0'); // output zero
		} else { // value
			if (floor($number) == $number) { // whole number
				$money = number_format($number, ($cents == 2 ? 2 : 0)); // format
			} else { // cents
				$money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
			} // integer or decimal
		} // value
		echo   "<td class=\"chart_data\">$callrate_currency<span>$money</span></td>\n";
	} else {
		echo   "<td class=\"chart_data\">&nbsp;</td>\n";
	}
} // formatMoney

/* 
	CallRate
	return callrate array [ areacode, rate, description, bill type, total_rate] 
*/
function callrates($dst,$duration,$file) {
	global $callrate_csv_file, $callrate_cache;

	if ( strlen($file) == 0 ) {
		$file = $callrate_csv_file;
		if ( strlen($file) == 0 ) {
			return array('','','','','');
		}
	}
	
	if ( ! array_key_exists( $file, $callrate_cache ) ) {
		$callrate_cache[$file] = array();
		$fr = fopen($file, "r") or die("Can not open callrate file ($file).");
		while(($fr_data = fgetcsv($fr, 1000, ",")) !== false) {
			$callrate_cache[$file]["$fr_data[0]"] = array( $fr_data[1], $fr_data[2], $fr_data[3] );
		}
		fclose($fr);
	}

	for ( $i = strlen($dst); $i > 0; $i-- ) {
		if ( array_key_exists( substr($dst,0,$i), $callrate_cache[$file] ) ) {
			$call_rate = 0;
			if ( $callrate_cache[$file][substr($dst,0,$i)][2] == 's' ) {
				// per second
				$call_rate = $duration * ($callrate_cache[$file][substr($dst,0,$i)][0] / 60);
			} elseif ( $callrate_cache[$file][substr($dst,0,$i)][2] == 'c' ) {
				// per call
				$call_rate = $callrate_cache[$file][substr($dst,0,$i)][0];
			} elseif ( $callrate_cache[$file][substr($dst,0,$i)][2] == '1m+s' ) {
				// 1 minute + per second
				if ( $duration < 60) {
					$call_rate = $callrate_cache[$file][substr($dst,0,$i)][0];
				} else {
					$call_rate = $callrate_cache[$file][substr($dst,0,$i)][0] + ( ($duration-60) * ($callrate_cache[$file][substr($dst,0,$i)][0] / 60) );
				}
			} elseif ( $callrate_cache[$file][substr($dst,0,$i)][2] == '30s+s' ) {
				// Thanks to `celosam` 
				// 30 second + per second
				if ( $duration > 0 && $duration <= 30) {
					$call_rate = ($callrate_cache[$file][substr($dst,0,$i)][0] / 2);
				} elseif ( $duration > 30 && $duration < 60) {
					$call_rate = ($callrate_cache[$file][substr($dst,0,$i)][0] / 2) + ( ($duration-30) * ($callrate_cache[$file][substr($dst,0,$i)][0] / 60) );
				} else {
					$call_rate = $callrate_cache[$file][substr($dst,0,$i)][0] + ( ($duration-60) * ($callrate_cache[$file][substr($dst,0,$i)][0] / 60) );
				}
			} elseif ( $callrate_cache[$file][substr($dst,0,$i)][2] == '30s+6s' ) {
				// Thanks to `celosam` 
				// 30 second + 6 second
				if ( $duration > 0 && $duration <= 30) {
					$call_rate = ($callrate_cache[$file][substr($dst,0,$i)][0] / 2);
				} else {
					$call_rate = ceil($duration / 6) * ($callrate_cache[$file][substr($dst,0,$i)][0] / 10);
				}
			} else {
				//( $callrate_cache[substr($dst,0,$i)][2] == 'm' ) {
				// per minute
				$call_rate = intval($duration/60);
				if ( $duration%60 > 0 ) {
					$call_rate++;
				}
				$call_rate = $call_rate*$callrate_cache[$file][substr($dst,0,$i)][0];
			}
			return array(substr($dst,0,$i),$callrate_cache[$file][substr($dst,0,$i)][0],$callrate_cache[$file][substr($dst,0,$i)][1],$callrate_cache[$file][substr($dst,0,$i)][2],$call_rate);
		}
	}

	return array (0,0,'unknown','unknown',0);
}

?>
