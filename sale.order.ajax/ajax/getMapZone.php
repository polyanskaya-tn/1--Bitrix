<?
/** @global CMain $APPLICATION */
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (isset($_POST['AJAX']) && $_POST['AJAX'] == 'Y')
{
    $APPLICATION->IncludeComponent(
        "angerro:angerro.yadelivery",
        "custom",
        Array(
            "WIDTH" => "auto",
            "HEIGHT" => "500",
            "MAP_ID" => $_POST["idZona"]
        )
    );
}