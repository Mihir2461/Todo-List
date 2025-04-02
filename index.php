<?php 
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$delete=false;
// Connect to DB
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes";
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Sorry we failed to connect: " . mysqli_connect_error());
} 

$insert = false; // Initialize $insert as false by default

if (isset($_GET['delete'])) {  // Corrected $_GET
  $sno = $_GET['delete'];
  $delete = true;
  $sql = "DELETE FROM `notes` WHERE `sno` = $sno";  // Corrected backticks
  $result = mysqli_query($conn, $sql);
  if (!$result) {
    echo "Error: " . mysqli_error($conn);
  }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['snoEdit'])) {
        // This is an edit request
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"]; // Using 'titleEdit' here to match the form field
        $description = $_POST["descriptionEdit"]; // Using 'descriptionEdit' here to match the form field
        
        // Correct the SQL update query
        $sql = "UPDATE `notes` SET `title`='$title', `description`='$description' WHERE `notes`.`sno`=$sno";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: /todoList/index.php?update=success");
            exit(); 
        } else {
            echo "Error: " . mysqli_error($conn); 
        }

    } else {
        // This is an insert request
        $title = $_POST["title"];
        $description = $_POST["description"];
        
        // SQL query to insert a new note into the database
        $sql = "INSERT INTO `notes`(`title`, `description`) VALUES('$title','$description')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Redirect to prevent form resubmission
            header("Location: /todoList/index.php?insert=success");
            exit(); 
        } else {
            echo "Error: " . mysqli_error($conn); 
        }
    }
}

// Check for successful insert or update
$insert = isset($_GET['insert']) && $_GET['insert'] == 'success';
$update = isset($_GET['update']) && $_GET['update'] == 'success';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <title>ToDoList</title>

    <script>
      const hideAlertAfterTimeout = () => {
        const alertBox = document.getElementById('successAlert');
        if (alertBox) {
          setTimeout(() => {
            alertBox.style.display = 'none';
          }, 3000); 
        }
      };
      window.onload = hideAlertAfterTimeout;
    </script>
  </head>
  <body>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/todoList/index.php" method="POST">
          <input type="hidden" name="snoEdit" id="snoEdit">
          <div class="mb-3 my-5">
              <h2>Edit Note</h2>
              <label for="titleEdit" class="form-label">Note Title</label>
              <input type="text" class="form-control" id="titleEdit" name="titleEdit" required>
          </div>
          <div class="form-floating">
              <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" required></textarea>
              <label for="descriptionEdit" class="form-label">Note Description</label>
          </div>
          <button type="submit" class="btn btn-primary my-3">Save Changes</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">ToDoList</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">About</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="#" tabindex="-1" aria-disabled="false">Contact</a>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<!-- Success message display -->
<?php 
if ($insert) {
    echo "<div id='successAlert' class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success! </strong> Your note has been inserted successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}

if ($update) {
    echo "<div id='successAlert' class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success! </strong> Your note has been updated successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}

if ($delete) {
  echo "<div id='successAlert' class='alert alert-success alert-dismissible fade show' role='alert'>
  <strong>Success! </strong> Your note has been deleted successfully.
  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
}
?>

<div class="container">
  <form action="/todoList/index.php" method="POST">
    <div class="mb-3 my-5">
      <h2>Add a Note</h2>
      <label for="title" class="form-label">Note Title</label>
      <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="form-floating">
      <textarea class="form-control" placeholder="Leave a comment here" id="description" name="description" required></textarea>
      <label for="description" class="form-label">Note Description</label>
    </div>
    <button type="submit" class="btn btn-primary my-3">Add Note</button>
  </form>
</div>

<div class="container">
  <table class="table" id="myTable">
    <thead>
      <tr>
        <th scope="col">Sno</th>
        <th scope="col">Title</th>
        <th scope="col">Desc</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
    <?php 
      $sql = "SELECT * FROM `notes`";
      $result = mysqli_query($conn, $sql);
      $sno = 0;
      while($row = mysqli_fetch_assoc($result)){
        $sno=$sno+1;
        echo "<tr>
              <th scope='row'>". $sno."</th>
              <td>". $row['title']."</td>
              <td>". $row['description']."</td>
              <td><button class='edit btn btn-primary' id=".$row['sno'].">Edit</button> 
              <button class='delete btn btn-sm btn-primary' id=d".$row['sno'].">Delete</button> </td>
            </tr>";
      }
    ?>
    </tbody>
  </table>
</div>
<hr>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- Initialize DataTable -->
<script>
  $(document).ready(function() {
    $('#myTable').DataTable();
  });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- JavaScript for Edit Modal -->
<script>
  edits=document.addEventListener('DOMContentLoaded', function() {
    const edits = document.getElementsByClassName('edit');
    Array.from(edits).forEach((element) => {
      element.addEventListener("click", (e) => {
        console.log("edit clicked");
        const tr = e.target.parentNode.parentNode;
        const title = tr.getElementsByTagName("td")[0].innerText;
        const description = tr.getElementsByTagName("td")[1].innerText
        console.log(title, description);
        document.getElementById('titleEdit').value = title;
        document.getElementById('descriptionEdit').value = description;
        document.getElementById('snoEdit').value = e.target.id;
        $('#editModal').modal('toggle');
      });
    });
  });

  deletes = document.getElementsByClassName('delete');
Array.from(deletes).forEach((element) => {
  element.addEventListener("click", (e) => {
    console.log("delete clicked");
    const sno = e.target.id.substr(1); // Extract ID from button
    if(confirm("Are you sure you want to delete this note?")) {
      window.location = `/todoList/index.php?delete=${sno}`;
    }
  });
});

  
</script>
  </body>
</html>
