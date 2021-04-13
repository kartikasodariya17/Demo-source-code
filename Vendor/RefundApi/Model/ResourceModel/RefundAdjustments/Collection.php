<?php
namespace Vendor\RefundApi\Model\ResourceModel\RefundAdjustments;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'vendor_refundapi_refundadjustments_collection';
    protected $_eventObject = 'refundadjustments_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vendor\RefundApi\Model\RefundAdjustments', 'Vendor\RefundApi\Model\ResourceModel\RefundAdjustments');
    }

}

