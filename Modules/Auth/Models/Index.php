<?php
declare(strict_types=1);

namespace Modules\Auth\Models;

use Random\RandomException;
use System\Model;

class Index extends Model{
	protected static $instance;
	protected string $table = 'sessions';
	protected string $pk = 'id_session';
    protected Users $users;

    protected function __construct(){
        parent::__construct();
        $this->users = new Users();
    }

    public function logout(): void {
        unset($_SESSION['token']);
        setcookie('token', '', time() - 1, BASE_URL);
    }

    /**
     * @throws RandomException
     */
    public function authCreateSession(int $id_user): string {
        $token = substr(bin2hex(random_bytes(128)), 0, 128);
        $this->sessionsAdd($id_user, $token);
        $_SESSION['token'] = $token;
        return $token;
    }

    // Функция проверки аутентификации пользователя
    public function authGetUser(): array{
        $user = [];

        $token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;

        if($token !== null){
            $session = $this->sessionsOne($token);

            if(!empty($session)){
                $user = $this->users->usersById($session['user']);
            }

            if(empty($user)){
                $this->logout();
            }
        }

        return $user;
    }
	
	protected function sessionsAdd(int $idUser, string $token) : bool{
		$this->add(['user' => $idUser, 'token' => $token, 'dt_add' => date('Y-m-d H:i:s')]);
		return true;
	}

	protected function sessionsOne(string $token) : array {
        $res = $this->select(['token' => $token]);
        return $res[0] ?? [];
    }
}