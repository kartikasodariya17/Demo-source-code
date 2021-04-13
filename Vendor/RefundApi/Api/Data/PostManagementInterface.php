<?php 
namespace Vendor\RefundApi\Api\Data;
 
 
interface PostManagementInterface
{


    const STOREORDERID = 'storeSeOrderId';
    const STORECODE = 'storeCode';
    const CLIENTORDERID = 'clientOrderId';
    const CLIENTNAME = 'clientName';
    const ITEMCODE = 'itemCode';
    const QUANTITY = 'quantity';
    const PRICE = 'price';
    const DIRECTREFUNDFLAG = 'directRefundflag';

    /**
     * set the storeorderid.
     *
     * @api
     * @return string
     */
    public function setStoreSeOrderId($storeorderid);
    /**
     * Gets the storeorderid.
     *
     * @api
     * @return string
     */
    public function getStoreSeOrderId();
    /**
     * set the storecode.
     *
     * @api
     * @return string
     */
    public function setStoreCode($storecode);
    /**
     * Gets the storecode.
     *
     * @api
     * @return string
     */
    public function getStoreCode();
    /**
     * set the clientorderid.
     *
     * @api
     * @return string
     */
    public function setClientOrderId($clientorderid);
    /**
     * Gets the clientorderid.
     *
     * @api
     * @return string
     */
    public function getClientOrderId();
     /**
     * set the clientname.
     *
     * @api
     * @return string
     */
    public function setClientName($clientname);
    /**
     * Gets the clientname.
     *
     * @api
     * @return string
     */
    public function getClientName();
     /**
     * set the itemcode.
     *
     * @api
     * @return string
     */
    public function setItemCode($itemcode);
    /**
     * Gets the itemcode.
     *
     * @api
     * @return string
     */
    public function getItemCode();
     /**
     * set the quantity.
     *
     * @api
     * @return string
     */
    public function setQuantity($quantity);
    /**
     * Gets the quantity.
     *
     * @api
     * @return string
     */
    public function getQuantity();
     /**
     * set the price.
     *
     * @api
     * @return string
     */
    public function setPrice($price);
    /**
     * Gets the price.
     *
     * @api
     * @return string
     */
    public function getPrice();
     /**
     * set the directrefundflag.
     *
     * @api
     * @return string
     */
    public function setDirectRefundflag($directrefundflag);
    /**
     * Gets the directrefundflag.
     *
     * @api
     * @return string
     */
    public function getDirectRefundflag();
     /**
     * set the refundItems.
     *
     * @api
     * @param mixed $refundItems
     * @return mixed
     */
    public function setRefundItems($refundItems);
    /**
     * Gets the refundItems.
     *
     * @api
     * @param mixed $refundItems
     * @return mixed
     */
    public function getRefundItems();
   
}