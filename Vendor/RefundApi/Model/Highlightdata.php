<?php
namespace Vendor\RefundApi\Model;
class Highlightdata implements \Vendor\RefundApi\Api\Data\PostManagementInterface
{
    protected $storeSeOrderId;
    protected $storeCode;
    protected $clientOrderId;
    protected $clientName;
    protected $itemCode;
    protected $quantity;
    protected $price;
    protected $directRefundflag;
    protected $refundItems;
    /**
     * Gets the storeorderid.
     *
     * @api
     * @return string
     */
    public function setStoreSeOrderId($storeSeOrderId)
    {
        $this->storeSeOrderId = $storeSeOrderId;
    }
    /**
     * Gets the storeorderid.
     *
     * @api
     * @return string
     */
    public function getStoreSeOrderId()
    {
        return $this->storeSeOrderId;
    }
      /**
     * set the storecode.
     *
     * @api
     * @return string
     */
    public function setStoreCode($storecode)
    {
        $this->storeCode = $storecode;
    }
    /**
     * Gets the storecode.
     *
     * @api
     * @return string
     */
    public function getStoreCode()
    {
        return $this->storeCode;
    }
    /**
     * set the clientorderid.
     *
     * @api
     * @return string
     */
    public function setClientOrderId($clientorderid)
    {
        $this->clientOrderId = $clientorderid;
    }
    /**
     * Gets the clientorderid.
     *
     * @api
     * @return string
     */
    public function getClientOrderId()
    {
        return $this->clientOrderId;
    }
     /**
     * set the clientname.
     *
     * @api
     * @return string
     */
    public function setClientName($clientname)
    {
        $this->clientName = $clientname;
    }
    /**
     * Gets the clientname.
     *
     * @api
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }
     /**
     * set the itemcode.
     *
     * @api
     * @return string
     */
    public function setItemCode($itemcode)
    {
        $this->itemCode = $itemcode;
    }
    /**
     * Gets the itemcode.
     *
     * @api
     * @return string
     */
    public function getItemCode()
    {
        return $this->itemCode;
    }
     /**
     * set the quantity.
     *
     * @api
     * @return string
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    /**
     * Gets the quantity.
     *
     * @api
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
     /**
     * set the price.
     *
     * @api
     * @return string
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
    /**
     * Gets the price.
     *
     * @api
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }
     /**
     * set the directrefundflag.
     *
     * @api
     * @return string
     */
    public function setDirectRefundflag($directrefundflag)
    {
        $this->directRefundflag = $directrefundflag;
    }
    /**
     * Gets the directrefundflag.
     *
     * @api
     * @return string
     */
    public function getDirectRefundflag()
    {
        return $this->directRefundflag;
    }
     /**
     * set the refundItems.
     *
     * @api
     * @param mixed $refundItems
     * @return mixed
     */
    public function setRefundItems($refundItems)
    {
        $this->refundItems = $refundItems;
    }
    /**
     * Gets the refundItems.
     *
     * @api
     * @param mixed $refundItems
     * @return mixed
     */
    public function getRefundItems()
    {
        return $this->refundItems;   
    }
    
}