<?php
//empty class
class display_default extends display{
	public function __construct() {
		parent::$instance =&$this;
	}
}
?>