<?php
namespace Vendor\RefundApi\Model;

class RefundAdjustments extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'refundapi_refundadjustments';

    protected $_cacheTag = 'refundapi_refundadjustments';

    protected $_eventPrefix = 'refundapi_refundadjustments';

    protected function _construct()
    {
        $this->_init('Vendor\RefundApi\Model\ResourceModel\RefundAdjustments');
    }

    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
}
