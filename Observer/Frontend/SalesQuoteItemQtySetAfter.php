<?php


namespace Metagento\AvailableQty\Observer\Frontend;


class SalesQuoteItemQtySetAfter implements
    \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Metagento\AvailableQty\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\CatalogInventory\Model\StockStateProvider
     */
    protected $_stockStateProvider;


    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockItem;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    public function __construct(
        \Metagento\AvailableQty\Helper\Data $helper,
        \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider,
        \Magento\Catalog\Model\Product $product,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_stockStateProvider = $stockStateProvider;
        $this->_product            = $product;
        $this->_stockItem          = $stockItem;
        $this->_catalogSession     = $catalogSession;
        $this->_messageManager     = $messageManager;
        $this->helper              = $helper;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if ( !$this->helper->isEnabled() ) {
            return $this;
        }
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $observer->getEvent()->getItem();
        $product   = $this->_product->loadByAttribute('sku', $quoteItem->getSku());
        $stockQty  = $this->_stockItem->getStockQty($product->getId());
        if ( $stockQty && $quoteItem->getQty() > $stockQty ) {
            $this->_messageManager->addWarningMessage(__("Available Qty for %1 is %2", $product->getName(), $stockQty));
            if ( $this->helper->isAutoAdd() ) {
                $quoteItem->getQuote()->setIsSuperMode(true);
                $quoteItem->getQuote()->setHasError(false);
                $quoteItem->setHasError(false);
                $quoteItem->setData('qty', $stockQty);
            }
        }
        return $this;
    }

}