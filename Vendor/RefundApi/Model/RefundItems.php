<?php
namespace Vendor\RefundApi\Model;

class RefundItems extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'refundapi_refunditems';

    protected $_cacheTag = 'refundapi_refunditems';

    protected $_eventPrefix = 'refundapi_refunditems';

    protected function _construct()
    {
        $this->_init('Vendor\RefundApi\Model\ResourceModel\RefundItems');
    }

    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
}
