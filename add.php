<?php 

$config = include 'config.php';

$dbh = new PDO($config['connection_string'], $config['user'], $config['password']);

$errors = array();

if(!empty($_POST)) { // sprawdzenie czy formularz został wysłany
    
    if(empty($_POST['title'])) {
        $errors[] = 'title';
    }
    if(empty($_POST['year'])) {
        $errors[] = 'year';
    }
    if(empty($_POST['director'])) {
        $errors[] = 'director';
    }
    if(empty($errors)) {
        $shouldCommit = true;
        $dbh->beginTransaction();
        if($dbh->exec("INSERT INTO `nauka`.`directors` (`name`) VALUES ('" . $_POST['director'] ."')")) {
            $id = $dbh->lastInsertId();
            if($dbh->exec("INSERT INTO `nauka`.`movies` (`title`, `year`, `fk_director`) VALUES ('" . $_POST['title'] ."', '" . $_POST['year'] ."', " . $id . ");")) {
                // OK
            } else {
                $shouldCommit = false;
                print_r($dbh->errorInfo());
            }
        } else {
            $shouldCommit = false;
            print_r($dbh->errorInfo());
        }
        if($shouldCommit) {
            $dbh->commit();
            
        } else {
            $dbh->rollBack();
        }
        header('Location: /nauka/'); die;
    }
} else {
    $_POST = array(
        'title' => '',
        'year' => '',
        'director' => '',
    );
}

?>
<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>
    <body>
        <form action="add.php" method="post">
            <div class="form-group <?php echo in_array('title', $errors)?'has-error':''; ?>">
                <label for="exampleInputEmail1">tytuł</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $_POST['title'] ?>"> 
            </div>
            <div class="form-group <?php echo in_array('year', $errors)?'has-error':''; ?>">
                <label for="exampleInputPassword1">Rok</label>
                <input type="text" class="form-control" id="year" name="year" value="<?php echo $_POST['year'] ?>">
            </div>
            <div class="form-group <?php echo in_array('director', $errors)?'has-error':''; ?>">
                <label for="exampleInputFile">Reżyser</label>
                <input type="text" class="form-control" id="director" name="director" value="<?php echo $_POST['director'] ?>">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </body>
</html>
