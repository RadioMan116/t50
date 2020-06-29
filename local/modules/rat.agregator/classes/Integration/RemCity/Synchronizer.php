<?php

namespace Agregator\Integration\RemCity;
use Agregator\IB\Section;
use Agregator\IB\Element;
use Agregator\IB\Elements;
use T50ArrayHelper;
use T50DB;

class Synchronizer
{
	use Traits\Logger;
	const REMCITY_IB_CODE = "remcity";

	protected $api;

	function __construct(){
		$this->api = new Api;
	}

    function sync(){
        if( !$this->syncTypes() )
            return false;

        return $this->syncServices();
    }

	function syncTypes(){
		$types = $this->api->getTypes();
		$types = T50ArrayHelper::keyBy($types, "id");
		if( empty($types) ){
			$this->log("cannot sync types; api not return data");
			return false;
		}

		$sections = Section::getList(self::REMCITY_IB_CODE, true);
		$currentData = T50ArrayHelper::keyBy($sections, "UF_REMCITY_ID");

		$newIds = array_keys($types);
        $currentIds = array_keys($currentData);

        $needAdd = array_diff($newIds, $currentIds);
        $needCheck = array_intersect($newIds, $currentIds);

        T50DB::startTransaction();
        foreach ($needAdd as $id) {
        	$success = (bool) Section::add(self::REMCITY_IB_CODE, [
        		"NAME" => $types[$id]["name"],
        		"UF_REMCITY_ID" => $id,
        	]);

            if( !$success )
                return T50DB::rollback();
        }
        foreach($needCheck as $id){
            if( $currentData[$id]["NAME"] == $types[$id]["name"] )
            	continue;

            $success = Section::update(self::REMCITY_IB_CODE, [
            	"ID" => $currentData[$id]["ID"],
            	"NAME" => $types[$id]["name"],
            ]);

            if( !$success )
                return T50DB::rollback();
        }

        return T50DB::commit();
	}

    function syncServices(){
        $sections = Section::getList(self::REMCITY_IB_CODE, true);
        $typeIdSectionId = [];
        foreach($sections as $section)
            $typeIdSectionId[$section["UF_REMCITY_ID"]] = $section["ID"];

        $updater = new Element(self::REMCITY_IB_CODE);
        $elements = (new Elements(self::REMCITY_IB_CODE))
            ->select("NAME", "IBLOCK_SECTION_ID")
            ->props("REMCITY_ID", "PRICE")
            ->setIndex("PROPERTY_REMCITY_ID_VALUE")
            ->getListFetch();

        $services = $this->api->getServices();

        T50DB::startTransaction();
        foreach($services as $service){
            $sectionId = (int) $typeIdSectionId[$service["model_type_id"]];
            if( $sectionId <= 0 )
                continue;

            $currentService = $elements[$service["id"]];

            if( isset($currentService) ){
                if( !$this->isEqualsServices($service, $currentService) ){
                    $update = $updater->update(
                        $currentService["ID"],
                        ["NAME" => $service["name"]],
                        ["PRICE" => $service["price"]]
                    );
                    if( !$update )
                        return T50DB::rollback();
                }
            } else {
                $create = $updater->create(
                    ["NAME" => $service["name"], "IBLOCK_SECTION_ID" => $sectionId],
                    ["PRICE" => $service["price"], "REMCITY_ID" => $service["id"]]
                );
                if( !$create )
                    return T50DB::rollback();
            }
        }

        return T50DB::commit();
    }

    private function isEqualsServices(array $remCityService, array $t50Service){
        return (
            $remCityService["id"] == $t50Service["PROPERTY_REMCITY_ID_VALUE"]
            && $remCityService["name"] == $t50Service["NAME"]
            && $remCityService["price"] == $t50Service["PROPERTY_PRICE_VALUE"]
        );
    }
}