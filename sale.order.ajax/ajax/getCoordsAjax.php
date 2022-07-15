<?
/** @global CMain $APPLICATION */
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);

use Bitrix\Main,
	Bitrix\Catalog,
    Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/description.php");

if (isset($_POST['AJAX']) && $_POST['AJAX'] == 'Y')
{
    $address = $_POST['Address'];

    $url = "https://nominatim.openstreetmap.org/search?q=${address}&format=json";

    $headers = array(
        "Referer: ${_SERVER['HTTP_REFERER']}"
    );

    $session = curl_init();
    curl_setopt($session,CURLOPT_URL,$url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($session);
    $err = curl_error($session);
    
    curl_close($session);

    if ($err) {
        $APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo Main\Web\Json::encode(
			array(
				'STATUS' => 'ERROR',
				'MESSAGE' => "cURL Error #:" . $err
		));
    } else {
        $APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo "{\"STATUS\":\"OK\", \"MESSAGE\":${response}}";
    }
}
