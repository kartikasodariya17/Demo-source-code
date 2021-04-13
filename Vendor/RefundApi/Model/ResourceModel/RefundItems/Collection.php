<?php
namespace Vendor\RefundApi\Model\ResourceModel\RefundItems;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'vendor_refundapi_refunditems_collection';
    protected $_eventObject = 'refunditems_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vendor\RefundApi\Model\RefundItems', 'Vendor\RefundApi\Model\ResourceModel\RefundItems');
    }

}

