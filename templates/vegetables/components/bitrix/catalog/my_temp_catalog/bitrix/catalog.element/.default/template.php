<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */


$this->setFrameMode(true);
$templateLibrary = array('popup');
$currencyList = '';
if (!empty($arResult['CURRENCIES']))
{
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList
);
unset($currencyList, $templateLibrary);

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
	'ID' => $strMainID,
	'PICT' => $strMainID.'_pict',
	'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
	'STICKER_ID' => $strMainID.'_sticker',
	'BIG_SLIDER_ID' => $strMainID.'_big_slider',
	'BIG_IMG_CONT_ID' => $strMainID.'_bigimg_cont',
	'SLIDER_CONT_ID' => $strMainID.'_slider_cont',
	'SLIDER_LIST' => $strMainID.'_slider_list',
	'SLIDER_LEFT' => $strMainID.'_slider_left',
	'SLIDER_RIGHT' => $strMainID.'_slider_right',
	'OLD_PRICE' => $strMainID.'_old_price',
	'PRICE' => $strMainID.'_price',
	'DISCOUNT_PRICE' => $strMainID.'_price_discount',
	'SLIDER_CONT_OF_ID' => $strMainID.'_slider_cont_',
	'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
	'SLIDER_LEFT_OF_ID' => $strMainID.'_slider_left_',
	'SLIDER_RIGHT_OF_ID' => $strMainID.'_slider_right_',
	'QUANTITY' => $strMainID.'_quantity',
	'QUANTITY_DOWN' => $strMainID.'_quant_down',
	'QUANTITY_UP' => $strMainID.'_quant_up',
	'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
	'QUANTITY_LIMIT' => $strMainID.'_quant_limit',
	'BASIS_PRICE' => $strMainID.'_basis_price',
	'BUY_LINK' => $strMainID.'_buy_link',
	'ADD_BASKET_LINK' => $strMainID.'_add_basket_link',
	'BASKET_ACTIONS' => $strMainID.'_basket_actions',
	'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail',
	'COMPARE_LINK' => $strMainID.'_compare_link',
	'PROP' => $strMainID.'_prop_',
	'PROP_DIV' => $strMainID.'_skudiv',
	'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
	'OFFER_GROUP' => $strMainID.'_set_group_',
	'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);
reset($arResult['MORE_PHOTO']);
$arFirstPhoto = current($arResult['MORE_PHOTO']);

?>


    <div class="block_green visible">
      <div class="body visible">
        <a class="sticker_midi" href="#" style="bottom: -80px;">
          <span>Каталог продукции</span>
        </a>

        <script type="text/javascript">
        	$(function() {
        	    $(".carousel").jCarouselLite({
        	        btnNext: ".prev",
        	        btnPrev: ".next",
        		  vertical: false,
        		  mouseWheel: false,
        		  circular: true,
        		  visible: 5,
        	    speed: 800
        	    });
        	});
        </script>

        <div id="jCarouselLite">
          <a class="next" href="javascript:void(0);"></a>
          <a class="prev" href="javascript:void(0);"></a>

          <div class="carousel">
            <ul>
              <li>
                <div class="imgblock">
                  <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/uploads/1.jpg" width="122" height="124" alt="" /></a>
                </div>
              </li>

              <li>
                <div class="imgblock">
                  <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/uploads/2.jpg" width="122" height="124" alt="" /></a>
                </div>
              </li>

              <li>
                <div class="imgblock">
                  <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/uploads/1.jpg" width="122" height="124" alt="" /></a>
                </div>
              </li>

              <li>
                <div class="imgblock">
                  <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/uploads/2.jpg" width="122" height="124" alt="" /></a>
                </div>
              </li>

              <li>
                <div class="imgblock">
                  <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/uploads/1.jpg" width="122" height="124" alt="" /></a>
                </div>
              </li>

              <li>
                <div class="imgblock">
                  <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/uploads/2.jpg" width="122" height="124" alt="" /></a>
                </div>
              </li>
            </ul>
          </div><!--/.carousel -->
        </div><!-- /jCarouselLite -->
      </div><!--/.body -->

      <div class="block_green_down"></div>
    </div><!--/.block_green -->
    

<div class="block_green nobg">
    <div class="body">
        <div class="sideleft">
        	<img id="<? echo $arItemIDs['PICT']; ?>" src="<? echo $arFirstPhoto['SRC']; ?>" alt="<? echo $strAlt; ?>" title="<? echo $strTitle; ?>">
        </div><!--/.sideleft -->

        <div class="dblock ohidden">
        	<h1>
				<?
				echo (
					isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
					? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
					: $arResult["NAME"]
				); ?>
			</h1>

			<?
			if ('' != $arResult['DETAIL_TEXT'])
			{
				if ('html' == $arResult['DETAIL_TEXT_TYPE'])
				{
					echo $arResult['DETAIL_TEXT'];
				}
				else
				{
					?><p><? echo $arResult['DETAIL_TEXT']; ?></p><?
				}
			}
			?>
		</div><!--/.dblock hidden -->
    </div><!--/.body -->
</div><!--/.block_green -->



