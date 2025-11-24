<?php
/**
 * Database Connection Class
 * Handles all database operations
 */

if (!class_exists('db_connection')) {
    class db_connection
    {
        public $db = null;
        public $results = null;

        /**
         * Establish a database connection
         */
        public function db_connect()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

            if (mysqli_connect_errno()) {
                return false;
            }

            // Set character encoding to UTF-8
            mysqli_set_charset($this->db, "utf8mb4");

            return true;
        }

        /**
         * Get active DB connection (or false on failure)
         */
        public function db_conn()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

            if (mysqli_connect_errno()) {
                return false;
            }

            // Set character encoding to UTF-8
            mysqli_set_charset($this->db, "utf8mb4");

            return $this->db;
        }

        /**
         * Run a SELECT query
         */
        public function db_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $this->results = mysqli_query($this->db, $sqlQuery);
            return $this->results !== false;
        }

        /**
         * Run an INSERT, UPDATE, DELETE query
         */
        public function db_write_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $result = mysqli_query($this->db, $sqlQuery);
            return $result !== false;
        }

        /**
         * Fetch a single record
         */
        public function db_fetch_one($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_assoc($this->results);
        }

        /**
         * Fetch all records
         */
        public function db_fetch_all($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_all($this->results, MYSQLI_ASSOC);
        }

        /**
         * Get count of rows in last result
         */
        public function db_count()
        {
            if ($this->results == null || $this->results == false) {
                return false;
            }
            return mysqli_num_rows($this->results);
        }

        /**
         * Get last inserted ID
         */
        public function last_insert_id()
        {
            return mysqli_insert_id($this->db);
        }

        /**
         * Close database connection
         */
        public function db_close()
        {
            if ($this->db) {
                mysqli_close($this->db);
            }
        }
    }
}
?>
