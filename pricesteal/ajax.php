<?php
// Located in /modules/pricesteal/ajax.php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(dirname(__FILE__) . '/classes/Pricesteal_Settings.php');
require_once(dirname(__FILE__) . '/classes/Pricesteal_Product.php');

function fetchPrice($domElem, $url)
{
	require_once(dirname(__FILE__) . '/classes/simple_html_dom.php'); // required simple dom library

	$html = new simple_html_dom(); // instantiate class
	$html->load_file($url); // read external html page
	$price = $html->find($domElem, 0)->innertext; // find required dom element text

	if((int) $price) // check if valid price, non null
	{
		return (float) $price; // return price
	}
	return NULL; // return null if not found
}

$id_product = Tools::getValue('idProduct');
$return = array();

$pricestealSettings = Pricesteal_Settings::fetchSettings();
if($pricestealSettings)
{
	foreach($pricestealSettings as $key => $value)
	{
		$pricestealProduct = Pricesteal_Product::loadByIdProduct($id_product, $value['id']);
		$productUrl = $pricestealProduct->product_url;
		if($productUrl)
		{
			$price = fetchPrice($value['regex'], $productUrl);
			if($price)
			{
				$return[$key]['competitor'] = $value['competitor'];
				$return[$key]['price']		= $price;
			}
		}
	}
}
echo Tools::jsonEncode( $return );
exit;