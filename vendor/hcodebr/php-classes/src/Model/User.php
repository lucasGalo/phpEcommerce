<?php 

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class User extends Model {

	const SESSION = "User";

	protected $fields = [
		"iduser", "idperson", "deslogin", "despassword", "inadmin", "dtergister"
	];

	public static function login($login, $password):User
	{

		$db = new Sql();

		$results = $db->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if (count($results) === 0) {
			throw new \Exception("User.php => Não foi possível fazer login.");
		}

		$data = $results[0]; // primeiro registro encontrado

		if (password_verify($password, $data["despassword"])) {

			$user = new User(); // gerando uma instancia da propria classe
			$user->setData($data);			

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("User.php => Não foi possível fazer login.");

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	public static function verifyLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION]) 
			|| 
			!$_SESSION[User::SESSION] 
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0 
			||
			(bool)$_SESSION[User::SESSION]["iduser"] !== $inadmin 
		) {
			
			header("Location: /admin/login");
			exit;

		}

	}

	public static function listAll(){
		
		$sql = new Sql();
		return $sql->SELECT("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}

	public function save(){
		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
		array(
			":desperson"  => $this->getdesperson(),
			":deslogin"   => $this->getdeslogin(),
			":despassword"=> $this->getdespassword(),
			":desemail"   => $this->getdesemail(),
			":nrphone"    => $this->getnrphone(),
			":inadmin"    => $this->getinadmin()
		));

		$this->setData($results[1]);
	}

}

 ?>