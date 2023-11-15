<?php

require_once '../private/Libs/Utils.php';
require_once '../private/Libs/Validation.php';


if ($_POST) {
    $fields = [
        'username' => 'required | unique: users,username | alpha | between: 5,15',
        'password' => 'required | secure',
        'passwordRepeat' => 'required | same: password',
        'email' => 'required | email | unique: users, email',
    ];

    $customMessages = [
            'username' => [
                    'required'=>'You must provide a %s',
                    'unique' => 'This %s is already used',
                    'between' => 'The %s must be between %d and %d characters',
                    'alpha' => 'A %s must contain only alpha characters'
            ],
            'email' => [
                    'required' => 'You must provide an %s',
                    'email' => 'You must provide a valid %s',
                    'unique' => 'This %s is already used'
            ]
    ];

    $validator = new Validator($_POST, $fields, $customMessages);
    $validationErrors = $validator->validate();

    if (!empty($validationErrors)) {
        die(json_encode($validationErrors));
    }
}
?>

<?php renderView('header', ['title' => 'Register']) ?>

<h1>CAMAGRU</h1>
<form action="/register.php" method="post" id="login">
    <div class="custom-field">
        <input type="email" name="email" id="email"/>
        <label class="placeholder" for="email">Email</label>
    </div>

    <div class="custom-field">
        <input type="text" name="username" id="username"/>
        <label class="placeholder" for="username">Username</label>
    </div>

    <div class="custom-field">
        <input type="password" name="password" id="password"/>
        <label class="placeholder" for="password">Password</label>
    </div>

    <div class="custom-field">
        <input type="password" name="passwordRepeat" id="password-confirm"/>
        <label class="placeholder" for="password-confirm">Password Confirmation</label>
    </div>

    <button class="custom-button" type="submit">Register</button>
</form>

<?php renderView('footer') ?>
