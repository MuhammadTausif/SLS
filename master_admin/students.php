<?php include('../includes/header.php'); ?>
<?php include('../includes/session.php'); ?>
<?php
if (isset($_POST['details_class_btn12'])) {
  $class_student_id = $_POST['details_class_btn12'];
  $_SESSION['current_class'] = $_POST['details_class_btn12'];
  $_SESSION['c_name'] = $_POST['c_name'];
  $class_name = $_POST['c_name'];
  $school_name = $_SESSION['school_name'];
  $school_class_id = $_SESSION['current_school'];
}
if (isset($_SESSION['current_class'])) {
  $class_student_id = $_SESSION['current_class'];
  $class_name = $_SESSION['c_name'];
  $school_name = $_SESSION['school_name'];
  $school_class_id = $_SESSION['current_school'];
}
?>

<body>
  <?php include('../includes/navbar.php'); ?>
  <div class="container-fluid">
    <div class="row-fluid">
      <?php //include('subject_sidebar.php'); 
      ?>

      <div class="span9" id="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "
                <div class='alert alert-danger alert-dismissible'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <h4><i class='icon fa fa-warning'></i> Error!</h4>
                  " . $_SESSION['error'] . "
                </div>
              ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
                <div class='alert alert-success alert-dismissible'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <h4><i class='icon fa fa-check'></i> Success!</h4>
                  " . $_SESSION['success'] . "
                </div>
              ";
          unset($_SESSION['success']);
        }
        if (isset($class_student_id)) {
          $conn = new PDO('mysql:host=localhost;dbname=sls', 'root', '');
          $sql = 'SELECT * FROM sls_teachers';
          try {
              $stmt = $conn->prepare($sql);
              $stmt->execute();
              $teachers_rows = $stmt->fetchAll();
          } catch (PDOException $e) {
              $_SESSION['error'] = $e->getMessage();
          }
          $teachers_selection =
              "<select name='teacher' class='custom-select'>";
          $teachers_selection =
              $teachers_selection .
              "<option value='-1'>Select Teacher</option>";
          foreach ($teachers_rows as $teacher_record) {
              $teachers_selection =
                  $teachers_selection .
                  "<option value='" .
                  $teacher_record['id'] .
                  "'>" .
                  $teacher_record['teacher_name'] .
                  '</option>';
          }
          $teachers_selection = $teachers_selection . '</select>';
          $stmt = $conn->prepare(
              "SELECT sls_students.*, sls_subjects.subject_name, 
              (SELECT COUNT(id) FROM sls_subjects WHERE sls_subjects.class_id = sls_students.classID) AS total_subjects 
              -- (SELECT COUNT(id) FROM sls_students WHERE sls_students.classID = sls_classes.id) AS total_students
              FROM sls_students
              LEFT JOIN sls_subjects
              ON sls_students.classID = sls_subjects.class_id
              WHERE sls_students.classID = $class_student_id AND sls_students.schoolID = $school_class_id
              GROUP BY sls_students.id;
              "
          );
          $stmt->execute();
          $rows = $stmt->fetchAll();

        ?>
        <div class="row-fluid"><br>
          <a href="classes.php" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Back to Classes</a>
          <a href="add_students.php" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Add Students</a>
          <!-- block -->
          <div id="block_bg" class="block">
            <div class="navbar navbar-inner block-header">
              <?php echo $school_name." > ".$class_name; ?>
              <!-- <div class="muted pull-left">Subjects List</div> -->
            </div>
            <div class="block-content /*collapse in*/">
              <div class="span12">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="example">

                  <!-- <a  id="delete_school" class="btn btn-danger" name="delete_school"><i class="icon-trash icon-large"></i>Delete</a> -->
                  <?php // include('delete_modal.php'); 
                  ?>
                  <thead>
                    <tr>

                      <th>Student Name</th>
                      <th>Subjects</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php
                    // 
                    //   $conn = new PDO("mysql:host=localhost;dbname=sls", "root", "");
                    //   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    //   $stmt = $conn->prepare("SELECT * FROM sls_students WHERE classID=$class_student_id");
                    //   $stmt->execute();
                    //   $rows = $stmt->fetchAll();
                      foreach ($rows as $row) {
                        $id = $row['id'];
                        $studentName = $row['s_name'];
                        $subjectName = $row['subject_name'];
                        $total_subjects = $row['total_subjects'];
                        if($total_subjects == 0){
                          $total_subjects = "<span class='badge bg-danger text-white ms-2'>0</span> Subjects &nbsp";
                          $total_subjects_text = "<button  type='submit' class='btn btn-warning' value='$id' name='details_studentProfile_btn'> $total_subjects</button>";
                        }
                        else{
                          $total_subjects = "<span class='badge bg-success text-white ms-2'>$total_subjects </span>  Subjects";
                          $total_subjects_text = "<button  type='submit' class='btn btn-primary' value='$id' name='details_studentProfile_btn'> $total_subjects</button>";
                        }

                        // $teacher_details = "<form class='form-inline mb-0'  action='edit_class.php' method='POST'>
                        //     <button type='submit' class='d-inline-block btn btn-warning' value='$id' name='edit_class'>Add Teacher</button>
                        //   </form>";
                        // if (!is_null($row['teacher_name'])) {
                        //     $teacher_details = $row['teacher_name'];
                        // }
                        echo " 
                            <tr> 
                              <td>$studentName</td>
                              <td> 
                                <form class='d-inline-block mb-0' action='student_profile.php' method='POST'>
                                  <input type='hidden' name='s_name' value='$studentName'>  
                                  $total_subjects_text
                                </form>
                               
                              </td>
                              
                              <td> 
                                <form class='d-inline-block mb-0' action='delete_student.php' method='POST'>       
                                  <button  type='submit' class='btn btn-danger' value=" . $row['id'] . " name='delete_student'><i class='bi bi-trash'></i></button>  
                                </form>
                                <form class='d-inline-block mb-0' action='edit_student.php' method='POST'>
                                  <button  type='submit' class='btn btn-success' value=" . $row['id'] . " name='edit_student'><i class='bi bi-pencil-square'></i></button>  
                                </form>
                                  
                              </td>
                            </tr>
                        ";
                      }
                    }
                    ?>

                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /block -->
    </div>
    <?php //include('footer.php'); 
    ?>
  </div>
  <?php include('../includes/script.php'); ?>
</body>

</html>