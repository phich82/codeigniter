<html>
<head>
    <title>My Form</title>

    <link rel="stylesheet" href="/assets/css/lib/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Form Validation Test</h1>

        <?php if (!empty($this->form_validation->error_array())): ?>
        <ul style="style-list: none;">
            <?php foreach($this->form_validation->error_array() as $field => $error): ?>
            <li class="alert alert-danger"><?php echo $field.': '.$error; ?></li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>

        <form action="/validation" method="post">
            <h5>Username</h5>
            <input type="text" name="username" value="" size="50" />

            <h5>Password</h5>
            <input type="text" name="password" value="" size="50" />

            <h5>Password Confirm</h5>
            <input type="text" name="passconf" value="" size="50" />

            <h5>Email Address</h5>
            <input type="text" name="email" value="" size="50" />

            <div style="margin-top: 10px;">
                <input class="btn btn-primary" type="submit" value="Submit" />
            </div>
        </form>
    </div>
</body>
<script src="/assets/js/lib/jquery-3.3.1.min.js"></script>
<script src="/assets/js/lib/bootstrap.min.js"></script>
</html>
