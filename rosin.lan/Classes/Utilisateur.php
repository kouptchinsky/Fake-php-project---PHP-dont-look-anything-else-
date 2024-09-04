<?php
    include 'src/_connexionDB.php';

    Class Utilisateur{
        // déclaration des attributs ainsi que des getters / setters
        private string $role;
        public function getRole(){
            return $this->role;
        }
        public function setRole($role){
            $this->role = $role;
        }
        private string $email;
        public function getEmail(){
            return $this->email;
        }
        public function setEmail($email){
            $this->email = $email;
        }
        private int $nombre_impression;
        public function getNombre_impression(){
            return $this->nombre_impression;
        }
        public function setNombre_impression($nombre_impression){
            $this->nombre_impression = $nombre_impression;
        }

        function __construct(string $role, string $email, int $nombre_impression = 0){
            $this->setRole($role);
            $this->setEmail($email);
            $this->setNombre_impression($nombre_impression);
        }

        public function getUserId($DB){
            $result = $DB->prepare('SELECT id_utilisateur FROM utilisateurs WHERE email = :email');
            $result->bindParam(':email', $this->email);
            $result->execute();
            $row = $result->fetch();
            return $row['id_utilisateur'];
        }
        
        // rajoute un nouvel utilisateur dans la table utilisateurs
        public function addNewUser($DB){
            $query = "INSERT INTO utilisateurs (role, email, nombre_impression) VALUES (:role, :email, :nombre_impression)";
            $req = $DB->prepare($query);
            $req->execute(array(
                'role' => $this->role,
                'email' => $this->email,
                'nombre_impression' => $this->nombre_impression
            ));
        }
    }
