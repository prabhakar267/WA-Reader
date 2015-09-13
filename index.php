<?php
if(isset($_FILES['file'])){
  $errors= array();
  $file_name = $_FILES['file']['name'];
  $file_tmp =$_FILES['file']['tmp_name'];

  if($_FILES["file"]["type"] !== "text/plain"){
     $errors[]="extension not allowed.";
  }
  
  if(empty($errors)==true){
     move_uploaded_file($file_tmp, "conversations/" . $file_name);
     header('Location: read.php?filename=' . $file_name);
  } else {
     print_r($errors);
  }
}
    require 'inc/header.inc.php';
    require 'inc/navbar.inc.php';
?>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="upload-popup">
                    Upload Whatsapp Text (.txt) file and view chat in a readable format<br>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="file" name="file">
                        <input type="submit" class="btn btn-lg btn-warning" >
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
    require 'inc/footer.inc.php';
?>