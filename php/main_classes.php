<?php
    require_once("database.php");
    require_once("controllers.php");

    class Main 
    {
        // Just an entry point
        // and some top-layer functions
        public static function main__()
        {
            $_controller = new AjaxController();
            $data = $_controller->GetJSONRequest();
            if (!$data)
            {
                echo json_encode(['success'=>false, 'error'=>-1]);
                exit;
            }

            $validation = Main::validate($data);
            if ($validation != 0)
            {
                echo json_encode(['success'=>false, 'error'=>$validation]);
                exit;
            }
            if ($data['type'] == 'login')
                Main::login($data);
            else if ($data['type'] == 'register')
                Main::registration($data);
            else if ($data['type'] == 'check_auth')
                Main::CheckAuth($data);
            else if ($data['type'] == 'logout')
                Main::logout();
            else echo json_encode(['success'=>false, 'error'=>1]);
        }

        private static function validate($data) : int 
        {
            // Server-side validation.
            // Client-side has it's own validation mechanism based on both JS and HTML5

            // ERROR CODES:
            // GENERAL:
            // -1 - Bad request (not an AJAX request)
            // 0 - OK
            // 1 - Bad request (no or corrupted request type)
            // 2 - Corrupted login or register request (no required data passed)
            // REGISTRATION:
            // 3 - login and/or password length validation failed
            // 4 - password symbol validation failed
            // 5 - spaces in login found
            // 6 - spaces in password found
            // 7 - password confirmation failed
            // 8 - email validation failed
            // 9 - name validation failed
            // 10 - password confirmation failed
            // 11 - account aleready exists
            // 12 - email already registered
            // LOGIN:
            // 13 - Wrong password
            // 14 - Account not found
            

            if (!isset($data['type']))
                return 1;                                                       // Corrupted request
            if ($data['type'] == 'check_auth' or $data['type'] == 'logout')     // No need to check anything else
                return 0;

            if (!isset($data['login']) or !isset($data['password']))
            return 2;                                                           // Corrupted form
            if ($data['type'] == 'register')
            {
                if (
                    strlen($data['login']) < 6 
                    or
                    strlen($data['password'])  < 6
                    )
                    return 3;
                if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $data['password']) or preg_match('/[^a-zAA-Z\d]/', $data['password']))
                    return 4;
                if (strpos($data['login'], ' ') !== false)
                    return 5;
                if (strpos($data['password'], ' ') !== false)
                    return 6;
                if (!isset($data['email']) or !isset($data['name']))
                    return 2;
                if ($data['password'] != $data['confirm_password'])
                    return 7;
                if (
                    strlen($data['email']) == 0 or
                    substr_count($data['email'], '@') != 1 or 
                    substr_count($data['email'], '.', strpos($data['email'], '@')) != 1 or 
                    strpos($data['email'], '.') === strlen($data['email']) - 1 or 
                    strpos($data['email'], ' ')
                )
                    return 8;
                if (
                    strlen($data['name']) < 0 or
                    strpos($data['name'], ' ') !== false
                )
                    return 9;
            }
            return 0;
        }

        private static function login($data)
        {
            $response = new JSONRespond();

            $dao = new UserDao();
            $user = new User($data['login'], $data['password']);
            $hash = new HashController();

            if ($dao->load($data['login']))
            {
                if (
                    $hash->checkPassword($user->getPassword(), $dao->getUser()->getPassword()) or
                    (isset($_COOKIE['login']) and $_COOKIE['login'] == $user->getLogin() and 
                    $_COOKIE['key'] == $dao->getUser()->getPassword())
                )
                {
                    $response->setSuccess(true);
                    $_SESSION['login'] = $user->getLogin();
                    setcookie('login', $user->getLogin(), time() + 60 * 60 * 24);
                    setcookie('key', $dao->getUser()->getPassword(), time() + 60 * 60 * 24);
                    echo $response->toJSON();
                }
                else
                {
                    $response->setError(13);
                    echo $response->toJSON();
                
                    exit;
                }
                exit;
            }
            else
            {
                $response->setError(14);
                echo $response->toJSON();
            }
        }

        private static function registration($data) 
        {
            $response = new JSONRespond();
            if ($data['password'] != $data['confirm_password'])
            {  
                $response->setError(10);
                echo $response->toJSON();
                exit;
            }

            $dao = new AccountDao();
            $account = new Account(
                $data['login'],
                $data['password'],
                $data['name'],
                $data['email']
            );
            if ($dao->load($account->getLogin()))
            {
                $response->setError(11);
                echo $response->toJSON();
                return;
            }
            
            $accounts = $dao->selectAll();
            foreach ($accounts as $acc)
                if ($account->getEmail() == $acc->getEmail())
                {
                    $response->setError(12);
                    echo $response->toJSON();
                    return;
                }
            $dao->setAccount($account);

            if ($dao->save())
            {   
                $response->setSuccess(true);
                $_SESSION['login'] = $dao->getAccount()->getLogin();
                setcookie('login', $dao->getAccount()->getLogin(), time() + 60 * 60 * 24);
                setcookie('key', $dao->getAccount()->getPassword(), time() + 60 * 60 * 24);
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
                if (
                    !(isset($_COOKIE['login']) and $_COOKIE['login'] == $dao->getAccount()->getLogin() and 
                    $_COOKIE['key'] == $dao->getAccount()->getPassword())
                )
                    $response->setError(14);
                    echo $response->toJSON();   
                    return;
            }
            echo json_encode(['success' => true, 'name' => $dao->getAccount()->getName()]);
        }

        public static function logout()
        {
            unset($_SESSION['login']);
            session_destroy();
            setcookie('login', "", time());
            setcookie('key', "", time());
            echo json_encode(['success' => true]);
            exit;            
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