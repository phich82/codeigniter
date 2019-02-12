<html>
<head>
    <title>Form Validation</title>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="<?php echo asset('css/lib/bootstrap.min.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Form Validation Test</h1>
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

            <h5>Email Address</h5>
            <?php if (array_key_exists('email', $errors)): ?>
                <label class="text-danger"><?php echo form_error('email', null, null); ?></label><br>
            <?php endif ?>
            <input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" />

            <h5>Educations</h5>
            <input type="text" name="levels[]" value="<?php echo set_value('levels[0]'); ?>" />
            <input type="text" name="levels[]" value="<?php echo set_value('levels[1]'); ?>" style="margin-top: 5px; margin-bottom: 5px;" />
            <input type="text" name="levels[]" value="<?php echo set_value('levels[2]'); ?>" />

            <h5>Roles</h5>
            <input type="checkbox" name="roles[role][]" value="1" <?php echo set_value('roles[role][0]') == 1 ? ' checked' : ''; ?> /> Admin<br>
            <input type="checkbox" name="roles[role][]" value="2" <?php echo set_value('roles[role][1]') == 2 ? ' checked' : ''; ?> /> Manager<br>
            <input type="checkbox" name="roles[role][]" value="3" <?php echo set_value('roles[role][2]') == 3 ? ' checked' : ''; ?> /> Leader<br>
            <input type="checkbox" name="roles[role][]" value="4" <?php echo set_value('roles[role][3]') == 4 ? ' checked' : ''; ?> /> Member

            <h5>Colors</h5>
            <input type="radio" name="colors[]" value="1" <?php echo set_value('colors[0]') == 1 ? ' checked' : ''; ?> /> Blue<br>
            <input type="radio" name="colors[]" value="2" <?php echo set_value('colors[0]') == 2 ? ' checked' : ''; ?> /> Red<br>
            <input type="radio" name="colors[]" value="3" <?php echo set_value('colors[0]') == 3 ? ' checked' : ''; ?> /> Yellow<br>
            <input type="radio" name="colors[]" value="6" <?php echo set_value('colors[0]') == 4 ? ' checked' : ''; ?> /> Organe

            <h5>Datetime</h5>
            <input type="text" name="datetime" value="<?php echo set_value('datetime'); ?>" />

            <h5>Password</h5>
            <?php if (array_key_exists('password', $errors)): ?>
                <label class="text-danger"><?php echo form_error('password', null, null); ?></label><br>
            <?php elseif(array_key_exists('passconf', $errors)): ?>
                <label class="text-danger"><?php echo form_error('passconf', null, null); ?></label><br>
            <?php endif ?>
            <input type="text" name="password" value="" size="50" />

            <h5>Password Confirm</h5>
            <input type="text" name="passconf" value="" size="50" />

            <div style="margin-top: 10px;">
                <input class="btn btn-primary" type="submit" value="Submit" />
            </div>
        </form>
    </div>
</body>
<script src="<?php echo asset('js/lib/jquery-3.3.1.min.js'); ?>"></script>
<script src="<?php echo asset('js/lib/bootstrap.min.js'); ?>"></script>
</html>
