<?php





exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>workies? <?= $_SERVER['PATH_INFO']; ?></title>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>
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
echo "</pre>";

?>
<br /><br /><br /><br /><br /><br /><br /><br />
<a href="one?one">one</a>
<a href="two?two=2">two</a>
<a href="three?3=three&yes">three</a>
<a href="mailto:sebble@sebble.com">mail</a>
<a href="http://www.google.com">external</a>
<a href="three?3=three&yes">three</a>

<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />

</body>
</html>
