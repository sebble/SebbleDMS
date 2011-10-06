<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  
  <meta name="description" content="SebbleDMS application login" />
  <meta name="author" content="Sebastian Mellor &lt;sebble@sebble.com&gt;" />
  
  <link rel="stylesheet" href="../css/reset.css" />
  <link rel="stylesheet" href="../css/admin.css" />
  <link rel="stylesheet" href="../css/forms.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <script type="text/javascript" src="../js/jquery.min.js"></script>
  <script type="text/javascript" src="../js/admin.js"></script>
  <script type="text/javascript">
    $(function(){
      Admin.enhance();
    });
  </script>
</head>
<body id="login">
<div>
<!--<h1>Sign in to access things.</h1>-->
<h1>Sign in to SebbleDMS{* comment *}</h1>
<form class="form">
<p><label for="username">Username</label><input type="text" id="username" name="username" /></p>
<p><label for="password">Password</label><input type="password" id="password" name="password" /></p>
<p><label for="domain">Domain</label><select id="domain" name="domain">{foreach from=$domains item=d}<option value="{$d}">{$d}</option>{foreachelse}<option value="1">default</option>{/foreach}</select></p>
<p><input type="submit" name="Admin_public_signIn" value="Sign In" /></p>
</form>
</div>
</body>
</html>
