
<?php

require_once "/Applications/XAMPP/xamppfiles/htdocs/inc/bootstrap.php";
require_once "/Applications/XAMPP/xamppfiles/htdocs/inc/bootstrap.php";

class UserController extends BaseController
{
    /**
     * Handles user login and authentication.
     */
    public function loginAction() {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);

        // Check if it's a POST request and required data is present
        if ($requestMethod === 'POST' && isset($data['username']) && isset($data['password'])) {
            try {
                $userModel = new UserModel();
                $arrUser = $userModel->getUserByUsername($data['username']);

                // Verify password and set session if authentication is successful
                if ($arrUser && password_verify($data['password'], $arrUser[0]['password'])) {
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["username"] = $data['username'];

                    $responseData = [
                        "success" => true,
                        "message" => "Authentication successful",
                        "username" => $data['username'],
                    ];
                } else {
                    // Authentication failed
                    $responseData = [
                        "success" => false,
                        "message" => "Authentication failed",
                    ];
                }
            } catch (Error $e) {
                // Handle unexpected errors
                $strErrorDesc = $e->getMessage() . ' Something went wrong! Please contact support.';
            }
        } else {
            // Invalid request method or missing parameters
            $strErrorDesc = 'Method not supported/Wrong Params';
        }

        // Send the response
        $this->sendResponse($responseData, $strErrorDesc);
    }

    /**
     * Logs out the user by destroying the session.
     */
    public function logoutAction() {
        session_start();
        session_destroy();

        $responseData = [
            "success" => true,
        ];

        // Send the response
        $this->sendResponse($responseData);
    }

    /**
     * Handles user registration.
     */
    public function registerAction() {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);

        // Check if it's a POST request and required data is present
        if ($requestMethod === 'POST' && isset($data['username']) && isset($data['password']) && isset($data['confirm_password'])) {
            try {
                // Access the values
                $username = $data["username"];
                $password = $data["password"];
                $confirmPassword = $data["confirm_password"];

                // Check if passwords match
                if ($password !== $confirmPassword) {
                    $responseData = [
                        "success" => false,
                        "message" => "Passwords do not match",
                    ];
                } else {
                    $userModel = new UserModel();
                    $usernameCheck = $userModel->getUserByUsername($username);

                    // Check if username is already taken
                    if (!empty($usernameCheck)) {
                        $responseData = [
                            "success" => false,
                            "message" => "Username already taken",
                        ];
                    } else {
                        // Register the user and set session
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $arrUser = $userModel->registerAction($username, $hashedPassword);

                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["username"] = $username;

                        $responseData = [
                            "success" => true,
                            "message" => "Registration successful",
                            "username" => $_SESSION["username"],
                        ];
                    }
                }
            } catch (Error $e) {
                // Handle unexpected errors
                $strErrorDesc = $e->getMessage() . ' Something went wrong! Please contact support.';
            }
        } else {
            // Invalid request method or missing parameters
            $strErrorDesc = 'Method not supported/Wrong Params';
        }

        // Send the response
        $this->sendResponse($responseData, $strErrorDesc);
    }

    /**
     * Sends the JSON response with appropriate headers.
     *
     * @param array $responseData The data to be sent in the response.
     * @param string $strErrorDesc The error description, if any.
     */
    private function sendResponse($responseData, $strErrorDesc = '') {
        if ($strErrorDesc === '') {
            // Send success response
            $this->sendOutput(json_encode($responseData), ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
        } else {
            // Send error response
            $this->sendOutput(json_encode(['error' => $strErrorDesc]), ['Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error']);
        }
    }
}
