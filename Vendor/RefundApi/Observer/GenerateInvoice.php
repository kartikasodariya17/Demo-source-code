<?php

namespace Vendor\RefundApi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\InvoiceOrderInterface;
use Psr\Log\LoggerInterface;

class GenerateInvoice implements ObserverInterface
{

    protected $invoiceOrderInterface;
    protected $logger;    
    
    public function __construct(
        InvoiceOrderInterface $invoiceOrderInterface,
            LoggerInterface $logger
    ) {
        $this->invoiceOrderInterface = $invoiceOrderInterface;
        $this->logger = $logger;
    }
    /**
     *
     * @param Observer $observer Observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        try{
            $order = $observer->getOrder();
            $this->logger->info("Automatic Invoice Generate Start For Order #".$order->getIncrementId()." " . __CLASS__ ." ". __LINE__);
            $isOffline = $order->getPayment()->getMethodInstance()->isOffline();
            $this->logger->info("isOffline: ".$isOffline." " . __CLASS__ ." ". __LINE__);
            if ($isOffline) {
                $invoiceId = $this->invoiceOrderInterface->execute($order->getId());
                $this->logger->info("Invoice Id #".$invoiceId ." ". __CLASS__ ." ". __LINE__);
            }
        } catch (\Exception $ex) {
            $this->logger->info("Error# ".$ex->getMessage()." " . __CLASS__ ." ". __LINE__);
        }
    }
}
