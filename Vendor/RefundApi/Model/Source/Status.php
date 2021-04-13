<?php
namespace Vendor\RefundApi\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Item status functionality model
 */
class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Item Status values
     */
    const STATUS_COMPLETE = 1;

    const STATUS_PENDING = 0;

    /**#@-*/

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return array(self::STATUS_COMPLETE => __('Complete'), self::STATUS_PENDING => __('Pending'));
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = array();

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = array('value' => $index, 'label' => $value);
        }

        return $result;
    }
}