<?php
return array(
	'app_begin'=>array(
		'Behavior\ReadHtmlCacheBehavior', // 读取静态缓存
		'Behavior\CheckLangBehavior', // 多语言
	),
);