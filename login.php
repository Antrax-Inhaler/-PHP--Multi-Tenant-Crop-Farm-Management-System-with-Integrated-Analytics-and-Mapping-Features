<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">
  <script>
    start_loader()
  </script>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #e0e0e0;
      margin: 0;
    }

    .login-container {
      background: linear-gradient(to bottom right, #9CDC78, #74DCB0);
      border-radius: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      width: 400px;
      padding: 20px;
      text-align: center;
      margin-top: 300px;
    }

    #logo-img {
      width: 15em;
      height: 15em;
      object-fit: scale-down;
      object-position: center center;
      border-radius: 50%;
    }

    #system_name {
      color: #fff;
      text-shadow: 3px 3px 3px #000;
      margin-top: 20px;
    }

    .login-header {
      padding: 10px;
    }

    .login-header h1 {
      margin: 0;
      font-size: 2em;
      color: white;
    }

    .login-body {
      padding: 20px;
    }

    .login-body p {
      font-size: 0.8em;
      color: #666;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 15px;
      font-size: 1em;
    }

    .form-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .login-link {
      text-decoration: none;
      color: #333;
      font-size: 0.9em;
    }

    .next-button {
      padding: 10px 20px;
      background: #00bfa5;
      color: #fff;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      font-size: 1em;
    }

    .next-button:hover, .btn-primary:hover {
      background: #00796b;
    }

    .login-msg {
      color: #00796b;
      padding: 0px;
    }
    @media (max-width: 1000px) {
      .login-container {
        margin-top: 100px;
    }
  }
  </style>

  <?php if($_settings->chk_flashdata('success')): ?>
    <script>
      alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
  <?php endif;?>

  <div class="login-container">
    <div class="clear-fix my-2"></div>
    <div class="login-header">
      <h1>Customer Login</h1>
      <p class="login-msg">Sign in to start your session</p>
    </div>
    <div class="login-body">
      <form id="cclogin-frm" action="" method="post">
        <div class="form-group">
          <input type="email" class="form-control" name="email" autofocus placeholder="Email">
        </div>
        <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Password">
        </div>
        <div class="form-footer">
          <div>
            <a href="<?= base_url ?>">Back to Site</a>
          </div>
          <div>
            <button type="submit" class="next-button">Sign In</button>
          </div>
        </div>
        <center><a href="<?= base_url.'./register.php' ?>">Create an Account</a></center>
      </form>
    </div>
  </div>

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>

  <script>
    $(function(){
      end_loader();
      $('#cclogin-frm').submit(function(e){
        e.preventDefault();
        var _this = $(this)
          $('.err-msg').remove();
        var el = $('<div>')
          el.addClass("alert err-msg")
          el.hide()
        if(_this[0].checkValidity() == false){
          _this[0].reportValidity();
          return false;
        }
        start_loader();
        $.ajax({
          url: _base_url_+"classes/Login.php?f=login_client",
          data: new FormData($(this)[0]),
          cache: false,
          contentType: false,
          processData: false,
          method: 'POST',
          type: 'POST',
          dataType: 'json',
          error: err => {
            console.error(err)
            el.addClass('alert-danger').text("An error occurred");
            _this.prepend(el)
            el.show('.modal')
            end_loader();
          },
          success: function(resp){
            if(typeof resp =='object' && resp.status == 'success'){
              location.href= './';
            } else if(resp.status == 'failed' && !!resp.msg){
              el.addClass('alert-danger').text(resp.msg);
              _this.prepend(el)
              el.show('.modal')
            } else {
              el.text("An error occurred");
              console.error(resp)
            }
            $("html, body").scrollTop(0);
            end_loader()
          }
        })
      })
    })
  </script>
</body>
</html>
