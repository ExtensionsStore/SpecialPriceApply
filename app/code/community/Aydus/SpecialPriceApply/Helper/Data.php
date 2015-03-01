<?php

/**
 * Special Price Apply helper
 * 
 * @category    Aydus
 * @package     Aydus_SpecialPriceApply
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_SpecialPriceApply_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Special Price apply javascript for category tab Category Special Prices
	 */
	public function getApplyJavascript()
	{
		$applyJavascript = '<script type="application/javascript">
jQuery(function(){
   	specialPriceApply.init();
});
</script>';
		
		return $applyJavascript;
		
	}
}