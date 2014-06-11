<?php
class Pricesteal_Settings extends ObjectModel
{
	/**
	 * function fetchSettings
	 * used for returning all saved settings for module
	 * @return type
	 */
    public static function fetchSettings(){
		$sql = "
			SELECT *
			FROM `"._DB_PREFIX_."ts_pricesteal_settings` AS tsps   
		";
        $result = Db::getInstance()->ExecuteS($sql);
		return $result;
    }
	
	/**
	 * function addSettings
	 * @param string $competitor
	 * @param string $regex
	 * @return type
	 */
	public static function addSettings($competitor, $regex)
	{
		$sql = "
			INSERT INTO `"._DB_PREFIX_."ts_pricesteal_settings` (`id`, `competitor`, `regex`)
			VALUES (NULL, '{$competitor}', '{$regex}');
		";
        $result = Db::getInstance()->ExecuteS($sql);
		return $result;
	}
	
	public static function updateSettings($id, $competitor, $regex)
	{
		$sql = "
			UPDATE `"._DB_PREFIX_."ts_pricesteal_settings`
			SET `competitor` = '{$competitor}',
			`regex` = '{$regex}'
			WHERE `id` = {$id};
		";
        $result = Db::getInstance()->ExecuteS($sql);
		return $result;
	}
	
	/**
	 * function deleteById
	 * used for deleting settings
	 * @param int $id
	 * @return type
	 */
	public static function deleteById($id)
	{
		$sql = "
			DELETE FROM `"._DB_PREFIX_."ts_pricesteal_settings`
			WHERE `id` = {$id};
		";
        $result = Db::getInstance()->ExecuteS($sql);
		return $result;
	}
}