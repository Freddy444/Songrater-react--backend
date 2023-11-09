<?php

class SongController extends BaseController
{

    public function listAction()
    {
        session_start();

        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $musicModel = new SongModel();
                $intLimit = 1000;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $arrUsers = $musicModel->getMusic($intLimit);
                $responseData = json_encode($arrUsers);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /**
     * Delete a music item based on the provided ID.
     */
    public function deleteAction()
    {
        session_start();

        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $jsonData = file_get_contents("php://input");

        // Decode the JSON data into a PHP array
        $data = json_decode($jsonData, true); // Set the second argument to true for an associative array

        if (strtoupper($requestMethod) == 'POST' && isset($data['id'])) {

            // Access the values
            $id = $data["id"];

            try {
                $musicModel = new SongModel();

                $username = $_SESSION["username"];
                $song_username = $musicModel->getMusicbyId($id);
                if ($song_username[0]["username"] === $username) {

                    $boolDelete = $musicModel->deleteMusic($id);
                    if ($boolDelete) {
                        $responseData = array("success" => true);
                    } else {
                        $responseData = array("success" => false);
                    }

                    $responseData = json_encode($responseData);
                } else {
                    $strErrorDesc = 'Something went wrong! Please contact support. Username doesn\'t match';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }

            } catch (Error $e) {
                $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported/Wrong Params';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /**
     * Update a music item based on the provided data.
     */
    public function updateAction()
    {
        session_start();
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $jsonData = file_get_contents("php://input");

        // Decode the JSON data into a PHP array
        $data = json_decode($jsonData, true); // Set the second argument to true for an associative array

        if (strtoupper($requestMethod) == 'POST' && isset($data['id']) && isset($data['artist'])
            && isset($data['song']) && isset($data['rating'])) {

            // Access the values
            $id = $data["id"];
            $artist = $data["artist"];
            $song = $data["song"];
            $rating = $data["rating"];

            try {
                $musicModel = new SongModel();

                $username = $_SESSION["username"];
                $song_username = $musicModel->getMusicbyId($id);
                if ($song_username[0]["username"] === $username) {

                    $boolUpdate = $musicModel->updateMusic($id, $artist, $song, $rating);
                    if ($boolUpdate) {
                        $responseData = array("success" => true);
                    } else {
                        $responseData = array("success" => false);
                    }

                    $responseData = json_encode($responseData);
                } else {
                    $strErrorDesc = 'Something went wrong! Please contact support. Username doesn\'t match';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }

            } catch (Error $e) {
                $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported/Wrong Params';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /**
     * Retrieve details of a specific music item.
     */
    public function readAction()
    {
        session_start();

        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $musicModel = new SongModel();
                $music_item = $musicModel->getMusicbyId($arrQueryStringParams['id']);
                $responseData = json_encode($music_item);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /**
     * Create a new music item.
     */
    public function createAction()
    {
        session_start();

        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $jsonData = file_get_contents("php://input");

        // Decode the JSON data into a PHP array
        $data = json_decode($jsonData, true); // Set the second argument to true for an associative array

        if (strtoupper($requestMethod) == 'POST' && isset($data['artist'])
            && isset($data['song']) && isset($data['rating'])) {

            // Access the values
            $artist = $data["artist"];
            $song = $data["song"];
            $rating = $data["rating"];

            try {
                $musicModel = new SongModel();

                $username = $_SESSION["username"];
                $check_song = $musicModel->getMusicId($username, $artist, $song);

                if ($check_song === FALSE) {
                    $strErrorDesc = 'Something went wrong (when inputting song into the database)! Please contact support';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                } else if ($check_song->num_rows > 0) {
                    $responseData = array("success" => false, "error" => "Song already exists for the user");
                } else {
                    $boolCreate = $musicModel->createMusic($username, $artist, $song, $rating);

                    if ($boolCreate) {
                        $responseData = array("success" => true, "message" => "entry created successfully");
                    } else {
                        $responseData = array("success" => false, "message: unknown error. Please contact support");
                    }

                }

                $responseData = json_encode($responseData);

            } catch (Error $e) {
                $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported/Wrong Params';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}
