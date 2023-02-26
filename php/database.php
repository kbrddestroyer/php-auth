<?php
    /*
    */

    require_once("dao.php");

    class User
    {
        /*
        *       User class connects web-form with database control unit and/or JSON
        */
        protected $login = "";
        protected $password = "";

        public function __construct($login, $password)
        {
            $this->login = $login;
            $this->password = $password;
        }

        public function getLogin() { return $this->login; }
        public function getPassword() { return $this->password; }
    }

    class Account extends User {
        private $name = "";
        private $email = "";

        public function __construct($login, $password, $name="", $email="")
        {
            $this->login = $login;
            $this->password = $password;
            $this->name = $name;
            $this->email = $email;
        }

        public function getName() { return $this->name; }
        public function getEmail() { return $this->email; }

        public function setName($name)
        {
            $this->name = $name;
        }
        public function setEmail($email)
        {
            $this->email = $email;
        }
    }
?>