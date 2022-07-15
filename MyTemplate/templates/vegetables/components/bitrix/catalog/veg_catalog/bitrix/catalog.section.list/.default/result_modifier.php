<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arViewModeList = array('LIST', 'LINE', 'TEXT', 'TILE');

$arDefaultParams = array(
	'VIEW_MODE' => 'LIST',
	'SHOW_PARENT_NAME' => 'Y',
	'HIDE_SECTION_NAME' => 'N'
);

$arParams = array_merge($arDefaultParams, $arParams);

if (!in_array($arParams['VIEW_MODE'], $arViewModeList))
	$arParams['VIEW_MODE'] = 'LIST';
if ('N' != $arParams['SHOW_PARENT_NAME'])
	$arParams['SHOW_PARENT_NAME'] = 'Y';
if ('Y' != $arParams['HIDE_SECTION_NAME'])
	$arParams['HIDE_SECTION_NAME'] = 'N';

$arResult['VIEW_MODE_LIST'] = $arViewModeList;

if (0 < $arResult['SECTIONS_COUNT'])
{
	if ('LIST' != $arParams['VIEW_MODE'])
	{
		$boolClear = false;
		$arNewSections = array();
		foreach ($arResult['SECTIONS'] as &$arOneSection)
		{
			if (1 < $arOneSection['RELATIVE_DEPTH_LEVEL'])
			{
				$boolClear = true;
				continue;
			}
			$arNewSections[] = $arOneSection;
		}
		unset($arOneSection);
		if ($boolClear)
		{
			$arResult['SECTIONS'] = $arNewSections;
			$arResult['SECTIONS_COUNT'] = count($arNewSections);
		}
		unset($arNewSections);
	}
}

if (0 < $arResult['SECTIONS_COUNT'])
{
	$boolPicture = false;
	$boolDescr = false;
	$arSelect = array('ID');
	$arMap = array();
	if ('LINE' == $arParams['VIEW_MODE'] || 'TILE' == $arParams['VIEW_MODE'])
	{
		reset($arResult['SECTIONS']);
		$arCurrent = current($arResult['SECTIONS']);
		if (!isset($arCurrent['PICTURE']))
		{
			$boolPicture = true;
			$arSelect[] = 'PICTURE';
		}
		if ('LINE' == $arParams['VIEW_MODE'] && !array_key_exists('DESCRIPTION', $arCurrent))
		{
			$boolDescr = true;
			$arSelect[] = 'DESCRIPTION';
			$arSelect[] = 'DESCRIPTION_TYPE';
		}
	}
	if ($boolPicture || $boolDescr)
	{
		foreach ($arResult['SECTIONS'] as $key => $arSection)
		{
			$arMap[$arSection['ID']] = $key;
		}
		$rsSections = CIBlockSection::GetList(array(), array('ID' => array_keys($arMap)), false, $arSelect);
		while ($arSection = $rsSections->GetNext())
		{
			if (!isset($arMap[$arSection['ID']]))
				continue;
			$key = $arMap[$arSection['ID']];
			if ($boolPicture)
			{
				$arSection['PICTURE'] = intval($arSection['PICTURE']);
				$arSection['PICTURE'] = (0 < $arSection['PICTURE'] ? CFile::GetFileArray($arSection['PICTURE']) : false);
				$arResult['SECTIONS'][$key]['PICTURE'] = $arSection['PICTURE'];
				$arResult['SECTIONS'][$key]['~PICTURE'] = $arSection['~PICTURE'];
			}
			if ($boolDescr)
			{
				$arResult['SECTIONS'][$key]['DESCRIPTION'] = $arSection['DESCRIPTION'];
				$arResult['SECTIONS'][$key]['~DESCRIPTION'] = $arSection['~DESCRIPTION'];
				$arResult['SECTIONS'][$key]['DESCRIPTION_TYPE'] = $arSection['DESCRIPTION_TYPE'];
				$arResult['SECTIONS'][$key]['~DESCRIPTION_TYPE'] = $arSection['~DESCRIPTION_TYPE'];
			}
		}
	}
}

//get elements list (part of detail page)
$arResult['ELEMENT_LIST'] = array();
if (0 < $arResult['SECTIONS_COUNT'])
{
	$arSections = array();
	foreach ( $arResult['SECTIONS'] as &$arSect )
	{
		$arSections[] = $arSect['ID'];	
	}

	$arFilter = array(
			'IBLOCK_ID' => $arResult['SECTIONS'][0]['IBLOCK_ID'],
			'SECTION_ID' => $arSections
	);

	$rsItems = CIBlockElement::GetList(
		array('SORT' => 'ASC'),
		$arFilter,
		false,
		false,
		array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'CODE')
	); 

	while ( $arItem = $rsItems->Fetch() )
	{
		$arElement = array();
		$arElement['SECTION_ID'] = $arItem['IBLOCK_SECTION_ID'];
		$arElement['PREVIEW_PICTURE'] = array(
				'ALT' => $arItem['NAME'],
				'TITLE' => $arItem['NAME']
			);
		$arElement['PREVIEW_PICTURE']['SRC'] = 
			CFile::GetPath($arItem['PREVIEW_PICTURE']);

		//get element URL (ЧПУ mode)
		$arElement['DETAIL_PAGE_URL'] = CComponentEngine::MakePathFromTemplate(
			$arItem['DETAIL_PAGE_URL'],
			array(
   				"SECTION_ID" => $arItem['IBLOCK_SECTION_ID'],
   				"ELEMENT_ID" => $arItem['ID'],
   				"SECTION_CODE" => $arItem['IBLOCK_SECTION_ID'],
   				"ELEMENT_CODE" => $arItem['CODE']
			)
		);

		$arResult['ELEMENT_LIST'][] = $arElement;
	}
}

?>