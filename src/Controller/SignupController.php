<?php

namespace App\Controller;

use PDOException;
use App\Dao\UserDao;
use App\Model\User;

class SignupController
{
    /**
     * On récupère les données POST de l'utilisateur
     * on effectue une vérification des données
     * puis on envoi les données au DAO pour l'insertion dans la BDD.
     * 
     * @return void
     */
    public function index()
    {
         $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            http_response_code(403);
            die;
        }

        /* Vérification des données utilisateur, dans un premier temps si le champ existe et par la suite
        /* si le champ n'est pas vide.*/
        if (!isset($data["pseudo"])) {
            $errors_messages[] = "Pseudo requis.";
        } else if (empty(trim($data["pseudo"]))) {
            $errors_messages[] = "Pseudo requis.";
        }

        if (!isset($data["email"])) {
            $errors_messages[] = "Email requis.";
        } else if (empty(trim($data["email"]))) {
            $errors_messages[] = "Email requis.";
        }

        if (!isset($data["pwd"])) {
        $errors_messages[] = "Mot de passe requis.";
        } else if (empty(trim($data["pwd"]))) {
            $errors_messages[] = "Mot de passe requis.";
        }

        if (!isset($data["conf_pwd"])) {
            $errors_messages[] = "Confirmation du mot de passe requis.";
        } else if (empty(trim($data["conf_pwd"]))) {
            $errors_messages[] = "Confirmation du mot de passe requis.";
        }

        // Vérification de la concordance des mots de passe.
        if (isset($data["conf_pwd"]) && isset($data["pwd"])) {
            if ($data["conf_pwd"] !== $data["pwd"]) {
                $errors_messages[] = "Les deux mots de passe doivent concorder.";
            }
        }
        
        if (isset($errors_messages)) {
            //On retourne au front les erreurs en format json et on arrête le script.
            header("Type-Content: application/json");
            echo json_encode([
                'error_messages'=>
                ['danger'=>$errors_messages]
            ]);
            die;        
        }

        $userDao = new UserDao();
        $user = new User();
        $user->setPseudo($data["pseudo"])
            ->setEmail($data["email"])
            ->setPwd($data["pwd"]);

        try {
            $userDao->newUser($user);
        } catch (PDOException $err) {
            //On retourne une erreur serveur si une exception PDO est levée.
            http_response_code(500);
            die;
        }
    }
}
