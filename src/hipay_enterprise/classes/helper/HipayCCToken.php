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

require_once(dirname(__FILE__) . '/dbquery/HipayDBTokenQuery.php');

/**
 * handle credit card token (OneClik payment)
 *
 * @author      HiPay <support.tpp@hipay.com>
 * @copyright   Copyright (c) 2017 - HiPay
 * @license     https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 * @link    https://github.com/hipay/hipay-enterprise-sdk-prestashop
 */
class HipayCCToken
{

    public function __construct($moduleInstance)
    {
        $this->module = $moduleInstance;
        $this->logs = $this->module->getLogs();
        $this->context = Context::getContext();
        $this->dbTokenQuery = new HipayDBTokenQuery($this->module);
    }

    /**
     * save credit card token and other informations
     * @param int $customerId
     * @param array $card
     */
    public function saveCCToken($customerId, $card)
    {

        if (!$this->tokenExist($customerId, $card["token"])) {
            $this->module->getLogs()->logInfos("# SaveCCToken for customer ID $customerId");
            $card = array_merge(array("customer_id" => $customerId), $card);

            $this->dbTokenQuery->setCCToken($card);
        }
    }

    /**
     * get all saved credit card from customer
     * @param type $customerId
     * @return type
     */
    public function getSavedCC($customerId)
    {
        return $this->dbTokenQuery->getSavedCC($customerId);
    }

    /**
     * check if customer credit card token exit
     * @param type $customerId
     * @param type $token
     * @return type
     */
    public function tokenExist($customerId, $token)
    {
        return $this->dbTokenQuery->ccTokenExist($customerId, $token);
    }

    /**
     * get token informations
     * @param type $customerId
     * @param type $token
     * @return type
     */
    public function getTokenDetails($customerId, $token)
    {
        return $this->dbTokenQuery->getToken($customerId, $token);
    }

    /**
     * delete customer credit card token
     * @param type $customerId
     * @param type $tokenId
     * @return type
     */
    public function deleteToken($customerId, $tokenId)
    {
        return $this->dbTokenQuery->deleteToken($customerId, $tokenId);
    }

    /**
     *
     * @param type $customerId
     * @return type
     */
    public function deleteAllToken($customerId)
    {
        return $this->dbTokenQuery->deleteAllToken($customerId);
    }
}
