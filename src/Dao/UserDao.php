<?php

namespace App\Dao;

use PDO;
use Core\AbstractDao;
use App\Model\User;

class UserDao extends AbstractDao
{
    /**
     * Récupère un utilisateur par son email si l'email existe dans la base de données,
     * sinon on récupèrera NULL
     *
     * @param string $email L'email de l'utilisateur
     * @return User|null Renvoi un utilisateur ou null
     */
    public function getByEmail(string $email): ?User
    {
        $sth = $this->dbh->prepare('SELECT * FROM user WHERE email = :email');
        $sth->execute([':email' => $email]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return null;
        }

        $u = new User();
        return $u->setIdUser($result['id_user'])
            ->setPseudo($result['pseudo'])
            ->setPwd($result['pwd'])
            ->setEmail($result['email'])
            ->setCreatedAt($result['created_at']);
    }

    /**
     * Récupère la liste de tous les utilisateur dans la base de donnée
     *
     * @return array|null Renvoi un tableau d'utilisateur ou null
     */
    public function getAllUsers(): ?array
    {           
        $query = $this->dbh->prepare("SELECT * FROM user");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($data))
        {
            return null;
        }

        for($i=0;$i<count($data);$i++)
        {
            $users[$i]=new User();
            $users[$i]->arrayToUser($data[$i]);
        }
        
        return $users;
    }

    /**
     * Récupère un utilisateur dans la base de donnée si l'id de l'utilisateur existe.
     *
     * @param [type] $idUser l'id de l'utilisateur recherché.
     * @return User|null renvoi un utilisateur ou null.
     */
    public function getUser($idUser) : ?User
    {
        $query = $this->dbh->prepare("SELECT * from user where id_user=:id");
        $query->execute([
            ":id"=>$idUser
        ]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if(empty($data)){
            return null;
        }
        $user = new User();
        $user->arrayToUser($data);  
        return $user;
    }

    /**
     * Insert un nouvel utilisateur dans la base de donnée.
     *
     * @param user $user l'utilisateur à insérer dans la base de donnée.
     * @return void
     */
    public function newUser(user $user): void
    {      
        $query = $this->dbh->prepare("INSERT into user (pseudo,email,pwd) values(:username,:email,:password)");
        $query->execute([
        ":username"=>$user->getPseudo(),
        ":email"=>$user->getEmail(),
        ":password"=>password_hash($user->getPwd(),PASSWORD_ARGON2I)
    ]);

    }
  
    /**
     * Effacer un utilisateur dans la base de donnée grâce à son id.
     *
     * @param [type] $iduser de l'utilisateur à effacer.
     * @return void
     */
    public function deleteUser($iduser) :void
    {    
        $query = $this->dbh->prepare("delete from user where id_user=:id");
        $query->execute([
            ":id" => $iduser
        ]);
    }

    /**
     * Modifier un utilisateur dans la base de donnée à l'aide de son ID.
     *
     * @param user $user l'utilisateur à modifier.
     * @return void
     */
    public function editUser(user $user):void
    {       
        $query =$this->dbh->prepare("UPDATE user set pseudo=:pseudo, email=:email, pwd=:pwd where id_user=:id_user");
        $query->execute([
        ":pseudo"=>$user->getPseudo(),
        ":email"=>$user->getEmail(),
        ":pwd"=>password_hash($user->getPwd(),PASSWORD_ARGON2I),
        ":id_user"=>$user->getIdUser()
        ]);
    }
}
