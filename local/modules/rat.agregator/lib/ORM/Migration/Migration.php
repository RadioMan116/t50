<?php

namespace ORM\Migration;

use Bitrix\Main\Entity;
use Bitrix\Highloadblock\DataManager as HLDataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HBT;
use Agregator\Common\ReportXml;

abstract class Migration
{
	use Traits\Errors;

	protected $entity;

	function setOrm($enityName = ""){
		$this->entity = HBT::getList(["filter" => ["=NAME" => $enityName]])->Fetch();
	}

	function getEntity(){
		return $this->entity;
	}

	abstract function exec();

	protected function getData($addFieldKeys = false){
		global $USER_FIELD_MANAGER;
		$res = \CUserTypeEntity::GetList(
			array(),
			array(
				'ENTITY_ID' => 'HLBLOCK_' . $this->entity['ID']
			)
		);
		$arResult = array();
		while ($row = $res->fetch())
		{
			$row = \CUserTypeEntity::getById($row['ID']);
			$row['BASE_TYPE'] = '';
			if (isset($USER_FIELD_MANAGER))
			{
				$type = $USER_FIELD_MANAGER->GetUserType($row['USER_TYPE_ID']);
				if (is_array($type) && isset($type['BASE_TYPE']))
				{
					$row['BASE_TYPE'] = $type['BASE_TYPE'];
					// get enums
					if ($type['BASE_TYPE'] == 'enum')
					{
						$i = 0;
						$row['enums'] = array();
						$enumValues = array();
						$resE = \CUserFieldEnum::GetList(
							array(),
							array(
								'USER_FIELD_ID' => $row['ID']
							)
						);
						while ($rowE = $resE->fetch())
						{
							$row['enums'][] = $rowE;
							$enumValues[] = $rowE['VALUE'];
						}
					}
				}
			}
			// check some settings
			if (isset($row['SETTINGS']) && is_array($row['SETTINGS']))
			{
				if (isset($row['SETTINGS']['HLBLOCK_ID']))
				{
					$hid = $row['SETTINGS']['HLBLOCK_ID'];
					$row['SETTINGS']['HLBLOCK_TABLE'] = $hls[$hid]['TABLE_NAME'];
				}
				if (isset($row['SETTINGS']['EXTENSIONS']) && is_array($row['SETTINGS']['EXTENSIONS']) && $row['USER_TYPE_ID'] == 'file')
				{
					$row['SETTINGS']['EXTENSIONS'] = implode(', ', array_keys($row['SETTINGS']['EXTENSIONS']));
				}
			}
			$row['enum_values'] = $enumValues;
			if( $addFieldKeys )
				$arResult[$row["FIELD_NAME"]] = $row;
			else
				$arResult[] = $row;
		}
		return $arResult;
	}
}