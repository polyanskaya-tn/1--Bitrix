<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<? //var_dump($arResult); ?>

	<ul class="bar">
<?foreach($arResult as $arItem):?>

	<?if ($arItem["PERMISSION"] > "D"):?>
		<li <?if ($arItem["SELECTED"]):?> class="active" <?endif?> >
			<a href="<?=$arItem["LINK"]?>"><nobr><?=$arItem["TEXT"]?></nobr></a>
		</li>
	<?endif?>

<?endforeach?>

	</ul>
<?endif?>






