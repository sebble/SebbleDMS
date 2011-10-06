<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  
  <meta name="description" content="SebbleDMS application page missing" />
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
<h1>Page is missing</h1>
<form class="form">
<p><label>Username</label><input type="text" disabled value="{$user->details['username']}" /></p>
<p><label>Password</label><input type="password" disabled value="********" /></p>
<p><label>Domain</label><select id="domain" disabled><option selected>{$user->domain}</option></select></p>
<p><input type="submit" name="Admin_public_signOut" value="Sign Out" /></p>
</form>{$session['activity']}<br>{$session['logintime']}<br>{$session['username']}<br>{$session['domain']}
</div>
</body>
</html>
