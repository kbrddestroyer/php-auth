<?php
    /*
    *       DAO library
    *   Contains DAO classes for User and Account
    *   Last Modified: 24.02.2023 14:00
    */
    require_once("controllers.php");

    interface Dao 
    {
        public function selectAll();
        public function load(string $key);
        public function update();
        public function delete();
        public function save();
    }

    class UserDao implements Dao
    {
        // User Data Access Object
        // Used in data I/O
        // Single Responsibility - User CRUD operations

        private User $user;
        private JSONController $controller;

        public function __construct($user = null)
        {
            if ($user and $user instanceof User)
                $this->user = $user;
            
            $config = new ConfigController('../serverconfig.ini');
            $this->controller = new JSONController($config->getConfig("database"));
        }

        public function getUser() { return $this->user; }
        public function setUser(User $user) { $this->user = $user; }
        // <---     CRUD    ---> //

        public function selectAll()
        {
            $users = array();
            $_data = $this->controller->json_load();
            $counter = 0;
            foreach ($_data as $key => $value)
                $users[$counter++] = new User($key, $value['password']);
            return $users;
        }

        public function load(string $key)
        {
            $_data = $this->controller->json_load();
            if (!$_data or !array_key_exists($key, $_data) or !isset($_data[$key])) return false;
            $this->user = new User($key, $_data[$key]['password']);
            return $this->user;
        }

        public function update()
        {
            $_data = $this->controller->json_load();
            if (!$_data or !array_key_exists($this->user->getLogin(), $_data)) return false;
            $_data[$this->user->getLogin()]['password'] = $this->user->getPassword();

            return $this->controller->json_save($_data);
        }

        public function delete()
        {
            if (!$this->user) return false;
            $_data = $this->controller->json_load();
            if (!$_data)
                return false;
            unset($_data[$this->user->getLogin()]);
            
            return $this->controller->json_save($_data);
        }

        public function save()
        {
            return false;   // Cannot save User object
        }
    }

    class AccountDao 
    {
        // Account Data Access Object
        // Used in data I/O
        // Single Responsibility - Account CRUD operations
        
        private Account $account;
        private JSONController $controller;

        public function __construct($account = null)
        {
            if ($account and $account instanceof Account)
                $this->account = $account;
            $config = new ConfigController('../serverconfig.ini');
            $this->controller = new JSONController($config->getConfig("database"));
        }

        public function getAccount() : Account { return $this->account; }
        public function setAccount(Account $account) { $this->account = $account; }

        // <---     CRUD    ---> //

        public function selectAll()
        {
            $accounts = array();
            $_data = $this->controller->json_load();
            $counter = 0;
            foreach ($_data as $key => $value)
                $accounts[$counter++] = new Account(
                    $key, 
                    $value['password'], 
                    $value['name'], 
                    $value['email']
                );
            return $accounts;
        }

        public function load(string $key)
        {
            $_data = $this->controller->json_load();
            if (!$_data or !array_key_exists($key, $_data)) return false;
            $this->account = new Account(
                $key, 
                $_data[$key]['password'],
                $_data[$key]['name'],
                $_data[$key]['email']
            );
            return true;
        }

        public function update()
        {
            $_data = $this->controller->json_load();
            if (!$_data or !array_key_exists($this->account->getLogin(), $_data)) return false;
            $_data[$this->account->getLogin()] = 
            [
                'password' => $this->account->getPassword(),
                'name' => $this->account->getName(),
                'email' => $this->account->getEmail()
            ];

            return $this->controller->json_save($_data);
        }

        public function delete()
        {
            if (!$this->account) return false;
            $_data = $this->controller->json_load();
            if (!$_data)
                return false;
            unset($_data[$this->account->getLogin()]);
            
            return $this->controller->json_save($_data);
        }

        public function save()
        {
            if (!$this->account) return false;

            $hash = new HashController();

            $_data = $this->controller->json_load();
            $_data[$this->account->getLogin()] = 
            [
                'password' => $hash->saltPassword($this->account->getPassword()),
                'name' => $this->account->getName(),
                'email' => $this->account->getEmail()
            ];

            return $this->controller->json_save($_data);
        }
    }
?>