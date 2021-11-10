<?php


namespace Itonomy\Flowbox\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Framework\Data\OptionSourceInterface;


class CheckoutIdentifier implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $collection;


    /**
     * @param Collection $collection
     */
    public function __construct(
        Collection $collection
    )
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function getUniqueAttributes(): array
    {
        $collection = $this->collection;

        $collection->addFieldToFilter(
            'entity_type_id',
            ['eq' => '4']
        );
        $collection->addFieldToFilter(
            'is_unique',
            ['eq' => '1']
        );
        return $collection->toArray();
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $items = $this->getUniqueAttributes();
        $options = [];

        if (array_key_exists('items', $items)) {
            foreach ($items['items'] as $item) {
                $options[] = ['value' => $item['attribute_code'],
                    'label' => $item['frontend_label']
                ];
            }
        }
        return $options;
    }
}
