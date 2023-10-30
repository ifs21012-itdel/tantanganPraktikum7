<?php
require_once(__DIR__ . '/../config.php');

class Todo
{
  private $conn;

  public function __construct()
  {
    // Menginisialisasi koneksi database
    $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($this->conn->connect_error) {
      die("Koneksi database gagal: " . $this->conn->connect_error);
    }
  }

  public function getAllTodos()
  {
    $query = "SELECT * FROM todos";
    $result = $this->conn->query($query);
    $todos = [];

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $todos[] = $row;
      }
    }

    return $todos;
  }

  public function runQuery($query) {
    return $this->conn->query($query);
  }
  


  public function createTodo($activity)
  {
    $activity = $this->conn->real_escape_string($activity);

    $query = "INSERT INTO todos (activity) VALUES ('$activity')";
    return $this->conn->query($query);
  }

  public function updateTodo($id, $activity, $status)
  {
    $activity = $this->conn->real_escape_string($activity);

    $query = "UPDATE todos SET activity='$activity', status=$status WHERE id=$id";
    return $this->conn->query($query);
  }

  public function deleteTodo($id)
  {
    $query = "DELETE FROM todos WHERE id=$id";
    return $this->conn->query($query);
  }
}
