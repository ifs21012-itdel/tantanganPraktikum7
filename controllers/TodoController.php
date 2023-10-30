<?php
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

      $todoModel = new Todo();
      $query = "SELECT activity FROM todos WHERE activity = '$activity'";
      $result = $todoModel->runQuery($query);

      if($result->num_rows > 0){
        $error_message = "";
        $type = "danger";
      } else{
        $result = $todoModel->createTodo($activity);
        if($result){
          header('Location: index.php');
        }
      }
      $todos = $todoModel->getAllTodos();
      include(__DIR__ . '/../views/index.php');
    }
  }

  public function update()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $activity = $_POST['activity'];
      $status = $_POST['status'];

      $todoModel = new Todo();
      $result = $todoModel->updateTodo($id, $activity, $status);
      if ($result) {
        header('Location: index.php');
      }
    }
  }

  public function delete()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
      $id = $_GET['id'];

      $todoModel = new Todo();
      $result = $todoModel->deleteTodo($id);
      if ($result) {
        header('Location: index.php');
      }
    }
  }
}
