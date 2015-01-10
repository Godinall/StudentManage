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

	public function get_error(){
		return $this->driver->error();
	}

	public function get_admin_info($name){
		return $this->driver->get("admin", "*", ['a_name' => $name]);
	}

	public function get_admin_info_by_id($id){
		return $this->driver->get("admin", "*", ['a_id' => $id]);
	}

	public function check_permission_exists($name){
		return $this->driver->has("permission", ['p_name' => $name]);
	}

	public function check_permission_exists_by_id($id){
		return $this->driver->has("permission", ['p_id' => $id]);
	}

	public function permission_add($info){
		return $this->driver->insert("permission", $info);
	}

	public function get_permission_list(){
		return $this->driver->select("permission", "*");
	}

	public function update_permission($name, $info){
		return $this->driver->update("permission", $info, ['p_name' => $name]);
	}

	public function update_permission_by_id($id, $info){
		return $this->driver->update("permission", $info, ['p_id' => $id]);
	}

	public function permission_delete($id){
		return $this->driver->delete("permission", ['p_id' => $id]);
	}

	public function permission_get($id){
		return $this->driver->get("permission", "*", ['p_id' => $id]);
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

	public function access_add($info){
		return $this->driver->insert("access", $info);
	}

	public function access_delete($r_id, $p_id){
		return $this->driver->delete("access", ['AND' => compact('r_id', 'p_id')]);
	}

	public function update_user_info($name, $info){
		return $this->driver->update("admin", $info, ['a_name' => $name]);
	}

	public function get_role_list(){
		return $this->driver->select("role", "*");
	}

	public function get_admin_list(){
		return $this->driver->select("admin", ['[>]role' => ['r_id' => 'r_id']], [
			'admin.a_id',
			'admin.a_name',
			'admin.a_status',
			'admin.a_ip',
			'role.r_name',
			'role.r_status',
		], ['ORDER' => 'admin.a_id']);
	}

	public function admin_add($info){
		return $this->driver->insert("admin", $info);
	}

	public function admin_exists_check($name){
		return $this->driver->has("admin", ['a_name' => $name]);
	}

	public function get_role_access($role){
		return $this->driver->select("access", "*", ['r_id' => $role]);
	}

	public function get_role_info($id){
		return $this->driver->get("role", "*", ['r_id' => $id]);
	}

	public function update_admin_info($id, $info){
		return $this->driver->update("admin", $info, ['a_id' => $id]);
	}

	public function role_exists_check($name){
		return $this->driver->has("role", ['r_name' => $name]);
	}

	public function role_id_exists_check($id){
		return $this->driver->has("role", ['r_id' => $id]);
	}

	public function role_add($info){
		return $this->driver->insert("role", $info);
	}

	public function role_delete($id){
		return $this->driver->delete("role", ['r_id' => $id]);
	}

	public function admin_delete($id){
		return $this->driver->delete("admin", ['a_id' => $id]);
	}


	public function role_edit($id, $name, $status){
		return $this->driver->update("role", [
			'r_name' => $name,
			'r_status' => $status
		], ['r_id' => $id]);
	}
}