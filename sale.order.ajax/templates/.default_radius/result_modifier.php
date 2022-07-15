<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);


if (\Bitrix\Main\Loader::includeModule("ieats.contentsite")) {

    $infoSIte = MainIeatsContentSite::getGlobalInfo();

    foreach ($arResult["JS_DATA"]['DELIVERY'] as $key => $item) {
        $arIdDeliveryFilter[$key] = $key;
    }

    $dbSubServicesRes = \Bitrix\Sale\Delivery\Services\Table::getList(array(
        "filter" => array("ID" => $arIdDeliveryFilter),
        "select" => array("ID", "PARENT_ID")
    ));
    while ($itemDeliveryGroupInfo = $dbSubServicesRes->fetch()) {
        $arResult["JS_DATA"]["DELIVERY_PARENTS"][$itemDeliveryGroupInfo["ID"]] = $itemDeliveryGroupInfo["PARENT_ID"];
    }



    if(!empty($infoSIte["PROP"]["DELIVERY_RADIUS"]))
        $arResult["JS_DATA"]["DELIVERY_RADIUS"] = $infoSIte["PROP"]["DELIVERY_RADIUS"];

    if(!empty($infoSIte["PROP"]['COORDINATS'])) {
        $arResult["JS_DATA"]["COORDINATS"] = $infoSIte["PROP"]["COORDINATS"];
        $arResult["JS_DATA"]["TITLE_MAP"] = $infoSIte["PROP"]["TITLE_MAP"];
    }
    if (!empty($infoSIte["PROP"]["TIME_AREA"]) && !empty($infoSIte["PROP"]["WIKE_WORK_TIME"])) {


        $infoSIte = MainIeatsContentSite::getGlobalInfo();
        $timeZone = $infoSIte["PROP"]["TIME_AREA"];
        $timeWork = $infoSIte["PROP"]["WIKE_WORK_TIME"];
        $arResult["ORDER_WEEK"] = $timeWork;

        $date = new DateTime(false, timezone_open($timeZone));
        $day = $date->format("l");
        $thisTime = $date->format('H:i');
        $thisDayIB = $timeWork[$day];

        $timeFromYesterday = getTransferTimeFromYesterday($timeWork, $timeZone);


        $existsTransferToNextDay = (new DateTime($thisDayIB["pm"]["time_end"]))->format("A") == "AM"
            && strtotime($thisDayIB["pm"]["time_end"]) >= strtotime("00:00")
            && strtotime($thisDayIB["pm"]["time_end"]) < strtotime($thisDayIB["pm"]["time_start"]);
        $lastTimestampInDay = $existsTransferToNextDay ? strtotime("23:59:59") : strtotime($thisDayIB["pm"]["time_end"]);

        switch ($thisDayIB['vh']) {
            case "Y":
                if ($thisDayIB["order"] == "Y") {
                    $arResult["ORDER_BLOCK"] = "N";
                } else {
                    $arResult["ORDER_BLOCK"] = "Y";
                }
                break;
            case "N":

                if ($thisDayIB["order"] == "N") {
                    if (!empty($timeFromYesterday)) {
                        if (strtotime($thisTime) >= strtotime($timeFromYesterday) && strtotime($thisTime) < strtotime($thisDayIB["am"]["time_start"])
                            || strtotime($thisTime) >= strtotime($thisDayIB["am"]["time_end"]) && strtotime($thisTime) < strtotime($thisDayIB["pm"]["time_start"])
                            || strtotime($thisTime) >= $lastTimestampInDay
                        ) {
                            $arResult["ORDER_BLOCK"] = "Y";
                        }
                    } else {
                        if (
                            strtotime($thisTime) < strtotime($thisDayIB["am"]["time_start"])
                            || strtotime($thisTime) >= strtotime($thisDayIB["am"]["time_end"]) && strtotime($thisTime) < strtotime($thisDayIB["pm"]["time_start"])
                            || strtotime($thisTime) >= $lastTimestampInDay
                        ) {
                            $arResult["ORDER_BLOCK"] = "Y";

                        }
                    }
                }
                break;
        }

    }
}

function getTransferTimeFromYesterday($timeWork, $timeZone)
{
    $yesterday = new DateTime(false, timezone_open($timeZone));
    $yesterdayDay = $yesterday->modify('-1 day');
    $yesterdayDayWek = $yesterdayDay->format("l");
    $yesterdayDayWekDayIB = $timeWork[$yesterdayDayWek];

    $time = explode(":", $yesterdayDayWekDayIB["pm"]["time_end"]);
    $yesterdayEndTime = new DateTime(false, timezone_open($timeZone));
    $yesterdayEndTime = $yesterdayEndTime->setTime($time[0], $time[1]);

    if ($yesterdayEndTime->format("A") == "AM"
        && (strtotime($yesterdayDayWekDayIB["pm"]["time_end"]) > strtotime("00:00"))
        && (strtotime($yesterdayDayWekDayIB["pm"]["time_end"]) < strtotime($yesterdayDayWekDayIB["pm"]["time_start"]))
    ) {
        $timeFromYesterday = $yesterdayDayWekDayIB["pm"]["time_end"];
        return $timeFromYesterday;
    }
    return NULL;
}

