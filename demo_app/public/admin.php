<?php


if (count($_POST)>0) {
    
    require '../admin.controller.php';
    new Controller_Admin();
    exit;
}


#exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>workies? <?= $_SERVER['PATH_INFO']; ?></title>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>
  <link rel="stylesheet" href="../css/admin.css" />
<script>
$(function(){
    Admin.enhance();
});
</script>
</head>
<body>
<?php

echo "Hello World";

echo "<pre>";
print_r($_GET);
print_r($_SERVER['PATH_INFO']);
print_r($_FILES);
echo "</pre>";

?>
<form enctype="multipart/form-data" action="admin.php" method="POST">
Choose a file to upload: <input name="myuploadedfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>
<br /><br /><br /><br /><br /><br /><br /><br />
<a href="one?one">one</a>
<a href="two?two=2">two</a>
<a href="three?3=three&yes">three</a>
<a href="mailto:sebble@sebble.com">mail</a>
<a href="http://www.google.com">external</a>
<a href="three?3=three&yes">three</a>


<div class="form">
<input type="hidden" name="key1" value="one" />
<input type="text" name="data1" value="done" />
<input type="text" name="data2" value="" />
<input type="submit" name="Object_filter_anAction" value="An Action" />
</div>

<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />

</body>
</html>
