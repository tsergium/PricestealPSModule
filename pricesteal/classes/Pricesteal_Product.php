<?php
class Pricesteal_Product extends ObjectModel
{
	public $id_pricesteal;
	public $id_product;
	public $product_url;
	
    public static $definition = array(
        'table'				=> 'ts_pricesteal_products',
        'primary'			=> 'id',
        'multilang'			=> false,
        'fields'			=> array(
            'id_settings'	=> array(
				'type' => self::TYPE_INT,
				'validate' => 'isInt',
				'required' => TRUE
				),
            'id_product'	=> array(
				'type' => self::TYPE_INT,
				'validate' => 'isInt',
				'required' => TRUE
				),
            'product_url'	=> array(
				'type' => self::TYPE_HTML,
				'validate' => 'isString'
			),
        ),
    );
	
    public static function loadByIdProduct($id, $idSettings){
		$sql = "
            SELECT *
            FROM `"._DB_PREFIX_."ts_pricesteal_products` AS tsp
			WHERE tsp.`id_product` = {$id} AND tsp.id_settings = {$idSettings}
		";
		
        $result = Db::getInstance()->getRow($sql);
        return new Pricesteal_Product($result['id']);
    }
}

