<?php

echo "Hello World!";

class Todo
{
  private $conn;

  public function __construct()
  {
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

    if ($result) {
      while ($row = $result->fetch_assoc()) {
        $todos[] = $row;
      }
      $result->free(); // Bebaskan hasil query
    } else {
      // Tampilkan pesan kesalahan jika terjadi masalah dalam query
      echo "Kesalahan dalam mengambil data: " . $this->conn->error;
    }

    return $todos;
  }

  public function createTodo($activity)
  {
    $activity = $this->conn->real_escape_string($activity);

    $query = "INSERT INTO todos (activity) VALUES ('$activity')";
    $result = $this->conn->query($query);

    if (!$result) {
      // Tampilkan pesan kesalahan jika terjadi masalah dalam query
      echo "Kesalahan dalam membuat aktivitas: " . $this->conn->error;
    }

    return $result;
  }

  public function getOne($query, $params)
  {
    $stmt = $this->conn->prepare($query);

    if ($stmt) {
      $types = str_repeat('s', count($params));
      $stmt->bind_param($types, ...$params);
      $stmt->execute();
      $stmt->store_result();

      $result = null;

      if ($stmt->num_rows > 0) {
        $stmt->bind_result($result);
        $stmt->fetch();
      }

      $stmt->close();

      return $result;
    } else {
      // Tampilkan pesan kesalahan jika terjadi masalah dalam query
      echo "Kesalahan dalam persiapan statement: " . $this->conn->error;
      return null;
    }
  }

  public function updateTodo($id, $activity, $status)
  {
    $activity = $this->conn->real_escape_string($activity);

    $query = "UPDATE todos SET activity='$activity', status=$status WHERE id=$id";
    $result = $this->conn->query($query);

    if (!$result) {
      // Tampilkan pesan kesalahan jika terjadi masalah dalam query
      echo "Kesalahan dalam memperbarui aktivitas: " . $this->conn->error;
    }

    return $result;
  }

  public function deleteTodo($id)
  {
    $query = "DELETE FROM todos WHERE id=$id";
    $result = $this->conn->query($query);

    if (!$result) {
      // Tampilkan pesan kesalahan jika terjadi masalah dalam query
      echo "Kesalahan dalam menghapus aktivitas: " . $this->conn->error;
    }

    return $result;
  }
}



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

  public function getOne($query, $params)
  {
    $stmt = $this->conn->prepare($query);
    
    if ($stmt) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->store_result();
        
        $result = null;
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($result);
            $stmt->fetch();
        }
        
        $stmt->close();
        
        return $result;
    } else {
      echo "Aktivitas sudah ada, silakan masukkan aktivitas unik.";
    }
  }
}

require_once(__DIR__ . '/../models/Todo.php');

class TodoController
{
  public function index()
  {
    $todoModel = new Todo();
    $todos = $todoModel->getAllTodos();
    include(__DIR__ . '/../views/index.php');
  }

  public function create()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $activity = $_POST['activity'];

      if (!$this->isActivityUnique($activity)) {
        $todoModel = new Todo();
        $todoModel->createTodo($activity);
    } else {
        // Tampilkan pesan kesalahan kepada pengguna jika aktivitas sudah ada
        echo "Silakan masukkan aktivitas lain yang unik.";
    }
    }
    
    header('Location: index.php');
  }

  function isActivityUnique($activity){
    $query = "SELECT COUNT(*) FROM todos WHERE activity = ?";
    $todoModel = new Todo();
    $result = $todoModel->getOne($query, [$activity]);
    return $result === 0;
  }

  public function update()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $activity = $_POST['activity'];
      $status = $_POST['status'];

      $todoModel = new Todo();
      $todoModel->updateTodo($id, $activity, $status);
    }

    header('Location: index.php');
  }

  public function delete()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
      $id = $_GET['id'];

      $todoModel = new Todo();
      $todoModel->deleteTodo($id);
    }

    header('Location: index.php');
  }
}
