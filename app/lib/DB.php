<?php
/**
 * User: loveyu
 * Date: 2015/1/8
 * Time: 23:23
 */

namespace ULib;


use CLib\Sql;

class DB{

	/**
	 * @var Sql
	 */
	private $driver;

	function __construct(){
		c_lib()->load('sql');
		$this->driver = new Sql(cfg()->get('database'));
		if(!$this->driver->status()){
			define('HAS_RUN_ERROR', true);
			cfg()->set('HAS_RUN_ERROR', $this->driver->ex_message());
		}
	}

	public function get_admin_info($name){
		return $this->driver->get("admin", "*", ['a_name' => $name]);
	}

	public function get_access($name){
		$role = NULL;
		$read = $this->driver->getReader();
		$p = $read->prepare("select role.r_id as id,role.r_name as name,role.r_status as status from role INNER JOIN admin on admin.r_id = role.r_id WHERE admin.a_name=:uname");
		if($p->execute([':uname' => $name])){
			$role = $p->fetchAll(\PDO::FETCH_ASSOC)[0];
		}
		return compact('role');
	}

	public function update_user_info($name, $info){
		return $this->driver->update("admin", $info, ['a_name' => $name]);
	}

	public function get_role_list(){
		return $this->driver->select("role", "*");
	}

	public function role_exists_check($name){
		return $this->driver->has("role", ['r_name' => $name]);
	}

	public function role_add($info){
		return $this->driver->insert("role", $info);
	}

	public function role_delete($id){
		return $this->driver->delete("role", ['r_id' => $id]);
	}

	public function role_edit($id, $name, $status){
		return $this->driver->update("role", [
			'r_name' => $name,
			'r_status' => $status
		], ['r_id' => $id]);
	}
}