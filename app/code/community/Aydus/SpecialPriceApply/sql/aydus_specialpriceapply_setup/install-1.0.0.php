<?php

/**
 * special_price_apply attribute
 *
 * @category    Aydus
 * @package     Aydus_SpecialPriceApply
 * @author      Aydus <davidt@aydus.com>
 */

$installer = $this;
$installer->startSetup();

$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$catalogSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'special_price_apply', array(
		'type'                      => 'int',
		'label'                     => 'Apply Special Price',
		'input'                     => 'boolean',
		'backend'                   => '',
		'source'					=> 'eav/entity_attribute_source_boolean',
		'default_value_yesno'       => '1',
        'default'					=> '1',
		'required'                  => '0',
		'sort_order'                => 10,
		'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
		'user_defined'              => true,
		'apply_to'                  => '',
		'group'                     => 'Prices',		
));

//table to reserve special price dates
$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_specialpriceapply')} (
`product_id` INT(11) NOT NULL,
`special_from_date` DATETIME,
`special_to_date` DATETIME,
PRIMARY KEY ( `product_id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->endSetup();

?>
