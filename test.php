<?php
// YunoAcm3K2zL
 
$dsn = "mysql:host=test_mysql;dbname=test";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, 'root', 'mypassword', $options);
     
     
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$action = $_GET['action'];
if ($action == 'login') {
    handleLogin($pdo);
    return;
}
if ($action == 'register') {
    handleRegister($pdo);
    return 1;
}
if ($action == 'show_user_by_school') {
    handleShowUserBySchool($pdo);
    return;
}
if ($action == 'login_hours') {
    handleLoginHours($pdo);
    echo 'true';
    return;
}

if ($action == 'show_hours_by_user'){
    handleShowHoursByUser($pdo);
    return;
}

if($action == 'update_account'){
    handleUpdateAccount($pdo);
    return;
}

function handleLogin($pdo)
{
    $statement = $pdo->prepare('select * from user where email = ? and password = ? limit 1');
    $statement->execute([$_POST['email'], $_POST['password']]);
    $result = $statement->fetch();
    if ($result) {
        $statement = $pdo->prepare('select * from user_class uc join class c on uc.class_id = c.id where uc.user_id = ?');
        $statement->execute([$result['id']]);
        $class = $statement->fetch();

        $statement = $pdo->prepare('select s.school_name, sponsor from school s join class c on c.school_id = s.id join user_class uc on uc.class_id = c.id join user u on u.id = uc.user_id where u.id = ?');
        $statement->execute([$result['id']]);
        $schoolinfo = $statement->fetch();
        if ($class){
            echo json_encode(["result" => $result, "class" => $class, "schoolinfo" => $schoolinfo]);
        }
    } else {
        echo "false";
    }
}
 

function handleRegister($pdo)
{
    $statement = $pdo->prepare('insert into user values(null, ? , ?, ?, ?)');
    $statement->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password']]);
}

function handleShowUserBySchool($pdo){
    $statement = $pdo->prepare('select first_name, last_name, email from user u join user_class uc on u.id = uc.user_id join class c on uc.class_id = c.id where c.school_id = ?');
    $statement->execute([$_POST['school_id']]);
    $result = $statement->fetchAll();
    if ($result) {
        echo json_encode($result);
    } else { 
        echo 'false';
    }
}

function handleLoginHours($pdo)
{
    $statement = $pdo->prepare('insert into hour values(null, ? , ?, ?, ?, ?)');
    $statement->execute([$_POST['user_id'], $_POST['hour'], $_POST['date'], $_POST['description'], $_POST['type']]);
}

function handleShowHoursByUser($pdo)
{
    $statement = $pdo->prepare('select h.id, h.hour, h.date, description, h.type from hour h join user u on u.id = h.user_id where u.id = ?');
    $statement->execute([$_POST['id']]);
    $result = $statement->fetchAll();
    if ($result) {
        echo json_encode($result);
    } else { 
        echo 'false';
    }
}

function handleUpdateAccount($pdo)
{
    $statement = $pdo->prepare('update user set email = ?, first_name = ?, last_name = ?, user.password = ? where id = ?');
    $statement->execute([$_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['password'], $_POST['id']]);
}

?>