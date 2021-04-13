<?php
namespace Vendor\RefundApi\Model;

use Vendor\RefundApi\Api\PostManagementInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\ShipmentItemInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterfaceFactory;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Api\RefundOrderInterface;
use Magento\Sales\Api\RefundInvoiceInterface;
use Vendor\RefundApi\Helper\Config;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Framework\Filesystem\Driver\File;

class PostManagement implements PostManagementInterface
{

    protected $orderRepository;
    protected $_creditmemoLoader;
    protected $_creditmemoManagement;
    protected $_refundAdjustmentsFactory;
    protected $_refundItemsFactory;
    protected $_orderFactory;
    protected $_searchCriteriaBuilder;
    protected $_shipmentItemCreationInterface;
    protected $_shipmentInterface;
    protected $_shipOrderInterface;
    protected $_shipmentCommentCreationInterface;
    protected $creditmemoItemCreationFactory;
    protected $creditmemoCommentCreation;
    protected $creditmemoArgumentsInterface;
    protected $refundOrderInterface;
    protected $refundInvoiceInterface;
    protected $configHelper;
    protected $invoiceOrderInterface;
    protected $file;
    
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Vendor\RefundApi\Model\RefundAdjustmentsFactory $refundAdjustmentsFactory,
        \Vendor\RefundApi\Model\RefundItemsFactory $refundItemsFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShipmentItemInterfaceFactory $shipmentItemCreationInterface,
        ShipmentInterface $shipmentInterface,
        ShipOrderInterface $shipOrderInterface,
        ShipmentCommentCreationInterface $shipmentCommentCreationInterface,
        RefundOrderInterface $refundOrderInterface,
        RefundInvoiceInterface $refundInvoiceInterface,
        CreditmemoItemCreationInterfaceFactory $creditmemoItemCreationFactory,
        CreditmemoCommentCreationInterface $creditmemoCommentCreation,
        CreditmemoCreationArgumentsInterface $creditmemoArgumentsInterface,
        Config $configHelper,
        InvoiceOrderInterface $invoiceOrderInterface,
            File $file
    ) {
        $this->orderRepository = $orderRepository;
        $this->_creditmemoLoader = $creditmemoLoader;
        $this->_creditmemoManagement = $creditmemoManagement;
        $this->_refundAdjustmentsFactory = $refundAdjustmentsFactory;
        $this->_refundItemsFactory = $refundItemsFactory;
        $this->_orderFactory = $orderFactory;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_shipmentItemCreationInterface = $shipmentItemCreationInterface;
        $this->_shipOrderInterface = $shipOrderInterface;
        $this->_shipmentInterface = $shipmentInterface;
        $this->_shipmentCommentCreationInterface = $shipmentCommentCreationInterface;
        $this->refundOrderInterface = $refundOrderInterface;
        $this->refundInvoiceInterface = $refundInvoiceInterface;
        $this->creditmemoItemCreationFactory = $creditmemoItemCreationFactory;
        $this->creditmemoCommentCreation = $creditmemoCommentCreation;
        $this->creditmemoArgumentsInterface = $creditmemoArgumentsInterface;
        $this->configHelper = $configHelper;
        $this->invoiceOrderInterface = $invoiceOrderInterface;        
        $this->file = $file;
    }
    public function getPost($highlightData) 
    {
        if (!empty($highlightData)) {
            $totalrefundprice = $totalRefundAmountInital = 0;
            $creditMemoData = array();
            $refundItems = array();
            $adjustmentItems = array();
            

            // Credit memo data
            $creditMemoData['do_offline'] = 1;
            $creditMemoData['shipping_amount'] = 0;
            $creditMemoData['adjustment_positive'] = 0;
            $creditMemoData['adjustment_negative'] = 0;
            $creditMemoData['comment_text'] = '';
            $creditMemoData['send_email'] = 0;

            foreach ($highlightData as $value) {
                $order_id = $value->getStoreSeOrderId();
                $client_order_id = $value->getClientOrderId();
                $storecode = $value->getStoreCode();
                $directRefundflag = $value->getDirectRefundflag();
                if ($directRefundflag == 1) {
                    $item_arr_refund = $value->getRefundItems();
                } else {
                    $item_arr_delivered = $value->getRefundItems();
                }

                break;
            }

            try {
                // $order = $this->orderRepository->get($order_id);
                // $order = $this->_orderFactory->create()->loadByIncrementId($order_id);
                $searchCriteria = $this->_searchCriteriaBuilder->addFilter('increment_id', $order_id)->create();
                $order = $this->orderRepository->getList($searchCriteria);
                foreach ($order as $orderData) {
                    $orderId = (int) $orderData->getId();
                    $incrementId = $orderData->getIncrementId();
                    $orderState = $orderData->getState();
                    $orderItems = $orderData->getAllItems();
                    $order = $orderData;
                    break;
                }
                
                $storeId = $order->getStoreId();
                $autorefundEnable = $this->configHelper->getAutorefundEnable($storeId);
                if ($autorefundEnable){
                    $creditMemoData['do_offline'] = 0;
                }

                $invoice_id = 0;
                $isOffline = $order->getPayment()->getMethodInstance()->isOffline();
                
                //Generate invoice for offline payment
                if ($isOffline){
                    $this->generateInvoice($order);
                    $orderFactory = $this->_orderFactory->create();
                    $order = $orderFactory->loadByIncrementId($order_id);
                    $autorefundEnable = 0;
                    $creditMemoData['do_offline'] = 1;
                }                
                
                foreach ($order->getInvoiceCollection() as $invoice)
                {
                    $invoice_id = $invoice->getId();
                }


                //Generate Shipment
                $this->generateShipment($orderId);
                
                $refundAdjustments = $this->_refundAdjustmentsFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('storese_order_id', array('eq' => $order_id))
                    ->addFieldToFilter('refund_type', array('eq' => $directRefundflag));
                if (count($refundAdjustments) > 0) {
                    return $this->setResponse('400', null, 0, 'Credit memo already generated for this order.');
                }
                
                if (!$order->canCreditmemo()){
                    return $this->setResponse('302', null, 0, 'Invalid order. We can not generate creditmemo for this order.');
                }

                $totalRefundPriceCreditMemo = 0;
                $totalRefundPricePriceDiff = 0;
                $totalRefundPrice = 0;

                $creditMemoEntries = array();
                $priceDiffTableEntries = array();

                if ($directRefundflag == 1) {
                    // In Request, items of some qty to be refunded is sent , and entire amount is generated by credit memo
                    $i = 0;
                    
                    foreach ($orderItems as $orderItem){
                        foreach ($item_arr_refund as $item_refund){
                            if ($orderItem->getSku() == $item_refund['itemCode']){
                                $totalRefundPriceCreditMemo += $orderItem->getRowTotal() - $orderItem->getDiscountAmount();
                                $adjustmentItems[$i]['sku'] = $orderItem->getSku();
                                $adjustmentItems[$i]['price_storese'] = ($orderItem->getRowTotal() - $orderItem->getDiscountAmount())/$orderItem->getQtyOrdered();
                                $adjustmentItems[$i]['price_fulfilled'] = ($orderItem->getRowTotal() - $orderItem->getDiscountAmount())/$orderItem->getQtyOrdered();
                                $adjustmentItems[$i]['qty_storese'] = $orderItem->getQtyOrdered();
                                $adjustmentItems[$i]['qty_fulfilled'] = 0;
                                $adjustmentItems[$i]['refund_amount_qty'] = $orderItem->getRowTotal() - $orderItem->getDiscountAmount();
                                $adjustmentItems[$i]['refund_amount_difference'] = 0;
                                $creditmemoItemCreation = $this->creditmemoItemCreationFactory->create();
                                $itemToCredit[] = $creditmemoItemCreation->setQty($item_refund['quantity'])->setOrderItemId($orderItem->getId());
                                $i++;
                            }
                        }                    
                    }

                    $creditMemoData['comment_text'] = "Creditmemo generated via API for direct refund.";
                    $this->creditmemoCommentCreation->setComment($creditMemoData['comment_text']);
                    $this->creditmemoArgumentsInterface->setShippingAmount(0);
                    if ($isOffline){
                        $creditmemoId = $this->refundOrderInterface->execute(
                            $orderId,
                            $itemToCredit,
                            false,
                            true,
                            $this->creditmemoCommentCreation,
                            $this->creditmemoArgumentsInterface
                        );
                    } else {
                        if (!$autorefundEnable){
                            $creditmemoId = $this->refundOrderInterface->execute(
                                $orderId,
                                $itemToCredit,
                                false,
                                true,
                                $this->creditmemoCommentCreation,
                                $this->creditmemoArgumentsInterface
                            );
                        } else{
                            $creditmemoId = $this->refundInvoiceInterface->execute(
                                $invoice_id,
                                $itemToCredit,
                                true,
                                false,
                                true,    
                                $this->creditmemoCommentCreation,
                                $this->creditmemoArgumentsInterface
                            );
                        }
                    }
                } else if ($directRefundflag == 0) {
                    // In Request , items delivered to customer is sent , we should refund the price of x qty of items which are not delivered to the customer, also the refund the difference amount if the price of the product decreaes later

                    $i = 0;
                    foreach ($orderItems as $item_actual) {
                        $item_actual_found_in_item_delivered = 0;
                        foreach ($item_arr_delivered as $key => $item_delivered) {
                            if ($item_actual->getSku() == $item_delivered['itemCode']) {
                                $item_actual_remain_qty = $item_actual->getQtyOrdered() - $item_actual->getQtyRefunded();

                                if ($item_delivered['quantity'] > $item_actual_remain_qty) {
                                    //Invalid case
                                    return $this->setResponse('302', null, 0, 'SKU: ' . $item_actual->getSku() . ' Invalid refund qty.');
                                }

                                $item_actual_found_in_item_delivered = 1;
                                if ($item_actual->getQtyOrdered() >= $item_delivered['quantity']) {
                                    //For Non Delivered items, refund items and generate credit memo
                                    $qtydiff = $item_actual->getQtyOrdered() - $item_delivered['quantity'];
                                    $totalRefundPriceCreditMemo += $qtydiff * (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                                    //TODO add to credit memo array
                                    if ($qtydiff > 0) {
                                        $refundItems[$item_actual->getItemId()] = array('qty' => $qtydiff);
                                    }

                                    $adjustmentItems[$i]['sku'] = $item_actual->getSku();
                                    $adjustmentItems[$i]['price_storese'] = (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                                    $adjustmentItems[$i]['price_fulfilled'] = $item_delivered['price'];
                                    $adjustmentItems[$i]['qty_storese'] = $item_actual->getQtyOrdered();
                                    $adjustmentItems[$i]['qty_fulfilled'] = $item_delivered['quantity'];
                                    $adjustmentItems[$i]['refund_amount_qty'] = $qtydiff * (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                                    $adjustmentItems[$i]['refund_amount_difference'] = 0;

                                    //For Delivered items, refund amount diff and insert into price diff
                                    if ((($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered()) > $item_delivered['price']) {
                                        $pricediff = (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered()) - $item_delivered['price'];
                                        $totalRefundPricePriceDiff += $item_delivered['quantity'] * $pricediff;
                                        $adjustmentItems[$i]['refund_amount_difference'] = $item_delivered['quantity'] * $pricediff;
                                    }
                                } else {
                                    return $this->setResponse('302', null, 0, 'SKU: ' . $item_actual->getSku() . ' Delivered qty ' . $item_delivered['quantity'] . ' can never be greater than ordered qty ' . $item_actual->getQtyOrdered());
                                }
                            }
                        }

                        if ($item_actual_found_in_item_delivered == 0) {
                            // item in actual order is not delivered, so it is missing in request , so refund entire qty of not found item
                            $totalRefundPriceCreditMemo += $item_actual->getQtyOrdered() * (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                            //TODO add to credit memo array
                            $refundItems[$item_actual->getItemId()] = array('qty' => $item_actual->getQtyOrdered());
                            $adjustmentItems[$i]['sku'] = $item_actual->getSku();
                            $adjustmentItems[$i]['price_storese'] = (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                            $adjustmentItems[$i]['price_fulfilled'] = (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                            $adjustmentItems[$i]['qty_storese'] = $item_actual->getQtyOrdered();
                            $adjustmentItems[$i]['qty_fulfilled'] = 0;
                            $adjustmentItems[$i]['refund_amount_qty'] = $item_actual->getQtyOrdered() * (($item_actual->getRowTotal() - $item_actual->getDiscountAmount())/$item_actual->getQtyOrdered());
                            $adjustmentItems[$i]['refund_amount_difference'] = 0;
                        }

                        $i++;
                    }
                }

                $totalRefundPrice = $totalRefundPriceCreditMemo + $totalRefundPricePriceDiff;
                
                if (!$isOffline && $autorefundEnable){
                    $creditMemoData['adjustment_positive'] = $totalRefundPricePriceDiff;
                }
                
                if ($directRefundflag == 0) {
                    if (sizeof($refundItems) > 0) {
                        $creditMemoData['items'] = $refundItems;
                        try {
                            $this->_creditmemoLoader->setOrderId($orderId);
                            $this->_creditmemoLoader->setInvoiceId($invoice_id);
                            $this->_creditmemoLoader->setCreditmemo($creditMemoData);
                            $creditmemo = $this->_creditmemoLoader->load();
                            if ($creditmemo) {
                                if (!$creditmemo->isValidGrandTotal()) {
                                    $message = __('The credit memo\'s total must be positive.');
                                    return $this->setResponse('302', null, 0, $message);
                                }

                                $creditmemo->getOrder()->setCustomerNoteNotify(0);
                                if ($isOffline){
                                    $this->_creditmemoManagement->refund($creditmemo, true);   
                                }else {
                                    $this->_creditmemoManagement->refund($creditmemo, $autorefundEnable ? false : true);   
                                }                                
                            }
                        } catch (exception $e) {
                            return $this->setResponse('302', null, 0, $e->getMessage());
                        }
                    }
                }

                $refundAdjustmentsModel = $this->_refundAdjustmentsFactory->create();
                $refundAdjustmentsModel->setStoreseOrderId($incrementId);
                $refundAdjustmentsModel->setClientOrderId($incrementId);
                $refundAdjustmentsModel->setStorecode($storecode);
                $refundAdjustmentsModel->setRefundAmount($totalRefundPrice);
                $refundAdjustmentsModel->setRefundType($directRefundflag);
                $refundAdjustmentsModel->setStatus($autorefundEnable);
                $refundAdjustmentsModel->save();
                $refundId = $refundAdjustmentsModel->getId();
                if ($refundId) {
                    $refundItemsModel = $this->_refundItemsFactory->create();
                    foreach ($adjustmentItems as $adjustmentItem) {
                        $adjustmentItem['refund_id'] = $refundId;
                        $refundItemsModel->setData($adjustmentItem);
                        $refundItemsModel->save();
                        $refundItemsModel->unsetData();
                    }
                }

                return $this->setResponse('200', $order_id, $totalRefundPrice, 'Success');
            } catch (exception $e) {
                return $this->setResponse('301', null, 0, 'OrderId not found.');
            }
        } else {
            return $this->setResponse('302', null, 0, 'No data found or invalid json data');
        }
    }
    public function setResponse($status, $order_id, $totalamount, $comments) 
    {
        $response[] = array(
            'status' => $status,
            'client_order' => $order_id,
            'store_se_order' => $order_id,
            'totalRefundAmount' => $totalamount,
            'timeStamp' => strtotime("now"),
            'returnTender' => "razorpay",
            'comments' => $comments
                );
        return $response;
    }
    
    protected function generateShipment($orderId)
    {
        try{
            $this->printLog('In generateshipment '.__CLASS__." on line no ".__LINE__);
            $this->printLog("Order ID #".$orderId);
            $comment = "Shipment generated via refund API.";
            $this->_shipmentCommentCreationInterface->setComment($comment);
            $shipmentId = $this->_shipOrderInterface->execute( 
                $orderId, 
                [], 
                false,
                true,
                $this->_shipmentCommentCreationInterface    
            );
            $this->printLog("Shipment ID #".$shipmentId);
        } catch (\Exception $ex) {
            $this->printLog("Exception: ".$ex->getMessage().__CLASS__." on ".__LINE__);
            return $this->setResponse('302', null, 0, $ex->getMessage());
        }
    }
    
    protected function generateInvoice($order)
    {
        try {
            if ($order->canInvoice()){
                $this->invoiceOrderInterface->execute($order->getId());
            }
        } catch (\Exception $ex) {
            return $this->setResponse('302', null, 0, $ex->getMessage());
        }
    }
    
    /**
     * Log errors
     * @param string $text 
     */
    protected function printLog($text, $filename = 'refundapi') 
    {
        // create directory if not exists
        $directory = BP . '/var/log/shipment';
        $directory = ($this->file->createDirectory($directory)) ? $directory : BP.'/var/log';
        $writer = new \Zend\Log\Writer\Stream($directory.'/'.$filename.'_'.date('d_m_Y').'.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($text);
    }
}
