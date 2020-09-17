<?php declare(strict_types=1);

namespace Itonomy\Flowbox\Observer;

class SetFlowboxDataOnSuccessPageViewObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(\Magento\Framework\View\LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // TODO: Any enrichment of order data here? Maybe, maybe not..
        // Might be needed in case of multishipping
//        $orderIds = $observer->getEvent()->getOrderIds();
//        if (empty($orderIds) || !is_array($orderIds)) {
//            return;
//        }
//        $block = $this->layout->getBlock('itonomy_flowbox_checkout_success');
//        if ($block instanceof \Magento\Framework\View\Element\Template) {
//            $block->setData('order_ids', $orderIds);
//        }
    }
}
