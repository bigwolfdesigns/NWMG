<?php

class format{
	private $formats = array();
	public function __construct(){
		$this->formats = lc('config')->get_and_unload_config('format');
	}
	public function date($time = NULL, $format = NULL){
		if(is_null($time)) $time	 = time();
		if(!is_numeric($time)) $time	 = strtotime($time);
		if(is_null($format)) $format	 = $this->formats['date'];
		if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
			$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
		}
		return strftime($format, $time);
	}
	public function datetime($time = NULL, $format = NULL){
		if(is_null($time)) $time	 = time();
		if(!is_numeric($time)) $time	 = strtotime($time);
		if(is_null($format)) $format	 = $this->formats['datetime'];
		if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
			$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
		}
		return strftime($format, $time);
	}
	public function time($time = NULL, $format = NULL){
		if(is_null($time)) $time	 = time();
		if(!is_numeric($time)) $time	 = strtotime($time);
		if(is_null($format)) $format	 = $this->formats['time'];
		if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
			$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
		}
		return strftime($format, $time);
	}
	public function number_format($number, $decimal = 0, $dec_separator = NULL, $thousands_separator = NULL){
		if($dec_separator === NULL) $dec_separator		 = $this->formats['dec_separator'];
		if($thousands_separator === NULL) $thousands_separator = $this->formats['thousands_separator'];
		return number_format($number, $decimal, $dec_separator, $thousands_separator);
	}
	public function number($number, $decimal = 0, $dec_separator = NULL, $thousands_separator = NULL){
		return $this->number_format($number, $decimal, $dec_separator, $thousands_separator);
	}
	//todo: make this smarter
	public function phone($num, $digitlength){
		if($digitlength == 7) return substr($num, 0, 3).'-'.substr($num, 3);
		else return $num;
	}
	public function boolean($value, $if_true = 'True', $if_false = 'False', $extra_true = array(), $extra_false = array()){
		if(!is_array($extra_true)) $extra_true	 = array($extra_true);
		if(!is_array($extra_false)) $extra_false = array($extra_false);
		return (((bool)$value || in_array($value, $extra_true)) && !in_array($value, $extra_false))?$if_true:$if_false;
	}
	public function percent($number, $decimals = 0, $sign = '%'){
		if(is_null($sign)) $sign		 = $this->formats['percent_sign'];
		if(is_null($decimals)) $decimals	 = $this->formats['percent_decimals'];
		return $this->number_format($number, $decimals).$sign;
	}
	public function bytes($bytes){
		//alias to num_to_bytes
		return $this->num_to_bytes($bytes);
	}
	public function num_to_bytes($bytes){
		if($bytes == 0) return '0 b';
		$sign	 = $bytes < 0?'-':'';
		$bytes	 = abs($bytes);
		if($bytes / 1024 < 1){
			$return = $this->number_format($bytes).' b';
		}elseif(($bytes / pow(1024, 2)) < 1){
			$return = $this->number_format($bytes / 1024, 2).' Kb';
		}elseif(($bytes / pow(1024, 3)) < 1){
			$return = $this->number_format($bytes / pow(1024, 2), 2).' Mb';
		}elseif(($bytes / pow(1024, 4)) < 1){
			$return = $this->number_format($bytes / pow(1024, 3), 2).' Gb';
		}elseif(($bytes / pow(1024, 5)) < 1){
			$return = $this->number_format($bytes / pow(1024, 4), 2).' Tb';
		}else{
			$return = $this->number_format($bytes / pow(1024, 5), 2).' Pb';
		}
		return $sign.$return;
	}
	/**
	 * This function transforms bytes (like in the the php.ini notation) for numbers (like '2M') to an integer (2*1024*1024 in this case)
	 */
	public function bytes_to_num($bytes){
		$bytes	 = trim($bytes);
		$ret	 = $bytes + 0;
		if($ret == 0 || strlen($ret) >= strlen($bytes)){
			return $ret;
		}
		$type = substr($bytes, strlen($ret));
		switch(strtoupper($type)){
			case 'P':
			case 'Pb':
				$ret *= 1024;
			case 'T':
			case 'Tb':
				$ret *= 1024;
			case 'G':
			case 'Gb':
				$ret *= 1024;
			case 'M':
			case 'Mb':
				$ret *= 1024;
			case 'K':
			case 'Kb':
				$ret *= 1024;
				break;
		}
		return $ret;
	}
	public function secs($secs){
		if($secs == 0) return '0';
		$d	 = (int)($secs / (24 * 3600));
		$secs -= $d * 24 * 3600;
		$h	 = (int)($secs / 3600);
		$secs -= $h * 3600;
		$m	 = (int)($secs / 60);
		$secs -= $m * 60;
		$s	 = (int)$secs;
		$secs -= $s;
		$ms	 = $secs - (int)$secs;

		$return = '';
		if($d > 0) $return .= $d.' days and ';
		if($d > 0 || $h > 0) $return .= substr('00'.$h, -2).':';
		if($d > 0 || $h > 0 || $m > 0) $return .= substr('00'.$m, -2).':';
		$return .= substr('00'.$s, -2);
		if($ms > 0) $return .= '.'.substr($ms, -3);

		return $return;
	}
	public function text($text, $width, $cont_char = '...', $if_empty = '', $htmlchar = false){
		if($text == '') $text = $if_empty;
		if(strlen($text) > $width){
			if($width > strlen(trim($cont_char))) $width -= strlen(trim($cont_char));
			$tmp	 = wordwrap($text, $width, $cont_char."\n", true);
			$tmps	 = explode($cont_char."\n", $tmp, 2);
			$text	 = $tmps[0];
		}
		if($htmlchar) $text = htmlentities($text);
		return $text;
	}
	public function valuta($valuta, $decimals = NULL, $sign = NULL, $sign_position = NULL){
		if(is_null($sign)) $sign			 = $this->formats['valuta_sign'];
		if(is_null($sign_position)) $sign_position	 = $this->formats['valuta_sign_position'];
		if(is_null($decimals)) $decimals		 = $this->formats['valuta_decimals'];
		return ($sign_position == 'before'?$sign:'').$this->number_format($valuta, $decimals).($sign_position == 'after'?$sign:'');
	}
	public function smart_valuta($valuta, $decimals = NULL, $sign = NULL, $cent_sign = NULL, $sign_position = NULL, $cent_sign_position = NULL, $cent_decimals = NULL, $wrap_sign = NULL, $wrap_cent_sign = NULL){
		if(is_null($sign)) $sign				 = $this->formats['smart_valuta_sign'];
		if(is_null($sign_position)) $sign_position		 = $this->formats['smart_valuta_sign_position'];
		if(is_null($cent_sign)) $cent_sign			 = $this->formats['smart_valuta_cent_sign'];
		if(is_null($cent_sign_position)) $cent_sign_position	 = $this->formats['smart_valuta_cent_sign_position'];
		if(is_null($decimals)) $decimals			 = $this->formats['smart_valuta_decimals'];
		if(is_null($cent_decimals)) $cent_decimals		 = $this->formats['smart_valuta_cent_decimals'];
		if(is_null($wrap_sign)) $wrap_sign			 = $this->formats['smart_valuta_wrap_sign'];
		if(is_null($wrap_cent_sign)) $wrap_cent_sign		 = $this->formats['smart_valuta_wrap_cent_sign'];
		if($wrap_sign != ''){
			$sign = '<'.$wrap_sign.'>'.$sign.'</'.$wrap_sign.'>';
		}
		if($wrap_cent_sign != ''){
			$cent_sign = '<'.$wrap_cent_sign.'>'.$cent_sign.'</'.$wrap_cent_sign.'>';
		}
		if($valuta < 1){
			return ($cent_sign_position == 'before'?$cent_sign:'').$this->number_format(($valuta * 100), $cent_decimals).($cent_sign_position == 'after'?$cent_sign:'');
		}else{
			return ($sign_position == 'before'?$sign:'').$this->number_format($valuta, $decimals).($sign_position == 'after'?$sign:'');
		}
	}
	public function round_to_num($number, $to, $force_up = false){
		if($to != 0){
			if($force_up && (round($number / $to, 0) * $to < $number)){
				$mult = 1000000;
				$number += round((($to * $mult) - 1) / $mult, 2);
			}
			$number = round($number / $to, 0) * $to;
		}
		return $number;
	}
	public function seo_string($string){
		$string	 = preg_replace('/\%/', ' percentage', $string);
		$string	 = preg_replace('/\$/', ' dollars', $string);
		$string	 = preg_replace('/\@/', ' at ', $string);
		$string	 = preg_replace('/\&/', ' and ', $string);
		$string	 = preg_replace('/\s[\s]+/', '-', $string); // Strip off multiple spaces
		$string	 = preg_replace('/[\s\W]+/', '-', $string); // Strip off spaces and non-alpha-numeric
		$string	 = preg_replace('/^[\-]+/', '', $string); // Strip off the starting hyphens
		$string	 = preg_replace('/[\-]+$/', '', $string); // // Strip off the ending hyphens
		$string	 = strtolower($string);
		return $string;
	}
	static function minify_css($text){
		$from	 = array(
			//					'%(#|;|(//)).*%',				// comments:  # or //
			'%/\*(?:(?!\*/).)*\*/%s', // comments:  /*...*/
			'/\s{2,}/', // extra spaces
			"/\s*([;{}])[\r\n\t\s]/", // new lines
			'/\\s*;\\s*/', // white space (ws) between ;
			'/\\s*{\\s*/', // remove ws around {
			'/;?\\s*}\\s*/', // remove ws around } and last semicolon in declaration block
				//					'/:first-l(etter|ine)\\{/',		// prevent triggering IE6 bug: http://www.crankygeek.com/ie6pebug/
				//					'/((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value\\s+/x',		// Use newline after 1st numeric value (to limit line lengths).
				//					'/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i',
		);
		$to		 = array(
			//					'',
			'',
			' ',
			'$1',
			';',
			'{',
			'}',
				//					':first-l$1 {',
				//					"$1\n",
				//					'$1#$2$3$4$5',
		);
		$text	 = preg_replace($from, $to, $text);
		return trim($text);
	}
	static function minify_js($text, $force_minify = false){
		$file_cache	 = strtolower(md5($text));
		$folder		 = TMPPATH.'tmp_files'.DIRECTORY_SEPARATOR.substr($file_cache, 0, 2).DIRECTORY_SEPARATOR;
		$file_cache	 = $folder.$file_cache.'_content.js';
		$contents	 = false;//lc('cache')->get($file_cache, false);
//		if(!is_dir($folder) && !@mkdir($folder, 0766, true)){
//			return 'Impossible to create the cache folder: '.$folder;
//			return 1;
//		}
//		if(!ll('files')->file_exists($file_cache, true) || filesize($file_cache)<1){
		if($contents == false){
			static $minification_submissions = 0;
			if(strlen($text) <= 512){
				$contents = $text;
			}else{
				$contents = '';
//				if($minification_submissions<=3 || $force_minify){
				if($force_minify){
					$minification_submissions++;
					$post_text		 = http_build_query(array(
						'js_code'			=>$text,
						'output_info'		=>'compiled_code', //($returnErrors ? 'errors' : 'compiled_code'),
						'output_format'		=>'text',
						'compilation_level'	=>'SIMPLE_OPTIMIZATIONS', //'ADVANCED_OPTIMIZATIONS',//'SIMPLE_OPTIMIZATIONS'
							), null, '&');
					$URL			 = 'http://closure-compiler.appspot.com/compile';
					$allowUrlFopen	 = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
					if($allowUrlFopen){
						$contents = @file_get_contents($URL, false, stream_context_create(array(
									'http'=>array(
										'method'		=>'POST',
										'header'		=>'Content-type: application/x-www-form-urlencoded',
										'content'		=>$post_text,
										'max_redirects'	=>0,
										'timeout'		=>5,
									)
						)));
					}elseif(defined('CURLOPT_POST')){
						$ch			 = curl_init($URL);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post_text);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
						$contents	 = curl_exec($ch);
						curl_close($ch);
					}else{
						//"Could not make HTTP request: allow_url_open is false and cURL not available"
						$contents = $text;
					}
				}
				if($contents == false || (trim($contents) == '' && $text != '') || strtolower(substr(trim($contents), 0, 5)) == 'error' || strlen($contents) <= 50){
					//No HTTP response from server or empty response or error
					$contents = $text;
				}
			}
			//lc('cache')->set($file_cache, $contents, true, 7200);
//			if(trim($contents)!='' && $text!=$contents){
//				$contents = trim($contents);
//				$f = fopen($file_cache, 'w');
//				fwrite($f, $contents);
//				fclose($f);
//			}
//		} else {
//			touch($file_cache);		//in the future I will add a timetout to the cache
//			$contents = file_get_contents($file_cache);
		}
		return $contents;
	}
	static function minify_html($text){
		if(strlen($text) < 1024){
			$buffer = $text;
		}else{
			$file_cache	 = strtolower(md5($text));
			$folder		 = TMPPATH.'tmp_files'.DIRECTORY_SEPARATOR.substr($file_cache, 0, 2).DIRECTORY_SEPARATOR;
			$file_cache	 = $folder.$file_cache.'_content.html';
			$buffer		 = false;//lc('cache')->get($file_cache, false);
//			if(!is_dir($folder) && !@mkdir($folder, 0766, true)){
//				return 'Impossible to create the cache folder: '.$folder;
//			}
//			if(!ll('files')->file_exists($file_cache, true) || filesize($file_cache)<1){
			if($buffer == false){
				$append_body = '';
				$append_head = '';

				//grab the external CSS files
				$search_media	 = '/\bmedia\s*=\s*[\"\'](.*?)[\"\']/i';
				$search_css_ext	 = '/<link\b\s(?=[^>]*[\"\']text\/css[\"\'])[^>]*href\s*=[\"\'](.*?)[\"\'][^>]*>/i';
				$ret			 = preg_match_all($search_css_ext, $text, $tmps, PREG_SET_ORDER);
				if($ret !== false && $ret > 0){
					foreach($tmps as $v){
						$ret = preg_match_all($search_media, $v[0], $ttmps, PREG_SET_ORDER);
						if($ret !== false && $ret > 0){
							$media = $ttmps[0][1];
						}else{
							$media = 'all';
						}
						$append_head .= '<link type="text/css" rel="stylesheet" href="'.$v[1].'" media= "'.$media.'" />';
					}
				}

				//get CSS and save it
				$search_css	 = '/<\s*style\b[^>]*>(.*?)<\s*\/style>/is';
				$ret		 = preg_match_all($search_css, $text, $tmps);
				$t_css		 = array();
				if($ret !== false && $ret > 0){
					foreach($tmps as $k=> $v){
						if($k > 0){
							foreach($v as $kk=> $vv){
								$t_css[] = $vv;
							}
						}
					}
				}
				$css = format::minify_css(implode("\n", $t_css));
				if(trim($css) != '') $append_head .= '<style>'.trim($css).'</style>'."\n";

				//get inline JS and save it
				$search_js_id	 = '/\bid\s*=\s*[\"\'](.*?)[\"\']/i';
				$search_js_ext	 = '/<\s*script\b.*?src=\s*[\'|"]([^\'|"]*)[^>]*>\s*<\s*\/script>/i';
				$search_js		 = '/<\s*script\b[^>]*>(.*?)<\s*\/script>/is';
				$ret			 = preg_match_all($search_js, $text, $tmps, PREG_SET_ORDER);
				if($ret !== false && $ret > 0){
					foreach($tmps as $k=> $v){
						//let's check if we have a souce (src="")
						$ret = preg_match_all($search_js_ext, $v[0], $ttmps);
						if($ret !== false && $ret > 0){
							$ret = preg_match_all($search_js_id, $v[0], $tttmps, PREG_SET_ORDER);
							if($ret !== false && $ret > 0){
								$js_id = 'id="'.$tttmps[0][1].'"';
							}else{
								$js_id = '';
							}
							$append_body .= '<script '.$js_id.' type="text/javascript" language="javascript" src="'.$ttmps[1][0].'"></script>';
						}elseif($v[1] != ''){
							$vv = format::minify_js($v[1]);
							$append_body .= '<script>'.trim($vv).'</script>'."\n";
						}
					}
				}

				//get inline noscript and save it
				$search_no_js	 = '/<\s*noscript\b[^>]*>(.*?)<\s*\/noscript>/is';
				$ret			 = preg_match_all($search_no_js, $text, $tmps);
				$t_js			 = array();
				if($ret !== false && $ret > 0){
					foreach($tmps as $k=> $v){
						if($k > 0){
							foreach($v as $kk=> $vv){
								$t_js[] = $vv;
							}
						}
					}
				}
				$no_js = implode('\n', $t_js);
				if(trim($no_js) != '') $append_body .= '<noscript>'.$no_js.'</noscript>';


				//is HEAD or BODY existing on this page ?
				$head			 = '/(.*)(<\s*\/\s*head\s*>)(.*)/';
				$body			 = '/(.*)(<\s*\/\s*body\s*>)(.*)/';
				$there_is_head	 = preg_match($head, $text) > 0;
				$there_is_body	 = preg_match($body, $text) > 0;
				//remove CSS and JS
				$search			 = array(
					$search_css_ext,
					$search_css,
					$search_js_ext,
					$search_js,
					$search_no_js,
					'/\>\s{2,}/s', // strip whitespaces after tags, except space
					'/\s{2,}\</s', // strip whitespaces before tags, except space
					'/(\s)+/s', // shorten multiple whitespace sequences
				);
				$replace		 = array(
					'',
					'',
					'',
					'',
					'',
					'> ',
					' <',
					'\\1',
				);
				if($there_is_head){
					$search[]	 = $head;
					$replace[]	 = '\\1'."\n".$append_head."\n".'\\2\\3';
				}
				if($there_is_body){
					$search[]	 = $body;
					$replace[]	 = '\\1'."\n".$append_body."\n".'\\2\\3';
				}
				$buffer = preg_replace($search, $replace, $text);

				//			$buffer	= wordwrap($buffer, 500, "\n");	//can cause new lines issues with XML
				$err = '';
				switch(preg_last_error()){
					case PREG_NO_ERROR:
//						$err = 'There is no error.';
						break;
					case PREG_INTERNAL_ERROR:
						$err = 'There is an internal error!';
						break;
					case PREG_BACKTRACK_LIMIT_ERROR:
						$err = 'Backtrack limit was exhausted!';
						break;
					case PREG_RECURSION_LIMIT_ERROR:
						$err = 'Recursion limit was exhausted!';
						break;
					case PREG_BAD_UTF8_ERROR:
						$err = 'Bad UTF8 error!';
						break;
					case PREG_BAD_UTF8_ERROR:
						$err = 'Bad UTF8 offset error!';
						break;
					default:
						$err = preg_last_error();
				}
				if($err != ''){
					trigger_error($err, E_USER_WARNING);
					$buffer = $text;
				}

				if(!$there_is_head) $buffer	 = trim($append_head."\n".$buffer);
				if(!$there_is_body) $buffer	 = trim($buffer."\n".$append_body);

				//lc('cache')->set($file_cache, $buffer, true, 7200);
//				if(trim($buffer)!=''){
//					$f = fopen($file_cache, 'w');
//					fwrite($f, trim($buffer));
//					fclose($f);
//				}
//			} else {
//				touch($file_cache);		//in the future I will add a timetout to the cache
//				$buffer = file_get_contents($file_cache);
			}
		}
		return $buffer;
	}
}