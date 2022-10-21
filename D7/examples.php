
Получить информацию по товарам из таблицы 

$iterator = \Bitrix\Catalog\ProductTable::getList(array(
    'select' => array(
        'ID', 'TYPE', 'AVAILABLE', 'CAN_BUY_ZERO', 'QUANTITY_TRACE', 'QUANTITY',
        'WEIGHT', 'WIDTH', 'HEIGHT', 'LENGTH',
        'MEASURE'
    ),
    'filter' => array('=ID' => $arProduct['ITEM']['ID'])
));
$productFields = $iterator->fetch();
unset($iterator);

-----------------------------------------------------------------

Добавить товар в корзину
класс CCatalogProductProvider отслеживает актуальное наличие товара
(если товара нет, будет проставлено нулевое кол-во а не то что передали
в корзину для добавления)

$product = array(
    'PRODUCT_ID' => $arProduct['ITEM']['ID'],
    'QUANTITY' => $deltaQuantity
);
if (!empty($arProps))
    $product['PROPS'] = $arProps;

$basketResult = \Bitrix\Catalog\Product\Basket::addProduct(
    $product, 
    [
    "PRODUCT_PROVIDER_CLASS" => 'CCatalogProductProvider',
    "FUSER_ID" => $fuserId,
    "CAN_BUY" => "N"
    ],
    ['USE_MERGE' => 'N']
);
if (!$basketResult->isSuccess())
{
    $APPLICATION->ThrowException(
        implode('; ', $basketResult->getErrorMessages())
    );
}
unset($basketResult);
------------------------------------------------------------------






-----------------------------------------------------
$rsSection = \Bitrix\Iblock\SectionTable::getList([
    'filter' => [
        'IBLOCK_ID' => $infoSIte["PROP"]["IB_CATALOG"]["IBLICK_ID"],
        "ACTIVE" => "Y",
        'IBLOCK_SECTION_ID' => 'NULL',
        'DEPTH_LEVEL' => '1'
    ],
    'select' =>  ['ID', 'NAME', 'SORT'],
    'order' => ['ID']
]);
while ($arSection = $rsSection->fetch())
    $arSections[$arSection['ID']] = $arSection['NAME'];

===========================================================

$result = \Bitrix\Iblock\ElementTable::getList([
    'select' => array('ID','NAME','IBLOCK_ID','ACTIVE', 'VALUE' => 'PROP_ELEM.VALUE', 
        'PROP_ID' => 'PROP_ELEM.IBLOCK_PROPERTY_ID', 'PROP_CODE' => 'PROP.CODE'),
    'filter' => ['IBLOCK_ID' => $iblockId],
    'runtime' => [
        new \Bitrix\Main\ORM\Fields\Relations\Reference(
            'PROP_ELEM',
            \Bitrix\Iblock\ElementPropertyTable::class,
            \Bitrix\Main\ORM\Query\Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')
        ),
        new \Bitrix\Main\ORM\Fields\Relations\Reference(
            'PROP',
            \Bitrix\Iblock\PropertyTable::class,
            \Bitrix\Main\ORM\Query\Join::on('this.PROP_ID', 'ref.ID')
        ),
    ]
]);
==============================================================