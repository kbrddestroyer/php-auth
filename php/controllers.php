<?php
    class ConfigController
    {
        private $data;
        public function __construct(string $filename)
        {
            $this->data = parse_ini_file($filename);
        }

        public function getConfig(string $key) 
        {
            if (!array_key_exists($key, $this->data)) 
                throw new Exception("No such key!");
            return $this->data[$key];    
        }
    }

    class HashController
    {
        public string $salt;

        public function __construct()
        {
            $config = new ConfigController("serverconfig.ini");
            $this->salt = $config->getConfig("static_salt");
        }

        public function saltPassword(string $password) { return sha1($this->salt.$password); }
        public function checkPassword(string $password, string $hash) : bool {
            return $this->saltPassword($password) == $hash;
        }
    }
    class AjaxController
    {
        public function __construct()
        {
            session_start([
                'use_only_cookies' => 1,
                'cookie_lifetime' => 0,
                'cookie_secure' => 1,
                'cookie_httponly' => 1
              ]);
            header('Content-type: text/html; charset=utf-8');
        }

        public function GetJSONRequest()
        {
            $json = trim(file_get_contents("php://input"));         // JSON input string
            return json_decode($json, true);        
        }

        public function GetSessionVariable(string $_key) {
            if (!array_key_exists($_key, $_SESSION) or !isset($_SESSION[$_key])) return false;
            return $_SESSION[$_key];
        } 
    }

    class JSONController
    {
        // Wrapper for JSON files
        // I/O controller
        // Single-Responsibility - Simple I/O JSON Interface

        private string $fname;

        public function __construct(string $fname)
        {
            $this->fname = $fname;
        }

        public function getFilename() { return $this->fname; }
        public function setFilename(string $fname) { $this->fname = $fname; }

        public function json_save($data)
        {
            return file_put_contents($this->fname, json_encode($data));
        }

        public function json_load()
        {
            if (!($json = file_get_contents($this->fname)))
                return false;

            return json_decode($json, true);
        }
    }
?>