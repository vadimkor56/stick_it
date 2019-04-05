<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!array_key_exists("secure", $_SESSION) || $_SESSION['secure'] == false) {
  header("Location: login.php");
  exit();
}

$host = "<your_host>";
$db = "<your_database>";
$password = "<your_password>";
$link = mysqli_connect($host, $db, $password, $db);
if (mysqli_connect_error()) {
  echo "<b>Failure:</b><br>";
  echo mysqli_connect_error()."<br>";
  die("<b>Unable to connect</b><br>");
}

$email = $_SESSION['email'];
$headings = [];
$notes = [];
$file_links = [];
$file_names = [];
$dates = [];
$ids = [];

$query = "select * from notes where email = '".mysqli_real_escape_string($link, $email)."'"; 
if ($result = mysqli_query($link, $query)) {
  while($row = mysqli_fetch_array($result)) {
    array_push($headings, $row['heading']);
    array_push($notes, $row['note']);
    array_push($file_links, $row['file_link']);
    array_push($file_names, $row['filename']);
    array_push($dates, $row['date']);
    array_push($ids, $row['id']);
  }
}

if ($_POST) {
  if (array_key_exists('submit-delete', $_POST) && $_POST['submit-delete'] != "") {
    $del_item = $_POST['submit-delete'];
    $query = "delete from notes where date='".$del_item."'";
    if (!mysqli_query($link, $query)) {
      echo "Error!!";
    }
    header("Refresh:0");
  } else if (array_key_exists('submit-edit', $_POST) && $_POST['submit-edit'] != "") {
    $edit_item = $_POST['submit-edit'];
    $edit_heading = "";
    $edit_note = "";
    $query = "select * from notes where date = '".mysqli_real_escape_string($link, $edit_item)."'"; 
    if ($result = mysqli_query($link, $query)) {
      if ($row = mysqli_fetch_array($result)) {
        $edit_heading = $row['heading'];
        $edit_note = $row['note'];
      }
      $edit_heading = str_replace("<br />", "", $edit_heading);
      $edit_note = str_replace("<br />", "", $edit_note);
      $edit_date = $edit_item;
    }
  } else if (array_key_exists('submit-update', $_POST) && $_POST['submit-update'] != "") {
    $update_item = $_POST['submit-update'];
    $heading = $_POST["heading"];
    $note = $_POST["note"];
    $note = nl2br($note);
    $filename = basename($_FILES["file"]["name"]);
    $uploadfile = "";
    
    if(file_exists($_FILES['file']['tmp_name'])) {
      $uploaddir = "uploads/";
      $uploadfile = $uploaddir.$filename;
      move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile); 
    }
    
    $query = "update notes set heading='".$heading."', note='".$note."', file_link='".$uploadfile."', filename='".mysqli_real_escape_string($link, $filename)."' where date = '".mysqli_real_escape_string($link, $update_item)."'";
    if (!mysqli_query($link, $query)) {
      echo "Error!!";
    }
    header("Refresh:0");
  } else if (array_key_exists('submit-close', $_POST) && $_POST['submit-close'] != "") {
    header("Refresh:0");
  }
  else {
    $heading = $_POST["heading"];
    $note = $_POST["note"];
    $note = nl2br($note);
    $filename = basename($_FILES["file"]["name"]);
    $uploadfile = "";

    if(file_exists($_FILES['file']['tmp_name'])) {
      $uploaddir = "uploads/";
      $uploadfile = $uploaddir.$filename;
      move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile); 
    }

    $date = date('l jS \of F Y h:i:s A');

    $query = "insert into notes (`email`,`heading`, `note`, `file_link`, `filename`, `date`) values ('".mysqli_real_escape_string($link, $email)."', '".mysqli_real_escape_string($link, $heading)."', '".mysqli_real_escape_string($link, $note)."', '".$uploadfile."', '".mysqli_real_escape_string($link, $filename)."', '".$date."')";
    if (!mysqli_query($link, $query)) {
      echo "Error!";
      echo $query;
    }
    header("Refresh:0");
  }
  


}

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    
    <link rel="shortcut icon" href="images/icon.png" type="image/x-icon">
    <link href="jquery-ui/jquery-ui.css" rel="stylesheet">

    <title>Stick It</title>
    
    <style>
      
      #refresh-btn {
        display: flex;
        justify-content: flex-end;
      }
      
      .container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
      }
      
      button, input, textarea {
        outline: none !important;
      }
      
      .note {
        height: auto;
        min-height: 100px;
        max-width: 500px;
        width: auto;
        min-width: 100px;
        color: black;
        border-radius: 20px;
        border: 1px solid grey;
        background-color: aliceblue;
        margin: 10px;
        padding: 10px;
        
      }
      h3 {
        margin-top: 20px;
      }
      
      .note p, .note h3 {
        box-sizing: border-box;
        width: 100%;
        word-wrap: break-word;
      }
      
      #new-note-div {
        align-self: center;
        margin: 7px auto;
        height: auto;
/*        border: 0.5px solid black;*/
        padding: 0px;
        border-radius: 15px;
      }
      
      #new-note-div button, #new-note-div input {
        border-radius: 0px;
      }
      
      #new-heading-input {
        border-top-left-radius: 15px !important;
        border-top-right-radius: 15px !important;
        border: 1px solid #ECD9D7;
      }
      
      #new-upload-input {
        border-bottom-left-radius: 15px !important;
      }
      
      #close-btn {
        border-bottom-right-radius: 15px !important;
      }
      
      
      #new-heading-input, .new-file-input, #new-upload-input, #close-btn {
        display: none;
      }
      
      .input-file {
        width: 100%;
      }
      
      .new-file-input {

        width: 120%;
        border: 1px solid #ECD9D7;
      }
      
      #logout-btn {
        position: absolute;
        right: 10px;
        top: 10px;
        z-index: 1;
      }
      
      #new-note-input {
        border-radius: 0px;
        border: 1px solid #ECD9D7;
      }
      
      .delete-note-btn {
        position: absolute;
        top: 0px;
        right: 0px; 
      }
      
      .edit-note-btn {
        position: absolute;
        top: 0px;
        left: 0px; 
      }
      
      nav {
        max-height: 56px !important;
      }
      
      #main-field {
        padding: 0px;
      }
      
      .highlight {
        background-color: yellow;
      }
      
      .no-radius {
        border-radius: 0px !important;
      }
      
      
    </style>
  </head>
  <body>
    <a href="login.php"><button id="logout-btn" class="btn btn-success">Log out</button></a>
    <nav class="navbar navbar-light bg-light d-flex justify-content-center row">
      <div class="form-inline col-12 row">
        <a class="navbar-brand col-3" href="#">
          <i class="far fa-sticky-note"></i>
          StickIt
        </a>
        <input id="search-input" class="form-control col-5 d-none d-sm-block" type="search" placeholder="Search" aria-label="Search">
        <button id="search-btn" class="btn btn-outline-success d-none d-sm-block"><i class="fas fa-search"></i></button>
      </div>
    </nav>
    <?php
    if (array_key_exists('submit-edit', $_POST) && $_POST['submit-edit'] != "") {
      echo '<form action="#" method="post" enctype="multipart/form-data" id="new-note-div" class="container row ">
      <input id="new-heading-input" name="heading" type="text" class="col-12 d-block" placeholder="Heading" autocomplete="off" value="'.$edit_heading.'">
      <textarea id="new-note-input" name="note" rows="4" type="text" class="col-12 no-radius" placeholder="New note" autocomplete="off">'.$edit_note.'</textarea>
      
      <input class="new-file-input" type="file" name="file" class="form-control-file col-12 d-block" id="exampleFormControlFile1">

      
      <button id="new-upload-input" name="submit-update" type="submit" class="col-6 btn btn-outline-success d-block" value="'.$edit_date.'">Update</button>
      <button id="close-btn" type="submit" name="submit-close" value="1" class="col-6 btn btn-outline-primary d-block">Close</button>
    </form>';
    } else {
      echo '<form action="#" method="post" enctype="multipart/form-data" id="new-note-div" class="container row ">
      <input id="new-heading-input" name="heading" type="text" class="col-12" placeholder="Heading" autocomplete="off">
      <textarea id="new-note-input" name="note" type="text" class="col-12" placeholder="New note" autocomplete="off" required></textarea>
      
      <input class="new-file-input" type="file" name="file" class="form-control-file col-12" id="exampleFormControlFile1">

      
      <input id="new-upload-input" type="submit" class="col-6 btn btn-outline-success" value="Upload">
      <button id="close-btn" type="button" class="col-6 btn btn-outline-primary">Close</button>
    </form>';
    }
    ?>
    
    <div id="main-field" class="container">
      <?php 
      for ($i = sizeof($notes) - 1; $i >= 0; $i--) {
        echo 
          '<div class="note drag-shape alert alert-success" data-toggle="tooltip" data-placement="top" title="'.$dates[$i].'">
            <form method="post" class="edit-note-btn" data-toggle="tooltip" data-placement="right" title="Edit">
              <button name="submit-edit" value="'.$dates[$i].'" type="submit" class="btn btn-default"><i class="fas fa-edit"></i></button>
            </form>
            <form method="post" class="delete-note-btn" data-toggle="tooltip" data-placement="right" title="Delete">
              <button name="submit-delete" value="'.$dates[$i].'" type="submit" class="btn btn-default"><i class="far fa-minus-square"></i></button>
            </form>
            <h3>'.$headings[$i].'</h3><hr>
            <p>'.$notes[$i].'</p>';
        if ($file_names[$i]) {
          echo '<hr><p><a href="'.$file_links[$i].'" target="_blank"><i class="fas fa-file-download"></i> '.$file_names[$i].'</p></a>
          </div>';  
        } else {
          echo '</div>';
        }
      }
      ?>
      
    </div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
    crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="jquery-ui/jquery-ui.js"></script>
  
    <script>
      $("#new-note-input").css("border-radius", "15px");
      
      $("#new-note-input").click(function() {
        $(this).css("height", "100px").css("border-radius", "0px");
        $("#new-heading-input").css("display", "block");
        $(".new-file-input").css("display", "block");
        $("#new-upload-input").css("display", "block");
        $("#close-btn").css("display", "block");
      });
      
      $("#close-btn").click(function() {
        $(".no-radius").removeClass("no-radius");
        $("#new-note-input").css("height", "auto").css("border-radius", "15px");
        $(".d-block").removeClass("d-block");
        $("#new-heading-input").css("display", "none");
        $(".new-file-input").css("display", "none");
        $("#new-upload-input").css("display", "none");
        $("#close-btn").css("display", "none");
        
        $("#new-note-input").val(null);
        $("#new-heading-input").val(null);
        $("#new-file-input").val(null);
      });
      
   
      
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      });
      
      $(function() {
        $( ".drag-shape" ).draggable({ containment: "parent" });
      });
      
      function highlight(text) {
        var inputTexts = document.getElementsByClassName("note");
        for (i = 0; i < inputTexts.length; i++) {
          var innerHTML = inputTexts[i].innerHTML;
          var index = innerHTML.indexOf(text);
          if (index >= 0) { 
            innerHTML = innerHTML.substring(0,index) + "<span class='highlight'>" + innerHTML.substring(index,index+text.length) + "</span>" + innerHTML.substring(index + text.length);
            inputTexts[i].innerHTML = innerHTML;
          }  
        }
        
      }
      
      $("#search-btn").click(function() {
        $(".highlight").removeClass("highlight");
        var text = $("#search-input").val();
        highlight(text);
      });
      
      
      
      
      function bs_input_file() {
          $(".input-file").before(
              function() {
                  if ( ! $(this).prev().hasClass('input-ghost') ) {
                      var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
                      element.attr("name",$(this).attr("name"));
                      element.change(function(){
                          element.next(element).find('input').val((element.val()).split('\\').pop());
                      });
                      $(this).find("button.btn-choose").click(function(){
                          element.click();
                      });
                      $(this).find("button.btn-reset").click(function(){
                          element.val(null);
                          $(this).parents(".input-file").find('input').val('');
                      });
                      $(this).find('input').css("cursor","pointer");
                      $(this).find('input').mousedown(function() {
                          $(this).parents('.input-file').prev().click();
                          return false;
                      });
                      return element;
                  }
              }
          );
      }
      $(function() {
          bs_input_file();
      });
      
    </script>
  </body>
</html>