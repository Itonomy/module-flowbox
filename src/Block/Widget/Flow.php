<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Itonomy\Flowbox\Block\Widget;

class Flow extends \Itonomy\Flowbox\Block\Base implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = "widget/flow.phtml";

    /**
     * @var \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface
     */
    private $getSkusByProductIds;

    public function __construct(
        \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getSkusByProductIds = $getSkusByProductIds;
    }

    /**
     * @inheritDoc
     */
    protected function prepareConfig(): void
    {
        try {
            $flow = $this->getFlow();
            $config = [
                'debug' => $this->isDebugJavaScript(),
                'lazyload' => (bool) $this->getData('lazyload'),
                'flow' => $flow,
                'key' => (string) $this->getData('key'),
                'locale' => (string) $this->pageConfig->getElementAttribute('html', 'lang'),
            ];

            if ($flow === 'dynamic-product') {
                $config['productId'] = $this->getProductSku();
            }

            if ($flow === 'dynamic-tag') {
                $config['tags'] = $this->getTags();
                $config['tagsOperator'] = $this->getData('tags_operator');
                $config['showTagBar'] = (bool) $this->getData('show_tag_bar');
            }

            $this->setData('flowbox', $config);
        } catch (\Exception $e) {
            $errorMessage = (string) __(
                '%flowbox: could not compile configuration: %error',
                ['flowbox' => 'Flowbox', 'error' => $e->getMessage()]
            );
            $this->addError($errorMessage);
            $this->_logger->error($errorMessage, ['exception' => $e]);
        }
    }

    /**
     * Return flow type
     * @return string
     */
    private function getFlow(): string
    {
        if (!$this->hasData('flow')) {
            $this->setData('flow', 'default');
        }
        return (string) $this->getData('flow');
    }

    /**
     * Return array of tags
     * @return string[]
     */
    private function getTags(): array
    {
        return \explode(
            ',',
            \preg_replace(
                '/\s+/',
                '',
                (string) $this->getData('tags')
            )
        );
    }

    /**
     * Return product sku from widget configuration or currently viewed product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductSku(): ?string
    {
        // Return product ID setting from widget if there is one
        $sku = $this->getData('product_id');
        if (\is_string($sku)) {
            return $sku;
        }

        // Otherwise return SKU for currently viewed product
        $productId = $this->getRequest()->getParam('id');
        $productSkus = $this->getSkusByProductIds->execute([$productId]);
        return \reset($productSkus);
    }
}
