<?php

namespace Agregator\Components;

use Agregator\Components\BaseComponent;


class ControllerAjaxComponent
{
	private $component;
	private $error;

	function __construct($componentName){
		$this->component = $this->resolveComponent($componentName);
	}

	private function resolveComponent($componentName){
		define("TESTS_RUNNING", ( $_SERVER["HTTP_HTTP_TEST_DB"] == bitrix_sessid() && $GLOBALS["USER"]->getId() == 1 ));
		$componentRoot = $_SERVER["DOCUMENT_ROOT"] . "/local/components/t50/" . $componentName;
		$path = $componentRoot . "/ajax.php";
		if( !file_exists($path) ){
			$this->error = "path {$path} not exists";
			return;
		}

		require_once $path;
		$className = \T50Text::camelCase($componentName) . "Component";
		if( !class_exists($className) ){
			$this->error = "class {$className} not exists";
			return;
		}

		$component = new $className;
		return $component;
	}

	function executeAction($action, $params = []){
		if( !isset($this->component) )
			return;

		$method = "action" . \T50Text::camelCase($action);
		if( !method_exists($this->component, $method) ){
			$this->error = "method {$method} not exists";
			return;
		}

		if( TESTS_RUNNING ){
			$GLOBALS["DB"]->query("SET FOREIGN_KEY_CHECKS=0;");
			$GLOBALS["DB"]->StartTransaction();
		}

		if( method_exists($this->component, "beforeAction") )
			$this->component->beforeAction();

		$result = $this->component->$method();

		if( TESTS_RUNNING ){
			$GLOBALS["DB"]->Rollback();
			$GLOBALS["DB"]->query("SET FOREIGN_KEY_CHECKS=1;");
		}

		return $result;
	}

	function getError(){
		return $this->error;
	}
}
