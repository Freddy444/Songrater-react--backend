<?php

require "/Applications/XAMPP/xamppfiles/htdocs/inc/bootstrap.php";

class Database
{
    protected $connection = null;

    /**
     * Establishes a connection to a MySQL database.
     */
    public function __construct()
    {
        require "/Applications/XAMPP/xamppfiles/htdocs/inc/config.php";

        try {
            $this->connection = new mysqli($servername, $username, $password, $dbname);

            if ($this->connection->connect_errno) {
                throw new Exception("Could not connect to the database: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Executes a SELECT query and returns the result.
     *
     * @param string $query The SELECT query.
     * @param array $params The parameters for the prepared statement.
     * @return array The result of the SELECT query.
     * @throws Exception If an error occurs.
     */
    public function select($query = "", $params = [])
    {
        try {
            $stmt = $this->executeStatement($query, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Executes a prepared statement.
     *
     * @param string $query The query to prepare.
     * @param array $params The parameters for the prepared statement.
     * @return mysqli_stmt The prepared statement.
     * @throws Exception If an error occurs.
     */
    private function executeStatement($query = "", $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                throw new Exception("Unable to prepare statement: " . $this->connection->error);
            }

            if ($params) {
                $stmt->bind_param(...$params);
            }

            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
