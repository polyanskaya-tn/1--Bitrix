<?require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent(
    "angerro:angerro.yadelivery",
    "custom",
    Array(
        "WIDTH" => "auto",
        "HEIGHT" => "500",
        "MAP_ID" => $_POST["idZona"]
    )
);?>