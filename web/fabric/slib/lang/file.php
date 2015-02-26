<?php

//this class is intended to help with the handling of the languages

class lang_file extends lang{
	private $config	 = array();
	private $words	 = array();
	private $loaded	 = array();
	public function __construct(){
		$this->config['lang']	 = 'en';
		$cfg					 = lc('config')->get_and_unload_config('lang');
		if(is_array($cfg)){
			foreach($cfg as $key=> $value){
				$this->config[$key] = $value;
			}
		}
		$this->set_lang(ll('cookies')->get('lang'));
	}
	public function set_lang($lang = 'en'){
		if($lang != ''){
			$this->config['detected'] = $lang;
		}
	}
	public function get_lang(){
		if(!isset($this->config['detected']) || $this->config['detected'] == ''){
			$this->_detect_lang();
		}
		return $this->config['detected'];
	}
	public function get_supported_langs(){
		return $this->config['supported'];
	}
	public function get($word, $default = '', $lang = ''){
		if($lang == '') $lang = $this->_detect_lang();
		if(!isset($this->words[$lang][$word]) || $this->words[$lang][$word] == ''){
			//this language is not loaded
			$file = '';
			if(strpos($word, '_') > 0){
				list($file, $word1) = explode('_', $word, 2);
				if($file == 'common'){
					$word = $word1;
				}
			}
			if(!$this->load_lang($lang, $file)){
				//let's try the common file
				$this->load_lang($lang, '');
			}
		}
		if(!isset($this->words[$lang][$word]) || $this->words[$lang][$word] == ''){
			//nothing .. returning the default
			//to avoid to load the file over and over
			//I set the current key as the default
			$this->words[$lang][$word] = $default != ''?$default:$word;
		}
		$return = $this->words[$lang][$word];
		//do I have to translate the text in HTML ?
		if(in_array($lang, $this->config['toHTML'])){
			// Take all the html entities
			$caracteres	 = get_html_translation_table(HTML_ENTITIES);
			// Find out the "tags" entities
			$remover	 = get_html_translation_table(HTML_SPECIALCHARS);
			// Spit out the tags entities from the original table
			$caracteres	 = array_diff($caracteres, $remover);
			// Translate the string....
			$return		 = strtr($return, $caracteres);
			// And that's it!

			if($return == ''){
				$return = $this->words[$lang][$word];
			}
		}
		return $return;
	}
	public function load_lang($language, $file = ''){
		//reset language
		if($file == '') $file		 = 'common';
		$folder		 = ETCPATH.'lang/'.$language.'/';
		$lang_file	 = $folder.$file.'.lang'.EXT;
		if(!isset($this->loaded[$language][$file])){
			if(file_exists($lang_file)){
				$lang = array();
				include_once($lang_file);
				foreach($lang as $key=> $value){
					$this->words[$language][($file != 'common'?$file.'_':'').$key] = $value;
				}
				$this->loaded[$language][$file] = true;
			}else{
				$this->loaded[$language][$file] = false;
			}
		}
		return $this->loaded[$language][$file];
		//maybe later add it to the cache
	}
	private function _detect_lang(){
		$lang = '';
		if($this->config['detect']){
			if(isset($this->config['detected']) && $this->config['detected'] != ''){
				$lang = $this->config['detected'];
			}else{
				$language = lc('user_info')->language();
				if(isset($language[0]['code'])){
					$lang = substr($language[0]['code'], 0, 2);
				}
			}
		}
		if(!in_array($lang, $this->config['supported'])) $lang						 = $this->config['lang'];
//		if(!isset($this->config['detected']) || $this->config['detected']!=$lang){
		$this->config['detected']	 = $lang;
		ll('cookies')->set('lang', $lang);
//		}
		return $lang;
	}
}

?>