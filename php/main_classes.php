<?php
    require_once("database.php");
    require_once("controllers.php");

    class Main 
    {
        // Just an entry point
        // and some top-layer functions
        public static function main()
        {
            $_controller = new AjaxController();
            $data = $_controller->GetJSONRequest();
            if (!$data)
            {
                echo json_encode(['success'=>false, 'error'=>6, 'message'=>'Not a JSON request']);
                exit;
            }

            if ($data['type'] == 'login')
                Main::login($data);
            else if ($data['type'] == 'register')
                Main::registration($data);
            else if ($data['type'] == 'check_auth')
                Main::CheckAuth($data);
            else echo json_encode(['success'=>false, 'error'=>0]);
        }

        private static function login($data)
        {
            $response = new JSONRespond();
            
            $dao = new UserDao();
            $user = new User($data['login'], $data['password']);
            $hash = new HashController();

            if ($dao->load($data['login']))
            {
                if (!$hash->checkPassword($user->getPassword(), $dao->getUser()->getPassword()))
                {
                    $response->setError(4);
                    echo $response->toJSON();
                }
                else
                {
                    $response->setSuccess(true);
                    echo $response->toJSON();
                    $_SESSION['login'] = $user->getLogin();
                    exit;
                }
                exit;
            }
            else
            {
                $response->setError(3);
                echo $response->toJSON();
            }
        }

        private static function registration($data) 
        {
            $response = new JSONRespond();
            if ($data['password'] != $data['confirm_password'])
            {  
                $response->setError(1);
                echo $response->toJSON();
                exit;
            }

            $dao = new UserDao();
            if ($dao->load($data['login']))
            {
                $response->setError(2);
                echo $response->toJSON();
                return;
            }

            $dao = new AccountDao(new Account(
                $data['login'],
                $data['password'],
                $data['name'],
                $data['email']
            ));

            if ($dao->save())
            {   
                $response->setSuccess(true);
                echo $response->toJSON();
                exit;
            }
        }

        public static function CheckAuth($data)
        {
            $dao = new AccountDao();
            $response = new JSONRespond();
            if (!isset($_SESSION['login']) or !$dao->load($_SESSION['login']))
            {
                $response->setError(1);
                echo $response->toJSON();   
                return;
            }
            echo json_encode(['success' => true, 'name' => $dao->getAccount()->getName()]);
        }
    }

    class JSONRespond
    {
        private bool $success;
        private int $error;
        public function __construct(bool $success = false, int $error = 0)
        {
            $this->success = $success;
            $this->error = $error;
        }

        public function isSuccessful() { return $this->success; }
        public function getError() { return $this->error; }
        public function setSuccess(bool $success) { $this->success = $success; }
        public function setError(int $error) { if ($error != 0) $success = false; $this->error = $error; }

        public function toJSON() 
        {
            return json_encode(
                ['success' => $this->success, 'error' => $this->error]
            );
        }
    }
?>