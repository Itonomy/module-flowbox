<?php declare(strict_types=1);

namespace Itonomy\Flowbox\Block\Widget;

class Flow extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
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

    public function getJsConfig(): string
    {
        $config = [
            'key' => $this->getData('key'),
            'container' => $this->getData('container'),
            'locale' => $this->localeResolver->getLocale(),
        ];
        $type = $this->getData('flow_type');

        if ($type === 'dynamic-product-flow') {
            $config['product_id'] = $this->getProductId();
        }

        if ($type === 'dynamic-tag-flow') {
            $config['tags'] = $this->getTags();
            $config['tags_condition'] = $this->getData('tags_condition');
        }

        $this->setData('flowbox', [
            'show_tag_bar' => $this->getData('show_tag_bar'),
            'flow' => $type,
            'config' => $config,
        ]);

        return $this->toJson(['flowbox']);
    }

    private function getTags(): array
    {
        // May need to do some magic here later
        return $this->getData('tags');
    }

    private function getProductId(): string
    {
        return (string) $this->getRequest()->getParam('product_id');
    }
}
