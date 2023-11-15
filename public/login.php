<?php

require_once '../private/Libs/Utils.php';

session_start();

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    die();
}

$errors = [];

if ($_POST) {
    require_once 'Validation.php';
    require_once 'Database.php';

    $errors = validate($_POST, [
        'username' => 'required',
        'password' => 'required',
    ]);

    if (!$errors) {
        global $pdo;

        $username = $_POST['username'];
        $issuedPassword = $_POST['password'];

        $statement = $pdo->prepare('SELECT * FROM  users WHERE username = ?');
        $statement->bindParam(1, $username, PDO::PARAM_STR );
        $statement->execute();

        $user = $statement->fetch();

        $loginError = "";

        if (login($user, $issuedPassword, $loginError)) {
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            die();
        }
    }
}
?>

<?php renderView('header', ['title' => 'Login']) ?>

<article id="login">
    <header>
        <h1>CAMAGRU</h1>
    </header>

    <form action="/login.php" method="POST" id="login-form">
        <div class="custom-field">
            <input type="text" name="username" id="username" required/>
            <label class="placeholder" for="username">Username</label>
        </div>

        <div class="custom-field">
            <input type="password" name="password" id="password" required/>
            <label class="placeholder" for="password">Password</label>
        </div>

        <button class="custom-button" type="submit">Login</button>
    </form>

    <?php if (!empty($loginError)):?>
        <p><?php echo $loginError ?></p>
    <?php endif;?>

    <aside>
        <p id="register-text">Doesn't have an account ? <a href="register.php">Register now</a> !</p>
    </aside>
</article>

<?php renderView('footer');