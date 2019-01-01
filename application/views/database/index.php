<html>
<head>
    <title>Database Basic</title>

    <link rel="shortcut icon" href="/assets/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="/assets/css/lib/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Database Action Test</h1>
        <?php $errors = $this->form_validation->error_array(); ?>
        <?php if (!empty($errors)): ?>
        <ul style="style-list: none;">
            <?php foreach($errors as $field => $error): ?>
            <li class="alert alert-danger"><?php echo $field.': '.$error; ?></li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>

        <form action="/validation" method="post">
            <h5>Username</h5>
            <?php if (array_key_exists('username', $errors)): ?>
                <label class="text-danger"><?php echo form_error('username', null, null); ?></label><br>
            <?php endif ?>
            <input type="text" name="username" value="<?php echo set_value('username'); ?>" size="50" />

            <h5>Password</h5>
            <?php if (array_key_exists('password', $errors)): ?>
                <label class="text-danger"><?php echo form_error('password', null, null); ?></label><br>
            <?php elseif(array_key_exists('passconf', $errors)): ?>
                <label class="text-danger"><?php echo form_error('passconf', null, null); ?></label><br>
            <?php endif ?>
            <input type="text" name="password" value="" size="50" />

            <h5>Password Confirm</h5>
            <input type="text" name="passconf" value="" size="50" />

            <h5>Email Address</h5>
            <?php if (array_key_exists('email', $errors)): ?>
                <label class="text-danger"><?php echo form_error('email', null, null); ?></label><br>
            <?php endif ?>
            <input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" />

            <div style="margin-top: 10px;">
                <input class="btn btn-primary" type="submit" value="Submit" />
            </div>
        </form>
    </div>
</body>
<script src="/assets/js/lib/jquery-3.3.1.min.js"></script>
<script src="/assets/js/lib/bootstrap.min.js"></script>
</html>
