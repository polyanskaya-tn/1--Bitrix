<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y") {
    $APPLICATION->SetTitle(Loc::getMessage("ORDER_BLOCK_TITLE"));
}
?>


<b><?= Loc::getMessage("ORDER_BLOCK_TEXT") ?></b>
<br/><br/>

<table id="order_time" border="1" cellpadding="1" cellspacing="1">
    <thead>
    <td><?= Loc::getMessage("ORDER_BLOCK_THEAD_DAY") ?></td>
    <td colspan="2"><?= Loc::getMessage("ORDER_BLOCK_THEAD_PERIOD_WORK_TIME") ?></td>
    </thead>

    <tbody>
    <? foreach ($arResult["ORDER_WEEK"] as $day => $infoDay) { ?>
        <tr>
            <td><?= Loc::getMessage($day) ?></td>

            <? if ($infoDay["vh"] == "Y") { ?>
                <td colspan="2"><?= Loc::getMessage("ORDER_BLOCK_THEAD_DAY_OFF") ?></td>
            <? } else { ?>
		<? if (noBreak($infoDay)) { ?>
			<td colspan="2"><?= $infoDay["am"]["time_start"] ?> - <?= $infoDay["pm"]["time_end"] ?> <?= Loc::getMessage("ORDER_BLOCK_THEAD_HOURS_SUFFIX") ?></td>
		<? } else { ?>
                        <td><?= $infoDay["am"]["time_start"] ?> - <?= $infoDay["am"]["time_end"] ?> <?= Loc::getMessage("ORDER_BLOCK_THEAD_HOURS_SUFFIX") ?></td>
			<td><?= $infoDay["pm"]["time_start"] ?> - <?= $infoDay["pm"]["time_end"] ?> <?= Loc::getMessage("ORDER_BLOCK_THEAD_HOURS_SUFFIX") ?></td>
		<? } ?>
            <? } ?>
        </tr>
    <? } ?>

    </tbody>
</table>


<? function noBreak($infoDay) {
	return strtotime($infoDay["am"]["time_end"]) >= strtotime($infoDay["pm"]["time_start"]);
}?>


