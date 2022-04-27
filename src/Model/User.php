<?php

namespace App\Model;

class User
{
    protected int $id_user;
    protected string $pseudo;
    protected ?string $pwd;
    protected string $email;
    protected string $created_at;


    /**
     * Efface le mot de passe pour des raisons de sécurité.
     *
     * @return void
     */
    public function deletePassword(): void
    {
        $this->pwd = null;
    }

    /**
     * Set les propriétés à partir d'un tableau.
     *
     * @param array $data le tableau de données qui va permettre de remplir les propriétés.
     * @return void
     */
    public function arrayToUser(array $data): void
    {
        if (isset($data["id_user"])) {
            $this->id_user = $data["id_user"];
        }
        if (isset($data["pseudo"])) {
            $this->pseudo = $data["pseudo"];
        }
        $this->email = $data["email"];
        $this->pwd = $data["pwd"];
        if (isset($data["created_at"])) {
            $this->created_at = $data["created_at"];
        }
    }

    /**
     * Permet de créer un tableau avec les données user.
     *
     * @return array tableau retourné avec les données user.
     */
    public function userToArray(): array
    {
        return [
            "id_user" => $this->id_user,
            "pseudo" => $this->pseudo,
            "email" => $this->email,
            "pwd" => $this->pwd,
            "created_at" => $this->created_at
        ];
    }

    /**
     * Hash un mot de passe, passé en paramètre, avant de l'assigner à la propriété pwd.
     *
     * @param string $pwd Mot de passe à hacher
     */
    public function setHashPwd(string $pwd): self
    {
        $this->pwd = password_hash($pwd, PASSWORD_ARGON2I);

        return $this;
    }

    /**
     * Vérifie la correspondance de la propriété pwd avec une chaîne de caractère fourni
     *
     * @param string $pwd Chaîne de caractère à vérifier
     * @return bool Renvoi true si la correspondance est vrai, sinon false
     */
    public function verifyPwd(string $pwd): bool
    {
        return password_verify($pwd, $this->pwd);  
    }

    /**
     * Suppression du mot de passe de la propriété de l'objet
     */
    public function erasePwd()
    {
        $this->pwd = null;
    }

    /**
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->id_user;
    }

    /**
     * @param int $id_user
     */
    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    /**
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return string
     */
    public function getPwd(): string
    {
        return $this->pwd;
    }

    /**
     * @param string $pwd
     */
    public function setPwd(string $pwd): self
    {
        $this->pwd = $pwd;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
