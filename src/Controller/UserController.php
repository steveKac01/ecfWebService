<?php

namespace App\Controller;

use PDOException;
use App\Dao\UserDao;
use App\Model\User;

class UserController
{
    /**
     * Affichage de tous les utilisateurs.
     *
     * @return void
     */
    public function index()
    {
        $userDao = new UserDao();

        try {
            $data = $userDao->getAllUsers();

            if (empty($data)) {
                http_response_code(403);
                die;
            }

            for ($i = 0; $i < count($data); $i++) {
                $users[$i] = $data[$i]->userToArray();
            }

            //Envoi des données au format json au front.
            header("Content-Type: Application/json");
            echo json_encode($users, true);
        } catch (PDOException $err) {
            //On retourne une erreur serveur si une exception PDO est levée.
            http_response_code(500);
            die;
        }
    }

    /**
     * Affiche d'un utilisateur à l'aide de l'id récupéré.
     *
     * @return void
     */
    public function userShow()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            http_response_code(401);
            die;
        }

        $daoUser = new UserDao();
        try {
            $user = $daoUser->getUser($data['id_user']);

            if ($user === null) {
                http_response_code(500);
                die;
            }

            $user = $user->userToArray();

            header("Content-Type: application/json");
            echo json_encode($user);
        } catch (PDOException $err) {
            //On retourne une erreur serveur si une exception PDO est levée.
            http_response_code(500);
            die;
        }
    }

    /**
     * Récupère l'id de l'utilisateur envoyé en méthode DELETE
     * et le renseigne au DAO pour éffacer l'utilisateur.
     *
     * @return void
     */
    public function delete()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            http_response_code(401);
            die;
        }

        $userDao = new UserDao();
        $userDao->deleteUser($data["id_user"]);
    }

    /**
     * Edition d'un utilisateur,vérification de la méthode http utilisée
     * pour nue methode "GET" 
     * 
     * @return void
     */
    public function edit()
    {
        //Vérification de la méthode http utilisée.
        $requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD");
        $daoUser = new UserDao();

        if ($requestMethod === "GET") {
            //On affiche l'utilisateur
            $this->userShow();
        }

        if ($requestMethod === "PUT") {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data)) {
                http_response_code(403);
                die;
            }
            //Vérification des données utilisateur.
            if (
                isset($data['id_user']) && isset($data['pseudo'])
                && isset($data['email']) && isset($data['pwd'])
                && isset($data['new_pwd']) && isset($data['conf_new_pwd'])
            ) {
                if (empty(trim($data["id_user"]))) {
                    $errors_messages[] = "Le champ id_user est requis.";
                }
                if (empty(trim($data["pseudo"]))) {
                    $errors_messages[] = "Le champ pseudo est requis.";
                }
                if (empty(trim($data["email"]))) {
                    $errors_messages[] = "Le champ email est requis.";
                }
                if (empty(trim($data["pwd"]))) {
                    $errors_messages[] = "Le champ pwd est requis.";
                }
                if (empty(trim($data["new_pwd"]))) {
                    $errors_messages[] = "Le champ new_pwd est requis.";
                }
                if (empty(trim($data["conf_new_pwd"]))) {
                    $errors_messages[] = "Le champ conf_new_pwd est requis.";
                }

                // Vérification de la concordance des deux nouveaux mots de passe.          
                if ($data["conf_new_pwd"] !== $data["new_pwd"]) {
                    $errors_messages[] = "Les deux mots de passe doivent concorder.";
                }
            } else {
                $errors_messages[] = "Tous les champs ne sont pas remplis.";
            }

            //Note pour thibaut: j'ai une erreur de front alors j'ai continué avec postman pour pas rester bloquer.
            if (isset($errors_messages)) {
                //On retourne au front les erreurs en format json et on arrête le script.
                header("Type-Content: application/json");
                echo json_encode([
                    'error_messages' =>
                    ['danger' => $errors_messages]
                ]);
                die;
            }

            //Récupération de utilisateur.
            $user = $daoUser->getByEmail($data["email"]);

            //Vérification du mot de passe.
            if ($user->verifyPwd($data["pwd"])) {
                $user->deletePassword();
                //Edition de l'utilisateur pour l'update            
                $user->setIdUser($data["id_user"])
                    ->setPseudo($data["pseudo"])
                    ->setEmail($data["email"])
                    ->setPwd($data["new_pwd"]);

                try {
                    $daoUser->editUser($user);
                } catch (PDOException $err) {
                    //On retourne une erreur serveur si une exception PDO est levée.
                    http_response_code(500);
                    die;
                }

            } else {
                //Le mot de passe n'est pas valide on retourne une erreur.
                http_response_code(403);
                die;
            }
        }
    }
}
