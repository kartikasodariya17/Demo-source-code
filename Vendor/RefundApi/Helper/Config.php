<?php

namespace Vendor\RefundApi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_AUTOREFUND_ENABLE = 'autorefund/general/enable';    
    
    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    
    /**
     * get config autorefund enable
     *
     * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    public function getAutorefundEnable($storeId)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_AUTOREFUND_ENABLE, $storeScope, $storeId);
    }
}
