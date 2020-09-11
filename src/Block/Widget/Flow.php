<?php declare(strict_types=1);

namespace Itonomy\Flowbox\Block\Widget;

class Flow extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const ENABLE = 'itonomy_flowbox/general/enable';

    protected $_template = "widget/flowbox.phtml";

    private $localeResolver;

    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->localeResolver = $localeResolver;
    }

    public function isFlowboxEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::ENABLE);
    }

    public function getJsConfig(): string
    {
        $this->prepareData();
        return $this->toJson(['flow', 'config']);
    }

    public function getContainerId(): string
    {
        return 'flowbox-' . $this->getFlow() . '-container';
    }

    private function prepareData()
    {
        if ($this->hasData('config')) {
            return;
        }

        $config = [
            'key' => $this->getData('key'),
            'container' => '#' . $this->getContainerId(),
            'locale' => $this->getLocalePrefix(),
        ];

        $flow = $this->getFlow();
        if ($flow === 'dynamic-product') {
            $config['productId'] = $this->getProductId();
        }
        if ($flow === 'dynamic-tag') {
            $config['tags'] = $this->getTags();
            $config['tagsOperator'] = $this->getData('tags_operator');
        }
        $config['show_tag_bar'] = $this->getData('show_tag_bar');
        $this->setData('config', $config);
    }

    private function getFlow(): string
    {
        if (!$this->hasData('flow')) {
            $this->setData('flow', 'default');
        }
        return (string) $this->getData('flow');
    }

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

    private function getProductId(): string
    {
        return (string) $this->getRequest()->getParam('product_id');
    }

    private function getLocalePrefix(): string
    {
        $localeArray = \explode('_', (string) $this->localeResolver->getLocale());
        return \strtolower(
            \reset($localeArray)
        );
    }
}
