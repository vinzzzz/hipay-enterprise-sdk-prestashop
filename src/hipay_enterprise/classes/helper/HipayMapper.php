<?php
/**
 * HiPay Enterprise SDK Prestashop
 *
 * 2017 HiPay
 *
 * NOTICE OF LICENSE
 *
 * @author    HiPay <support.tpp@hipay.com>
 * @copyright 2017 HiPay
 * @license   https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 */

require_once(dirname(__FILE__) . '/../../lib/vendor/autoload.php');
require_once(dirname(__FILE__) . '/dbquery/HipayDBSchemaManager.php');
require_once(dirname(__FILE__) . '/dbquery/HipayDBMapperQuery.php');

/**
 * Handle category and carrier mapping
 *
 * @author      HiPay <support.tpp@hipay.com>
 * @copyright   Copyright (c) 2017 - HiPay
 * @license     https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 * @link    https://github.com/hipay/hipay-enterprise-sdk-prestashop
 */
class HipayMapper
{
    const PS_CAT_LEVEL_DEPTH = 2;
    const HIPAY_CAT_MAPPING = 'category';
    const HIPAY_CARRIER_MAPPING = 'carrier';

    public function __construct($moduleInstance)
    {
        $this->context = Context::getContext();
        $this->module = $moduleInstance;
        $this->logs = $this->module->getLogs();
        $this->dbMapperQuery = new HipayDBMapperQuery($this->module);
        $this->dbSchemaManager = new HipayDBSchemaManager($this->module);
    }

    /**
     * return all Hipay categories from SDK
     * @return array
     */
    public function getHipayCategories()
    {
        return HiPay\Fullservice\Data\Category\Collection::getItems();
    }

    /**
     * return all active prestashop categories (depth level = PS_CAT_LEVEL_DEPTH)
     * @return array
     */
    public function getPrestashopCategories()
    {
        return Category::getCategories(
            $this->context->language->id,
            true,
            false,
            'AND `level_depth` = ' . HipayMapper::PS_CAT_LEVEL_DEPTH
        );
    }

    /**
     * return all Hipay carriers from SDK
     * @return array
     */
    public function getHipayCarriers()
    {
        return HiPay\Fullservice\Data\DeliveryMethod\CollectionModeShipping::getItems();
    }

    /**
     * return all active prestashop carriers (depth level = PS_CAT_LEVEL_DEPTH)
     * @return array
     */
    public function getPrestashopCarriers()
    {
        return Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
    }

    /**
     * retrieve mapped categories
     * @param type $idShop
     * @return array
     */
    public function getMappedCategories($idShop)
    {
        $categoriesDB = $this->dbMapperQuery->getHipayMappedCategories($idShop);

        if (!$categoriesDB) {
            $categories = array();
        } else {
            foreach ($categoriesDB as $cat) {
                $categories[$cat["hp_ps_cat_id"]] = $cat["hp_cat_id"];
            }
        }

        return $categories;
    }

    /**
     * retrieve mapped carriers
     * @param type $idShop
     * @return array
     */
    public function getMappedCarriers($idShop)
    {
        $carriersDB = $this->dbMapperQuery->getHipayMappedCarriers($idShop);

        if (!$carriersDB) {
            $carriers = array();
        } else {
            foreach ($carriersDB as $car) {
                $carriers[$car["hp_ps_carrier_id"]]["mode"] = $car["hp_carrier_mode"];
                $carriers[$car["hp_ps_carrier_id"]]["shipping"] = $car["hp_carrier_shipping"];
                $carriers[$car["hp_ps_carrier_id"]]["preparation_eta"] = $car["preparation_eta"];
                $carriers[$car["hp_ps_carrier_id"]]["delivery_eta"] = $car["delivery_eta"];
            }
        }

        return $carriers;
    }

    /**
     *
     * @param type $PSId
     * @return int
     */
    public function getMappedHipayCatFromPSId($PSId)
    {
        $hipayCatId = $this->dbMapperQuery->getHipayCatFromPSId($PSId);

        if ($hipayCatId) {
            return (int)$hipayCatId["hp_cat_id"];
        }

        return 1;
    }

    /**
     * @param $PSId
     * @return int|null
     */
    public function getMappedHipayCarrierFromPSId($PSId)
    {
        $hipayCarrier = $this->dbMapperQuery->getHipayCarrierFromPSId($PSId);
        if ($hipayCarrier) {
            return $hipayCarrier;
        }

        return null;
    }

    /**
     * create mapping tables
     */
    public function createTable()
    {
        if (!$this->dbSchemaManager->createCatMappingTable() || !$this->dbSchemaManager->createCarrierMappingTable()) {
            $this->logs->logErros('# Cannot create Mapping table');
            die('Module DB Error');
        }
    }

    /**
     * delete mapping tables
     */
    public function deleteTable()
    {
        if (!$this->dbSchemaManager->deleteCatMappingTable() || !$this->dbSchemaManager->deleteCarrierMappingTable()) {
            $this->logs->logErrors('# Cannot delete Mapping table');
        }
    }

    /**
     * save mapping
     *
     * @param $type
     * @param $values
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function setMapping($type, $values)
    {
        switch ($type) {
            case HipayMapper::HIPAY_CAT_MAPPING:
                if (!empty($values)) {
                    $row = array();
                    foreach ($values as $val) {
                        $row[] = array(
                            "hp_ps_cat_id" => pSQL((int)$val["pscat"]),
                            "hp_cat_id" => pSQL((int)$val["hipaycat"]),
                            "shop_id" => $this->context->shop->id
                        );

                        $rootCat = new Category($val["pscat"]);
                        // we mapp all childs of root category
                        foreach ($rootCat->getAllChildren() as $childCat) {
                            $row[] = array(
                                "hp_ps_cat_id" => (int)$childCat->id,
                                "hp_cat_id" => pSQL((int)$val["hipaycat"]),
                                "shop_id" => (int)$this->context->shop->id
                            );
                        }
                    }

                    return $this->dbMapperQuery->setHipayCatMapping($row, $this->context->shop->id);
                }
                return true;
            case HipayMapper::HIPAY_CARRIER_MAPPING:
                if (!empty($values)) {
                    foreach ($values as $val) {
                        $row[] = array(
                            "hp_ps_carrier_id" => pSQL((int)$val["pscar"]),
                            "hp_carrier_mode" => pSQL($val["hipaycarmode"]),
                            "hp_carrier_shipping" => pSQL($val["hipaycarshipping"]),
                            "preparation_eta" => pSQL((int)$val["prepeta"]),
                            "delivery_eta" => pSQL((int)$val["deliveryeta"]),
                            "shop_id" => (int)$this->context->shop->id
                        );
                    }

                    $this->dbMapperQuery->setHipayCarrierMapping($row, $this->context->shop->id);
                }
                return true;
        }
    }

    /**
     * check if id is an hipay category code
     *
     * @param int $catId
     * @return boolean
     */
    public function hipayCategoryExist($catId)
    {
        $hipayCat = $this->getHipayCategories();
        $hipayCatId = array();

        foreach ($hipayCat as $cat) {
            $hipayCatId[] = $cat->getCode();
        }
        return in_array($catId, $hipayCatId);
    }

    /**
     * @param $idCarrierOld
     * @param $idCarrierNew
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function updateCarrier($idCarrierOld, $idCarrierNew)
    {
        $this->logs->logInfos('# UpdateCarrier New ID {$idCarrierNew} Old ID {$idCarrierOld} ');
        $mappedCarrier = $this->getMappedHipayCarrierFromPSId($idCarrierOld);
        if ($idCarrierOld != $idCarrierNew && $mappedCarrier != null) {
            $this->logs->logInfos('# mappedCarrier = ' . print_r($mappedCarrier, true));
            $row = array();
            $row[] = array(
                "hp_ps_carrier_id" => pSQL((int)$idCarrierNew),
                "hp_carrier_mode" => pSQL($mappedCarrier["hp_carrier_mode"]),
                "hp_carrier_shipping" => pSQL($mappedCarrier["hp_carrier_shipping"]),
                "preparation_eta" => pSQL((int)$mappedCarrier["preparation_eta"]),
                "delivery_eta" => pSQL((int)$mappedCarrier["delivery_eta"]),
                "shop_id" => (int)$this->context->shop->id
            );

            $this->dbMapperQuery->setHipayCarrierMapping($row, $this->context->shop->id);
        }

        return true;
    }
}
