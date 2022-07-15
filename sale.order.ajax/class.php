<?php

use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Controller\PhoneAuth;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Location\GeoIp;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PersonType;
use Bitrix\Sale\Result;
use Bitrix\Sale\Services\Company;
use Bitrix\Sale\Shipment;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var $APPLICATION CMain
 * @var $USER CUser
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("sale"))
{
	ShowError(Loc::getMessage("SOA_MODULE_NOT_INSTALL"));

	return;
}

CBitrixComponent::includeComponentClass("bitrix:sale.order.ajax");

class DgsSaleOrderAjax extends SaleOrderAjax
{
	protected function getAddons($basket, $basketItem) {

		if (!$basketItem) return null;

		$addons = array();
		$iterator = $basket->getIterator();
		while ( $iterator->valid() ) {
			$product = $iterator->current();
			$properties = $product->getPropertyCollection();
			$values = $properties->getPropertyValues();
			if ($values["PRODUCT.LINK"]["VALUE"] == $basketItem->getId()) {
				$addons[] = $product->getId();
			}
			$iterator->next();
		}
		unset($iterator);

		return $addons;
	}

	protected function recalculateAction()
	{
		$error = '';
		$isMainChanged = false;
		$isNotChangedAddons = false;

		if (!$this->request->isPost() || !$this->checkSession)
		{
			$this->showAjaxAnswer([
				'order' => [],
				'error' => Loc::getMessage('SESSID_ERROR'),
			]);
			return;
		}

		$id = $this->request->get('id');
		if (!isset($id))  {
			//error wrong params
		} else {
			$request = $this->request->get('order');
			$newValue = $request['QUANTITY_'.$id];
	
			$basket = $this->getBasketStorage()->getBasket();
			$basketItem = $basket->getItemByBasketCode($id);

			if ($basketItem->getQuantity() != $newValue) {
				$mainResult = $basketItem->setField('QUANTITY', $newValue);
				$isMainChanged = $mainResult->isSuccess();

				if ($isMainChanged) {
					//recalc addons
					$addons = $this->getAddons($basket, $basketItem);
					if (count($addons) > 0) {
						foreach($addons as $addonsId) {
							$addonsItem = $basket->getItemByBasketCode($addonsId);
							$addonsResult = $addonsItem->setField('QUANTITY', $newValue);
							if (!$addonsResult->isSuccess())
							{
								$isNotChangedAddons = true;
								break;
							}
						}
					}
				}

				if (!$isNotChangedAddons && $isMainChanged) {
					$saveResult = $basket->save();
					if ($saveResult->isSuccess())
					{
						$this->arElementId[] = $id;
					}
					else
					{
						$error = $saveResult->getErrors();
					}
				} else {
					//error saved value
				}
			} else {
				//error wrong value
			}
		} 

		if (!empty($this->arElementId)) {

			global $USER;

			if ($USER)
				$userId = $USER->GetID();
	
			if (!$userId)
			{
				$userId = CSaleUser::GetAnonymousUserID();
			}
	
			$this->request->set($this->request->get('order'));

			$this->order = $this->createOrder($userId);
			$this->initGrid();
			$this->obtainBasket();
			$this->obtainPropertiesForIbElements();

			if ($this->arParams['COMPATIBLE_MODE'] == 'Y')
			{
				$this->obtainFormattedProperties();
			}

			$this->obtainDelivery();
			$this->obtainPaySystem();
			$this->obtainTaxes();
			$this->obtainTotal();

			if ($this->arParams['USER_CONSENT'] === 'Y')
			{
				$this->obtainUserConsentInfo();
			}

			$this->getJsDataResult();

			$this->showAjaxAnswer([
				'order' => $this->arResult['JS_DATA'],
				'error' => $error,
			]);
		}
	}

	protected function deleteAction()
	{
		$isDeleted = false;
		$isDeletedAddons = true;
		$html = '';
		$error = '';

		if (!$this->request->isPost() || !$this->checkSession)
		{
			$this->showAjaxAnswer([
				'order' => [],
				'error' => Loc::getMessage('SESSID_ERROR'),
			]);
			return;
		}
		$id = $this->request->get('id');
		
		if (!isset($id))  {
			//$error = Loc::getMessage('SESSID_ERROR');
		} else {
			$basket = $this->getBasketStorage()->getBasket();
			$basketItem = $basket->getItemByBasketCode($id);
			if ($basketItem)
			{
				$addons = $this->getAddons($basket, $basketItem);

				if (count($addons) > 0) {
					foreach($addons as $id) {
						$product = $basket->getItemByBasketCode($id);
						$deleteResult = $product->delete();
						if (!$deleteResult->isSuccess())
						{
							$isDeletedAddons = false;
							break;
						}
					}
				}

				if ($isDeletedAddons) {
					$deleteResult = $basketItem->delete();
					if ($deleteResult->isSuccess())
					{
						$saveResult = $basket->save();
						if ($saveResult->isSuccess())
						{
							$_SESSION['SALE_BASKET_NUM_PRODUCTS'][$this->getSiteId()]--;
							$isDeleted = true; 
						}
						else
						{
							$deleteResult->addErrors($saveResult->getErrors());
						}
					}
				}
			}
		}

		if ($isDeleted) {
			global $USER;

			if ($USER)
				$userId = $USER->GetID();
	
			if (!$userId)
			{
				$userId = CSaleUser::GetAnonymousUserID();
			}
	
			$this->request->set($this->request->get('order'));

			$this->order = $this->createOrder($userId);
			$this->initGrid();
			$this->obtainBasket();
			$this->obtainPropertiesForIbElements();

			if ($this->arParams['COMPATIBLE_MODE'] == 'Y')
			{
				$this->obtainFormattedProperties();
			}

			$this->obtainDelivery();
			$this->obtainPaySystem();
			$this->obtainTaxes();
			$this->obtainTotal();

			if ($this->arParams['USER_CONSENT'] === 'Y')
			{
				$this->obtainUserConsentInfo();
			}

			$this->getJsDataResult();
			$html = $this->arResult['JS_DATA'];
		}

		$this->showAjaxAnswer([
			'order' => $html,
			'error' => $error,
		]);
	}

	protected function getJsDataResult() {
		parent::getJsDataResult();

		$result =& $this->arResult['JS_DATA'];
		$result['ORDER_RESTAURANTS'] = $this->arUserResult['ORDER_RESTAURANTS'];

		if (\Bitrix\Main\Loader::includeModule("ieats.contentsite")) {
			$infoSIte = MainIeatsContentSite::getGlobalInfo();

			if ($infoSIte["PROP"]["IB_RESTAURANTS"]) {
				$ibRestoLink = $infoSIte["PROP"]["IB_RESTAURANTS"]["IBLICK_ID"];
				if ($ibRestoLink) {

					$iterator = \CIBlockElement::GetList(
						array(),
						array('IBLOCK_ID' => $ibRestoLink), 
						false,
						false,
						array('ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_*')
					);
					$arrResto = array();
					$ibResto = 0;
					while ($arItem = $iterator->GetNextElement())
					{
						$arFields = $arItem->GetFields();
						$arProps = $arItem->GetProperties();

						if ( $arProps["ID_SITE"]["VALUE"] == $this->getSiteId() ) {
							$id = $arProps["ID_RESTAURANT"]["VALUE"];

							$item = array();
							$item["ID_ZONE"] = $arProps["ID_ZONE"]["VALUE"];
							$item["ID"] = $id;
							$arrResto[$id] = $item;
						}
						
						$ibResto = $arProps["ID_RESTAURANT"]["LINK_IBLOCK_ID"];
					}

					$restoIds = array();
					foreach ($arrResto as $item) {
						$restoIds[] = $item["ID"];
					}

					$iterator = \CIBlockElement::GetList(
						array(),
						array('IBLOCK_ID' => $ibResto, 'ID' => $restoIds), 
						false,
						false,
						array('ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_*')
					);

					while ($arItem = $iterator->GetNextElement())
					{
						$arFields = $arItem->GetFields();
						$arProps = $arItem->GetProperties();

						$id = $arFields['ID'];
						$arrResto[$id]['ADDRESS'] = $arProps['ADDRESS']['VALUE']; 
						$arrResto[$id]['CITY'] = $arProps['CITY']['VALUE'];
					}
					unset($arItem);

					$result["ADDRESS_RESTO"] = $arrResto;
				}
			} 

			$result["DELIVERY_NO_ZONE"] = $infoSIte["PROP"]["DELIVERY_NO_ZONE"];
		}
	}
	
	protected function addonsToProduct($productId, $addonsId, $data) {

		$arResult =& $this->arResult;
		$item =& $arResult['GRID']['ROWS'][$productId];

		if (isset($item) && isset($addonsId) && ($addonsId > 0)) {

			if (!isset($item['data']['addons']))
				$item['data']['addons'] = [];	

			$item['data']['addons'][] = $data;
		}
	}

	protected function obtainBasket()
	{
		parent::obtainBasket();

		$recalcItems = array();
		$addonsPrices = array();
		foreach ($this->arResult["BASKET_ITEMS"] as &$basketItem) 
		{
			if (isset($basketItem["PROPS"]) && (count($basketItem["PROPS"]) > 0)) {
				if ($basketItem["PROPS"][0]["CODE"] == "PRODUCT.SET") {
					$recalcItems[] = &$basketItem;
				}
				if ($basketItem["PROPS"][0]["CODE"] == "PRODUCT.LINK") {
					$addonsPrices[$basketItem["PRODUCT_ID"]] = $basketItem;
				}
			}
		}
		
		foreach ($recalcItems as &$basketItem) 
		{
			$price = $basketItem['PRICE'];
			$basePrice = $basketItem['BASE_PRICE'];

			if ($basketItem["PROPS"][0]["CODE"] == "PRODUCT.SET") {
				$addonsIds = explode(",", $basketItem["PROPS"][0]["VALUE"]);
			}
			if (isset($addonsIds) && (count($addonsIds) > 0)) {
				foreach ($addonsIds as $id) {
					$price += $addonsPrices[$id]["PRICE"];
					$basePrice += $addonsPrices[$id]["BASE_PRICE"];
				}
			}
			$basketItem["SUM_NUM"] = $basketItem['QUANTITY'] * $price;
			$basketItem["SUM"] = SaleFormatCurrency($price * $basketItem['QUANTITY'], $this->order->getCurrency());
			$basketItem["SUM_BASE"] = $basketItem['QUANTITY'] * $basePrice;
			$basketItem["SUM_BASE_FORMATED"] = SaleFormatCurrency($basketItem['QUANTITY'] * $basePrice, $this->order->getCurrency());
		}
	}

	protected function obtainPropertiesForIbElements() 
	{
		parent::obtainPropertiesForIbElements();
		
		$deleted = [];
		foreach ($this->arResult['GRID']['ROWS'] as $item)
		{
			if (count($item['data']["PROPS"]) > 0) 
			{
				foreach ($item['data']["PROPS"] as $property) 
				{
					if ($property['CODE'] == 'PRODUCT.LINK')
					{
						//addons
						$this->addonsToProduct($property['VALUE'], $item['data']["ID"], $item['data']);
						$deleted[] = $item['data']["ID"];
					}
				}
			}
		}
		foreach ($deleted as $id) {
			unset($this->arResult['GRID']['ROWS'][$id]);
		}
	}

	protected function makeUserResultArray()
	{
		parent::makeUserResultArray();

		$request =& $this->request;

		if (strlen($request->get('Order_Restaurants')) > 0)
		{
			$this->arUserResult["~ORDER_RESTAURANTS"] = $request->get('Order_Restaurants');
			$this->arUserResult["ORDER_RESTAURANTS"] = htmlspecialcharsbx($request->get('Order_Restaurants'));
		}
	}
}
