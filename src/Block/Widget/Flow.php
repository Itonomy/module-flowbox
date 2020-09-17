<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

namespace Itonomy\Flowbox\Block\Widget;

class Flow extends \Itonomy\Flowbox\Block\Base implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = "widget/flow.phtml";

    /**
     * @var \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface
     */
    private $getSkusByProductIds;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getSkusByProductIds = $getSkusByProductIds;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
                'flow' => $flow,
                'key' => $this->escapeHtml((string) $this->getData('key')),
                'lazyload' => (bool) ($this->getData('lazyload') ?: true),
                'locale' => (string) $this->pageConfig->getElementAttribute('html', 'lang'),
            ];

            if ($flow === static::FLOW_TYPE_DYNAMIC_PRODUCT) {
                $config['productId'] = $this->getProductIdentifier();
            }

            if ($flow === static::FLOW_TYPE_DYNAMIC_TAG) {
                $config['tags'] = $this->getTags();
                $config['tagsOperator'] = $this->getTagsOperator();
                $config['showTagBar'] = (bool) $this->getData('show_tag_bar');
            }

            $this->setData('flowbox', $config);
        } catch (\Exception $e) {
            $errorMessage = $this->escapeHtml(
                (string) __(
                    '%flowbox: could not compile configuration: %error',
                    ['flowbox' => 'Flowbox', 'error' => $e->getMessage()]
                )
            );
            $this->addError($errorMessage);
            $this->_logger->error($errorMessage, ['exception' => $e]);
        }
    }

    /**
     * Return flow identifier
     * @return string
     */
    private function getFlow(): string
    {
        if (!$this->hasData('flow')) {
            $this->setData('flow', 'default');
        }
        $flow = (string) $this->getData('flow');
        return $this->escapeHtml($flow);
    }

    /**
     * Return array of tags
     * @return string[]
     */
    private function getTags(): array
    {
        return \explode(
            ',',
            $this->escapeHtml(
                \preg_replace(
                    '/\s+/',
                    '',
                    (string) $this->getData('tags')
                )
            )
        );
    }

    /**
     * Return product identifier
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductIdentifier(): ?string
    {
        // Return product ID setting from widget if there is one
        $sku = $this->getData('product_id');
        if (\is_string($sku)) {
            return $sku;
        }

        // Return product ID attribute value for currently viewed product
        $productIdAttr = (string) $this->getData('product_id_attribute');
        if ($productIdAttr === 'custom') {
            $productIdAttr = (string) $this->getData('product_id_attribute_code');
        }

        $productId = $this->getRequest()->getParam('id');
        if ($productIdAttr === 'sku') {
            $productSkus = $this->getSkusByProductIds->execute([$productId]);
            return \reset($productSkus);
        }
        return $this->loadProductIdentifier($productIdAttr, $productId);
    }

    /**
     * @param $attributeCode
     * @param $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function loadProductIdentifier($attributeCode, $productId): string
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $productId)->setPageSize(1);

        $products = $this->productRepository->getList(
            $searchCriteria->create()
        )->getItems();

        $product = \reset($products);
        if ($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            $value = (string) $product->getData($attributeCode);
            if (empty($value)) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __(
                        'Attribute %attribute_code not set on product with entity_id=%product_id',
                        ['attribute_code' => $attributeCode, 'product_id' => $productId]
                    )
                );
            }
            return $value;
        }
        throw new \Magento\Framework\Exception\NoSuchEntityException(
            __('No product found with entity_id=%product_id', ['product_id' => $productId])
        );
    }

    private function getTagsOperator(): string
    {
        return (string) ($this->getData('tags_operator') ?: 'any');
    }
}
