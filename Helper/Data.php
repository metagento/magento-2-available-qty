<?php


namespace Metagento\AvailableQty\Helper;


class Data extends
    \Magento\Framework\App\Helper\AbstractHelper
{

    public function isEnabled( $storeId = null )
    {
        return $this->scopeConfig->getValue('availableqty/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isAutoAdd( $storeId = null )
    {
        return $this->scopeConfig->getValue('availableqty/general/auto_add', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}