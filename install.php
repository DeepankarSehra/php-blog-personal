<?php
// Get the pdo dsn string
require_once "lib/common.php";
require_once "lib/install.php";

session_start();

if($_POST){
    $pdo = getPDO();
    list($rowCounts, $error) = installBlog($pdo);

    $password = '';
    if(!$error){
        $username = 'admin';
        list($password, $error) = createUser($pdo, $username);
    }

    $_SESSION['count'] = $rowCounts;
    $_SESSION['error'] = $error;
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    $_SESSION['try-install'] = true;

    //  here we redirect from post to get

    redirectAndExit('install.php');
}

// check installation
$attempted = false;
if(isset($_SESSION['try-install'])){
    $attempted = true;
    $count = $_SESSION['count'];
    $error = $_SESSION['error'];
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];

    // unset variables so we only report errors only once
    unset($_SESSION['count']);
    unset( $_SESSION['error']);
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['try-install']);
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Blog installer</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
            .box {
                border: 1px dotted silver;
                border-radius: 5px;
                padding: 4px;
            }
            .error {
                background-color: #ff6666;
            }
            .success {
                background-color: #88ff88;
            }
        </style>
    </head>
    <body>
        <?php if($attempted): ?>
            <?php if ($error): ?>
                <div class="error box">
                    <?php echo $error ?>
                </div>
            <?php else: ?>
                <div class="success box">
                    The database and demo data was created OK.
                    
                    <?php ?>
                    <?php foreach (array('post', 'comment') as $tableName): ?>
                        <?php if (isset($count[$tableName])): ?>
                            <?php // Prints the count ?>
                            <?php echo $count[$tableName] ?> new
                            <?php // Prints the name of the thing ?>
                            <?php echo $tableName ?>s
                            were created.
                        <?php endif ?>
                    <?php endforeach ?>

                    <?php ?>
                    The new '<?php echo htmlEscape($username)?>' password is
                    <span style="font-size: 1.2em;"><?php echo htmlEscape($password) ?></span>(copy it to clipboard if you wish)."></span>
                </div>
                
                <p>
                    <a href="index.php">View the Blog</a>
                    or <a href="install.php">Install again</a>
                </p>

            <?php endif ?>

        <?php else: ?>
            <p> Click the install button to reset the database.</p>
            <form method="post">
                <input name="install", type="submit", value="Install"/>
            </form>
        <?php endif ?>
    </body>
</html>
