<?php

//数据工厂类

final class Db_factory {
	
	
	/**
	 * 当前数据库工厂类静态实例
	 */
	private static $db_factory;
	
	/**
	 * 数据库配置列表
	 */
	protected $db_config = array();
	
	/**
	 * 数据库操作实例化列表
	 */
	protected $db_list = array();
	
    
	public function __construct(){
		
		
	}
	
	/**
	 * 返回当前终级类对象的实例
	 * @param $db_config 数据库配置
	 * @return object
	 */
	public static function get_instance($db_config = '') {
	 
		if($db_config == '') {
			$db_config = Yaf_Application::app()->getConfig()->db->toArray();
		}
		
		//单例
		if(db_factory::$db_factory == '') {
			Db_factory::$db_factory = new Db_factory();
		}
		
		if($db_config != '' && $db_config != db_factory::$db_factory->db_config){
			Db_factory::$db_factory->db_config = array_merge($db_config, Db_factory::$db_factory->db_config);
		} 
		
		
		return Db_factory::$db_factory;
		
	}
	
	/**
	 * 获取数据库操作实例
	 * @param $db_name 数据库配置名称
	 */
	public function get_database($db_name) {
		
		if(!isset($this->db_list[$db_name]) || !is_object($this->db_list[$db_name])) {
			
			$this->db_list[$db_name] = $this->connect($db_name);
			
		}
		
		return $this->db_list[$db_name];
	}
	
	
	/**
	 *  加载数据库驱动
	 * @param $db_name 	数据库配置名称
	 * @return object
	 */
	public function connect($db_name) {
		
		$object = null;		
		
		//$object =new Db_mysql();
		$class_name = 'Db_'.$this->db_config[$db_name]['type'];
		
		if(class_exists($class_name)){

			$object  = new $class_name();
		    $object->open($this->db_config[$db_name]);
		
		}else{
			exit($this->db_config[$db_name]['type'].' data driven does not exist.');
		}
		
		return $object;
	}
	
	
	/**
	 * 关闭数据库连接
	 * @return void
	 */
	protected function close() {
		foreach($this->db_list as $db) {
			$db->close();
		}
	}
	
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		$this->close();
	}
	
	
}





?>