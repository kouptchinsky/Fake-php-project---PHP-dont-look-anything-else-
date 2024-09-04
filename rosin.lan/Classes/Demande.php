<?php
    include 'src/_connexionDB.php';

    Class Demande{
        // déclaration des attributs ainsi que des getters / setters
        private $id_utilisateur;
        public function getId_utilisateur(){
            return $this->id_utilisateur;
        }
        public function setId_utilisateur($id_utilisateur){
            $this->id_utilisateur = $id_utilisateur;
        }
        private $fichier_contenu;
        public function getFichier_contenu(){
            return $this->fichier_contenu;
        }
        public function setFichier_contenu($fichier_contenu){
            $this->fichier_contenu = $fichier_contenu;
        }
        private $fichier_nom;
        public function getFichier_nom(){
            return $this->fichier_nom;
        }
        public function setFichier_nom($fichier_nom){
            $this->fichier_nom = $fichier_nom;
        }
        private $fichier_type;
        public function getFichier_type(){
            return $this->fichier_type;
        }
        public function setFichier_type($fichier_type){
            $this->fichier_type = $fichier_type;
        }
        private $nombre_de_page;
        public function getNombre_de_page(){
            return $this->nombre_de_page;
        }
        public function setNombre_de_page($nombre_de_page){
            $this->nombre_de_page = $nombre_de_page;
        }
        private $couleur;
        public function getCouleur(){
            return $this->couleur;
        }
        public function setCouleur($couleur){
            $this->couleur = $couleur;
        }
        private $nombre_de_copies;
        public function getNombre_de_copies(){
            return $this->nombre_de_copies;
        }
        public function setNombre_de_copies($nombre_de_copies){
            $this->nombre_de_copies = $nombre_de_copies;
        }
        private $reliure;
        public function getReliure(){
            return $this->reliure;
        }
        public function setReliure($reliure){
            $this->reliure = $reliure;
        }
        private $page_de_garde;
        public function getPage_de_garde(){
            return $this->page_de_garde;
        }
        public function setPage_de_garde($page_de_garde){
            $this->page_de_garde = $page_de_garde;
        }
        private $date;
        public function getDate(){
            return $this->date;
        }
        public function setDate($date){
            $this->date = $date;
        }
        private $statut;
        public function getStatut(){
            return $this->statut;
        }
        public function setStatut($statut){
            $this->statut = $statut;
        }

        public function __construct($id_utilisateur, $fichier_nom, $nombre_de_page, $couleur, $nombre_de_copies, $reliure, $page_de_garde, $date, $statut, $fichier_type, $fichier_contenu){
            $this->setId_utilisateur($id_utilisateur);
            $this->setFichier_nom($fichier_nom);
            $this->setNombre_de_page($nombre_de_page);
            $this->setCouleur($couleur);
            $this->setNombre_de_copies($nombre_de_copies);
            $this->setReliure($reliure);
            $this->setPage_de_garde($page_de_garde);
            $this->setDate($date);
            $this->setStatut($statut);
            $this->setFichier_type($fichier_type);
            $this->setFichier_contenu($fichier_contenu);
        }

        public function saveToDB($DB){;
            try{
                $request = $DB->prepare("INSERT INTO demandes (id_utilisateur, fichier_contenu, nombre_de_page, couleur, nombre_de_copie, reliure, page_de_garde, date, statut, fichier_nom, fichier_type) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $request->execute([$this->id_utilisateur,
                $this->fichier_contenu,
                $this->nombre_de_page,
                $this->couleur,
                $this->nombre_de_copies,
                $this->reliure,
                $this->page_de_garde,
                $this->date,
                $this->statut,
                $this->fichier_nom,
                $this->fichier_type]);
                $request->closeCursor(); 
            } catch (Exception $e){ }
        }
    }
