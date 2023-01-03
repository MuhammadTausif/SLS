<?php
include('../includes/database_conn.php');

if(isset($_POST['delete_subject'])){
    
        $id = $_POST['delete_subject'];
        
        $conn = $pdo->open();
        try{
        $stmt = $conn->prepare("DELETE FROM sls_subjects WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        
        }catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
        }

        $pdo->close();
    }
header('location: subjects.php');
?>