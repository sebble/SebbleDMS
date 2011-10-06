<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>{$group} :: {$page}</title>
  
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
<body class="admin">
<div id="admin-head"><span class="form">{$username} - [<a href="Admin_public_signOut" class="action">Sign Out</a>]</span> <h1>[<em><a href="#">{$appname}</a></em>] - {$group} :: {$page}</h1></div>
<div id="admin-side">
    <div>
        <h3>Dashboard</h3>
        <ul>
        <li><a href="#">Overview</a></li>
        </ul>
    </div>
    <div>
        <h3>Website</h3>
        <ul>
            <li><a href="#">Configure</a></li>
            <li><a href="#">Options</a></li>
        </ul>
    </div>
    <div>
        <h3>Pages</h3>
        <ul>
            <li><a href="#">Manage</a></li>
            <li><a href="#">Content</a></li>
        </ul>
    </div>
    <div>
        <h3>Pages</h3>
        <ul>
            <li><a href="#">Manage</a></li>
            <li><a href="#">Content</a></li>
        </ul>
    </div>
</div>
<div id="admin-main">
    <h2>{$group} :: {$page}</h2>

