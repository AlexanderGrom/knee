<?php

return array(

	// Разрешённые теги
	'cfgAllowTags' => array(
		// вызов метода с параметрами
		array(
			array('b', 'i', 'u', 's', 'q', 'br', 'code', 'tt'),
		),			
	),
	// Коротие теги типа
	'cfgSetTagShort' => array(
		array(
			array('br')
		),
	),
	// Преформатированные теги
	'cfgSetTagPreformatted' => array(
		array(
			array('code', 'tt')
		),
	),
	// Разрешённые параметры тегов
	'cfgAllowTagParams' => array(
		
		array(
			'code',
			array('lang'=>'#text')
		)
		
	),
	// Параметры тегов являющиеся обязательными
	'cfgSetTagParamsRequired' => array(),
	// Теги которые необходимо вырезать из текста вместе с контентом
	'cfgSetTagCutWithContent' => array(
		array(
			array('script',  'style', 'iframe')
		),
	),
	// Вложенные теги
	'cfgSetTagChilds' => array(),
	// Теги которые которы не должны быть дочерними относительно любых других тегов
	'cfgSetTagGlobal' => array(),
	// Не нужна авто-расстановка <br>
	'cfgSetTagNoAutoBr' => array(),
	// Теги с обязательными параметрами
	'cfgSetTagParamDefault' => array(),
	// Включение авто-добавления <br>
	'cfgSetAutoBrMode' => array(
		array(
			true
		)
	),
	// Отключаем XHTML
	'cfgSetXHTMLMode' => array(
		array(
			false
		)
	),
	// Включаем автоподсветку ссылок
	'cfgSetAutoLinkMode' => array(
		array(
			true
		)
	),
	// Список допустимых протоколов для ссылок
	'cfgSetLinkProtocolAllow' => array(
		array(
			array('http','https','ftp')
		)
	),
	// Теги в которых не нужно типографирование
	'cfgSetTagNoTypography' => array(			
		array(
			array('code','tt')
		),
	),
	// Теги, после которых необходимо пропускать одну пробельную строку
	'cfgSetTagBlockType' => array(
		array(
			array('code')
		)
	),
	'cfgSetTagBuildCallback' => array(
		array(
			'code',
			array('$this','tag_code_build'),
		)
	),
	'cfgSetSpecialCharCallback' => array(
		array(
			'@',
			array('$this', 'tag_at_build')
		)
	)
);