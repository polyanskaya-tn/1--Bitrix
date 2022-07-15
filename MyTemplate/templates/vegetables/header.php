<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title><?$APPLICATION->ShowTitle()?></title>

<?$APPLICATION->ShowHead();?>

  <link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/style/index.css" media="screen" />
  <!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/style/ie7.css" media="screen"><![endif]-->
  <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.6.1.min.js"></script>
  <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jcarousellite_1.0.1.pack.js"></script>
  <!--<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/cufon-yui.js"></script>
  <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/Lobster_400.font.js"></script> -->

  <!--<script type="text/javascript">
    Cufon.replace("h1, h2, h3");
    Cufon.replace("ul.bar, .sticker_midi span");

    //Cufon.replace("ul.bar li a" {
      //hover: true}
    //);
  </script> -->
</head>

<body>

<?$APPLICATION->ShowPanel();?> 

<div id="wrapper">
	<div id="header">
    <div class="dblock ohidden">
      <a class="left" href="index.html"><img src="<?=SITE_TEMPLATE_PATH?>/images/logotype.png" width="278" height="116" alt="" title="<?$APPLICATION->ShowTitle()?>" /></a>

      <div class="contacts"><strong>Телефон:</strong> +7 495 745 58 96<br /><strong>Эл. почта:</strong> <a href="mailto:info@premiko-d.ru">info@premiko-d.ru</a></div>

      <?$APPLICATION->IncludeComponent("bitrix:menu", "simple_main_menu", Array(
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
		"COMPONENT_TEMPLATE" => "grey_tabs",
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"MENU_CACHE_TYPE" => "N",	// Тип кеширования
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	),
	false
);?>

    </div><!--/.dblock ohidden -->

<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "nav", Array(
	"COMPONENT_TEMPLATE" => ".default",
		"PATH" => "",	// Путь, для которого будет построена навигационная цепочка (по умолчанию, текущий путь)
		"SITE_ID" => "-",	// Cайт (устанавливается в случае многосайтовой версии, когда DOCUMENT_ROOT у сайтов разный)
		"START_FROM" => "0",	// Номер пункта, начиная с которого будет построена навигационная цепочка
	),
	false
);?>

<?$APPLICATION->IncludeComponent(
  "bitrix:main.include",
  "",
  Array(
    "AREA_FILE_SHOW" => "page",
    "AREA_FILE_SUFFIX" => "inc",
    "COMPONENT_TEMPLATE" => ".default",
    "EDIT_TEMPLATE" => ""
  )
);?>

</div><!-- /header -->