<?php
    //多语言支持
    return array(
        'app_begin' => array('Behavior\CheckLangBehavior'),
        //'app_begin' => array('CheckLang')
        'action_begin'=>array(
		'Common\\Behavior\\AccessBehavior'
	),
    );

?>
