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
$id_settings = Tools::getValue('idSettings');
$return = array();


$pricestealProduct = Pricesteal_Product::loadByIdProduct($id_product, $id_settings);
$productUrl = $pricestealProduct->product_url;
if($productUrl)
{
	$settings = Pricesteal_Settings::find($id_settings);
	if($settings)
	{
		$price = fetchPrice($settings[0]['regex'], $productUrl);
		if($price)
		{
			$return['price']		= $price;
		}
	}
}

echo Tools::jsonEncode( $return );
exit;