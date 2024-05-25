<?php

class Database
{
    public $con = "";
    public function __construct()
    {
        $this->con = new mysqli("localhost", "root", "", "management");
        // Check the connection
        if ($this->con->connect_error) {
            die("Database connection failed: " . $this->con->connect_error);
        } else {
            // echo "Database connected successfully!";
            // die();
        }
    }
    public function insertData($tbl, $data)
    {
        $key = array_keys($data);
        $dk = implode(",", $key);

        $val = array_values($data);
        $formattedValues = [];

        foreach ($val as $value) {
            if (is_array($value)) {
                // Handle arrays appropriately, or convert them to a string representation
                $formattedValues[] = implode(',', $value);
            } else {
                $formattedValues[] = $value;
            }
        }

        $dv = implode("','", $formattedValues);

        $sql = "INSERT INTO $tbl ($dk) VALUES ('$dv')";

        $a = $this->con->query($sql);
        $productId = $this->con->insert_id; // Obtain the last inserted ID

        // echo "Product ID after insertion: " . $productId . "<br>";

        // Return the last inserted ID
        return $productId;
    }
    public function select($columns, $table, $where = NULL) {
        $data = [];
    
        // Building the SELECT query
        $query = 'SELECT ' . $columns . ' FROM ' . $table  .  $where;
        // $query = 'SELECT ' . $columns . ' FROM ' . $table .

        // Check if there are conditions in the WHERE clause
        if (!empty($result) && is_array($result)) {
            $query .= ' WHERE';
    
            // Check if conditions are not empty before imploding
            $conditions = implode(' AND ', array_filter($where));
    
            // Concatenate the conditions with AND
            $query .= ' ' . $conditions;
        }
    
        // Print the SQL query for debugging
        // echo "Debug: SQL Query - $query<br>";
    
        // Execute the query
        $result = $this->con->query($query);
    
        if ($result) {
            // Fetching all rows into an array
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            // Handle the query error if needed
            echo "Error: " . $this->con->error;
            return false;
        }
    
        return $data;
        // print_r($data);
    }
    public function sql($columns, $table, $where) {
        // Construct the SQL query
        $query = "SELECT $columns FROM $table WHERE $where";

        // Execute the query
        $result = $this->con->query($query);

        // Check if the query was successful
        if ($result) {
            // Fetch the result as an associative array
            $data = $result->fetch_all(MYSQLI_ASSOC);

            // Free the result set
            $result->free_result();

            // Return the data
            return $data;
        } else {
            // If the query fails, return false
            return false;
        }
    }
    public function query($sql) {
        return $this->con->query($sql);
    }
    function updateData($tbl, $data, $where)
    {
        $setClause = implode(', ', array_map(function ($key) {
            return "$key = ?";
        }, array_keys($data)));
        // echo $data;

        $whereClause = implode(' AND ', array_map(function ($key) {
            return "$key = ?";
        }, array_keys($where)));
        // echo $where;

        $sql = "UPDATE $tbl SET $setClause WHERE $whereClause";
        // echo $sql;
        // exit;

        // Prepare the SQL statement
        $stmt = $this->con->prepare($sql);
        // echo $stmt;

        if ($stmt === false) {
            // Handle error (e.g., log it, display an error message)
            return false;
        }

        // Combine parameters for setClause and whereClause
        $types = '';
        $params = array();

        foreach ($data as $value) {
            if (is_array($value)) {
                $types .= 's'; // Assuming arrays should be treated as strings
                $params[] = implode(', ', $value);
            } else {
                $types .= 's'; // Adjust the type according to your data types
                $params[] = $value;
            }
        }

        foreach ($where as $value) {
            if (is_array($value)) {
                $types .= 's'; // Assuming arrays should be treated as strings
                $params[] = implode(', ', $value);
            } else {
                $types .= 's'; // Adjust the type according to your data types
                $params[] = $value;
            }
        }

        // Bind parameters
        $stmt->bind_param($types, ...$params);

        // Execute the statement
        if (!$stmt->execute()) {
            // Handle error (e.g., log it, display an error message)
            return false;
        }

        // Close the statement
        $stmt->close();

        // Return success indicator
        return true;
    }
    public function deleteData($tbl, $condition)
    {
        $sql = "DELETE FROM $tbl WHERE $condition";
        $result = $this->con->query($sql);

        // Check for SQL errors
        if (!$result) {
            echo "Error: " . $this->con->error;
        }

        return $result;
    }
    public function __destruct()
    {
        // Close the database connection
        $this->con->close();
    }
}
