<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
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
            background-image: url('<?= validate_image($_settings->info('cover')) ?>');
            background-repeat: no-repeat;
            background-size: cover;
        }

        .login-container {
            background: linear-gradient(to bottom right, #9CDC78, #74DCB0);
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            padding: 20px;
            text-align: center;
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

        .form-group input, .form-group select {
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

        .next-button, .back-button {
            padding: 10px 20px;
            background: #00bfa5;
            color: #fff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1em;
        }

        .next-button:hover, .btn-primary:hover, .back-button:hover {
            background: #00796b;
        }

        .login-msg {
            color: #00796b;
            padding: 0px;
        }

        .input-group-append {
            display: flex;
            align-items: center;
            padding: 10px;
        }

        .input-group-text {
            cursor: pointer;
        }

        @media (max-width: 1000px) {
            .login-container {
                margin-top: 100px;
            }
        }
    </style>

    <div class="login-container">
        <div class="clear-fix my-2"></div>
        <div class="login-header">
            <h1>Create an Account</h1>
            <p class="login-msg">Sign in to start your session</p>
        </div>
        <div class="login-body">
            <form id="step1-frm">
                <div class="form-group">
                    <label for="shop_owner" class="control-label">Fullname</label>
                    <input type="text" id="shop_owner" name="shop_owner" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="shop_name" class="control-label">Shop Name</label>
                    <input type="text" id="shop_name" name="shop_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact #</label>
                    <input type="text" id="contact" name="contact" class="form-control" required>
                </div>
                <div class="form-footer">
                    <div>
                        <a href="<?= base_url ?>">Back to Site</a>
                    </div>
                    <div>
                        <button type="button" class="next-button" onclick="showStep2()">Next</button>
                    </div>
                </div>
                <div class="text-center">
                    <a href="<?= base_url . 'vendor/login.php' ?>">Already have an Account</a>
                </div>
            </form>

            <form id="step2-frm" style="display: none;" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="shop_type_id" class="control-label">Shop Type</label>
                    <select id="shop_type_id" name="shop_type_id" class="form-control select2" required>
                        <option value="" disabled selected></option>
                        <?php
                        $types = $conn->query("SELECT * FROM `shop_type_list` where delete_flag = 0 and `status` = 1 order by `name` asc ");
                        while ($row = $types->fetch_assoc()) :
                        ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user_id" class="control-label">Barangay Farmers Association</label>
                    <select id="user_id" name="user_id" class="form-control select2" required>
                        <option value="" disabled selected></option>
                        <?php
                        $users = $conn->query("SELECT id, firstname FROM `users` ORDER BY `firstname` ASC");
                        while ($row = $users->fetch_assoc()) :
                        ?>
                            <option value="<?= $row['id'] ?>"><?= $row['firstname'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-eye-slash pass_view"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cpassword" class="control-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="cpassword" class="form-control" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-eye-slash pass_view"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="logo" class="control-label">Shop Logo</label>
                    <input type="file" id="logo" name="img" class="form-control" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg" required>
                </div>
                <div class="form-group text-center">
                    <img src="<?= validate_image('') ?>" alt="Shop Logo" id="cimg" class="border border-gray img-thumbnail">
                </div>
                <div class="form-footer">
                    <button type="button" class="back-button" onclick="showStep1()">Back</button>
                    <button type="submit" class="next-button">Create Account</button>
                </div>
                <div class="text-center">
                    <a href="<?= base_url . 'vendor/login.php' ?>">Already have an Account</a>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <script>
        function displayImg(input, _this) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#cimg').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#cimg').attr('src', '<?= validate_image('') ?>');
            }
        }

        function showStep1() {
            $('#step1-frm').show();
            $('#step2-frm').hide();
        }

        function showStep2() {
            localStorage.setItem('shop_owner', $('#shop_owner').val());
            localStorage.setItem('shop_name', $('#shop_name').val());
            localStorage.setItem('contact', $('#contact').val());
            $('#step1-frm').hide();
            $('#step2-frm').show();
        }

        $(function() {
            end_loader();
            $('body').height($(window).height());
            $('.select2').select2({
                placeholder: "Please Select Here",
                width: '100%'
            });
            $('.select2-selection').addClass("form-border");
            $('.pass_view').click(function() {
                var _el = $(this).closest('.input-group');
                var type = _el.find('input').attr('type');
                if (type == 'password') {
                    _el.find('input').attr('type', 'text').focus();
                    $(this).find('i.fa').removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    _el.find('input').attr('type', 'password').focus();
                    $(this).find('i.fa').removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });

            $('#step2-frm').submit(function(e) {
                e.preventDefault();
                var _this = $(this);
                $('.err-msg').remove();
                var el = $('<div>');
                el.addClass("alert err-msg");
                el.hide();
                if (_this[0].checkValidity() == false) {
                    _this[0].reportValidity();
                    return false;
                }
                if ($('#password').val() != $('#cpassword').val()) {
                    el.addClass('alert-danger').text('Password does not match.');
                    _this.append(el);
                    el.show('slow');
                    $('html, body').scrollTop(0);
                    return false;
                }
                start_loader();
                var formData = new FormData($(this)[0]);
                formData.append('shop_owner', localStorage.getItem('shop_owner'));
                formData.append('shop_name', localStorage.getItem('shop_name'));
                formData.append('contact', localStorage.getItem('contact'));

                $.ajax({
                    url: _base_url_ + "classes/Users.php?f=save_vendor",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    dataType: 'json',
                    error: err => {
                        console.error(err);
                        el.addClass('alert-danger').text("An error occurred");
                        _this.prepend(el);
                        el.show('.modal');
                        end_loader();
                    },
                    success: function(resp) {
                        if (typeof resp == 'object' && resp.status == 'success') {
                            localStorage.removeItem('shop_owner');
                            localStorage.removeItem('shop_name');
                            localStorage.removeItem('contact');
                            location.href = './login.php';
                        } else if (resp.status == 'failed' && !!resp.msg) {
                            el.addClass('alert-danger').text(resp.msg);
                            _this.prepend(el);
                            el.show('.modal');
                        } else {
                            el.text("An error occurred");
                            console.error(resp);
                        }
                        $("html, body").scrollTop(0);
                        end_loader();
                    }
                });
            });
        });
    </script>
</body>
</html>
