
<html>
<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="login2.css" rel="stylesheet">
</head>
<body>
<div style="background-color:#004732">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<img width="256" alt="mas madrid" src="logo-masmadrid.svg" />
<br />
<br />
</div>




<div class="wrapper fadeInDown">
  <div class="row">
  <div class="col-md-12 login-form-1">
      <h3>Acceso al sistema</h3>
      <form action="<?php echo $script; ?>" method="POST" name="login_form1">
          <div class="form-group">
              <input type="text" id="user" name="user" class="form-control" placeholder="Usuario" value="" />
          </div>
          <div class="form-group">
              <input type="password" id="password" name="password" class="form-control" placeholder="Password" value="" />
          </div>
          <div class="form-group">
              <input type="submit" class="btnSubmit" value="Acceder" />
          </div>
           <input type="hidden" name="action" id="action" value="LOGIN">
      </form>
  </div>


</div>
</body>
</html>
