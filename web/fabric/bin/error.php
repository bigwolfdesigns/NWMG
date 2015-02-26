<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * to use mainly for 404 pages
 */
class error{
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		$tmps = @parse_url(lc('uri')->get_uri());
		$path = isset($tmps['path'])?$tmps['path']:'/';
		if($path=='/' || is_null($path)){
			lc('uri')->set(CLASS_KEY,'home');
			lc('home');
		}elseif(lc('uri')->get(CLASS_KEY)=='error'){
			if(ll('failover')->check()){
				$canonical_uri = lc('uri')->create_uri(array(CLASS_KEY=>ll('failover')->get_code()));
				lc('uri')->set_canonical_uri($canonical_uri);
				lc('uri')->set(CLASS_KEY,ll('failover')->get('class_key'));
				lc('uri')->set(TASK_KEY,ll('failover')->get('task_key'));
				lc(ll('failover')->get('class_key'));
			} else {
				$this->show_error(404);
			}
		}
	}

	public function show_error($error, $title = ''){
		ll('client')->set_initial();
//		ll('store')->set_page_view_type('error');
		lc('uri')->set(CLASS_KEY,'error');
		$title = trim($title)==''?$this->_translate_error($error):$title;
		header('HTTP/1.0 '.$this->_translate_error($error),true,$error);
		ll('display')
			->assign('title', $title)
			->assign('task', '');
		switch($error) {
			case 404:
			case 410:
			case 530:
				ll('display')->show('error_pages/'.$error);
				break;
			default:
				ll('display')->show('error_pages/default');
				break;
		}
	}

	private function _translate_error($error){
		$return = '';
		switch($error) {
			case 402:	$return = 'Payment Required';		break;
			case 403:	$return = 'Forbidden';				break;
			case 404:	$return = 'Not Found';				break;
			case 405:	$return = 'Method Not Allowed';		break;
			case 410:	$return = 'Gone';					break;
			case 500:	$return = 'Internal Server Error';	break;
			case 530:	$return = 'User access denied';		break;
			default:	$error = '';						break;
		}
		return $error.' '.$return;
	}
}