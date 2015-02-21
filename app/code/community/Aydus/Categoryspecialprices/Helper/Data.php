<?php

/**
 * Category special prices helper
 * 
 * @category    Aydus
 * @package     Aydus_Categoryspecialprices
 * @author		Aydus <davidt@aydus.com>
 */

class Aydus_Categoryspecialprices_Helper_Data extends Mage_Core_Helper_Abstract
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