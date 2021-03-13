<?php
// YunoAcm3K2zL
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    if (file_exists('./wp-config.php')) {
        $dsn = "mysql:host=localhost;dbname=test";
        $pdo = new PDO($dsn, 'root', 'YunoAcm3K2zL', $options);
    } else {
        $dsn = "mysql:host=test_mysql;dbname=test";
        $pdo = new PDO($dsn, 'root', 'mypassword', $options);
    } 
     
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


function getAllSchools($pdo)
{
    $statement = $pdo->prepare('select * from school');
    $statement->execute();
    return $statement->fetchAll();
}

function getAllClasses($pdo)
{
    $statement = $pdo->prepare('select c.*, s.school_name from class c join school s on c.school_id = s.id');
    $statement->execute();
    return $statement->fetchAll();
}

function getAllUsers($pdo)
{
    $statement = $pdo->prepare('select u.*, c.name, uc.class_id, s.school_name from user u left join user_class uc on u.id = uc.user_id join class c on uc.class_id = c.id join school s on c.school_id = s.id order by u.id');
    $statement->execute();
    return $statement->fetchAll();
}
 
if (!empty($_POST)) {
    if ($_POST['type'] == 'school' && $_POST['name']) {
        $statement = $pdo->prepare('insert into school values(null, ?, ?)');
        $statement->execute([$_POST['name'], $_POST['sponsor']]);
    }
    if ($_POST['type'] == 'class' && $_POST['name']) {
        $statement = $pdo->prepare('insert into class values(null, ?, ?, ?, ?, ?, ?)');
        $statement->execute([$_POST['name'], $_POST['level'], $_POST['time'], $_POST['foreign_time'], $_POST['school_id'], $_POST['day']]);
    }
    if ($_POST['type'] == 'assign-class' && $_POST['user_id']) {
        $statement = $pdo->prepare('insert into user_class values(null, ?, ?)');
        $statement->execute([$_POST['user_id'], $_POST['class_id']]);
    }
}
if (!empty($_GET)) {
    if ($_GET['action'] == 'remove_class' && $_GET['user_id'] && $_GET['class_id']) {
        $statement = $pdo->prepare('delete from user_class where user_id = ? and class_id = ?');
        $statement->execute([$_GET['user_id'], $_GET['class_id']]);
    }
}
$schools = getAllSchools($pdo);
$classes = getAllClasses($pdo);
$users = getAllUsers($pdo);

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container">
        <div class="card">
            <div class="card-header">Schools 
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add
            </button>
            </div>
            <div class="card-body"> 
            <div class="row">
                    <div class="col-sm">ID</div>
                    <div class="col-sm"> Name</div>
                    <div class="col-sm"> Sponsor </div>
                </div>
            <?php foreach ($schools as $school): ?>
                <div class="row">
                    <div class="col-sm">
                        <?= $school['id'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $school['school_name'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $school['sponsor'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Classes<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#class-modal">
                Add
            </button>
            </div>
            <div class="card-body">
            <div class="row">
                    <div class="col-sm">ID</div>
                    <div class="col-sm">Name</div>
                    <div class="col-sm">  Level </div>
                    <div class="col-sm">  Local Time </div>
                    <div class="col-sm">  Foreign Time </div>
                    <div class="col-sm"> School </div>
                    <div class="col-sm">   Day </div>
                    <div class="col-sm">    </div>
                </div>
            <?php foreach ($classes as $class): ?>
                <div class="row">
                    <div class="col-sm">
                        <?= $class['id'] ?>
                    </div>
                    <div class="col-sm class-name">
                        <?= $class['name'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $class['level'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $class['time'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $class['foreign_time'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $class['school_name'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $class['day_of_the_week'] ?>
                    </div>
                    <div class="col-sm">
                        <button class="btn btn-sm btn-success assign" data-bs-toggle="modal" data-bs-target="#assign-modal" id="<?= $class['id'] ?>">Assign User</button>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <div class="card">
            
        </div>
    
    <div class="card">
            <div class="card-header">Users
            </div>
            <div class="card-body">
            <div class="row">
                    <div class="col-sm">User ID</div>
                    <div class="col-sm">  Name </div>
                    <div class="col-sm">  Email </div>
                    <div class="col-sm"> Password  </div>
                    <div class="col-sm"> Class  </div>
                    <div class="col-sm">School</div>
	            <div class="col-sm"></div> 
               </div>
            <?php foreach ($users as $user): ?>
                <div class="row">
                    <div class="col-sm">
                        <?= $user['id'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $user['first_name'] . ' ' . $user['last_name'] ?>
                    </div>
                    
                    <div class="col-sm">
                        <?= $user['email'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $user['password'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $user['name'] ?>
                    </div>
                    <div class="col-sm">
                        <?= $user['school_name'] ?>
                    </div>
                   <div class="col-sm">
                        <a href="manage.php?action=remove_class&user_id=<?= $user['id'] ?>&class_id=<?= $user['class_id'] ?>" onclick="return confirm('Are you sure you want to remove this class?')">Remove Class</a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
         
    </div>
     

<!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add School</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="" method ="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name">
                <input type="hidden" name='type' value='school'>
                
            </div>
            <div class="mb-3">
                <label for="sponsor" class="form-label">Sponsor</label>
                <input type="text" class="form-control" id="name" name="sponsor">
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        
        </div>
    </div>
    </div>
    <div class="modal fade" id="class-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Class</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="" method ="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name">
                <input type="hidden" name='type' value='class'>
                
            </div>
            <div class="mb-3">
                <label for="level" class="form-label">level</label>
                <input type="text" class="form-control" id="level" name="level">
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Local Time</label>
                <input type="text" class="form-control" id="time" name="time">
            </div>
            <div class="mb-3">
                <label for="foreign_time" class="form-label">Foreign Time</label>
                <input type="text" class="form-control" id="foreign_time" name="foreign_time">
            </div>
            <div class="mb-3">
                <label for="school_id" class="form-label">School ID</label>
                <input type="text" class="form-control" id="school_id" name="school_id">
            </div>
            <div class="mb-3">
                <label for="day" class="form-label">Day</label>
                <input type="text" class="form-control" id="day" name="day">
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        
        </div>
    </div>
    </div>
    <div class="modal fade" id="assign-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Assign User to Class</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="" method ="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Class</label>
                <input type="text" class="form-control" id="name" readonly>
                <input type="hidden" name='type' value='assign-class'>
                <input type="hidden" name='class_id' id="class_id">
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">User ID</label>
                <input type="text" class="form-control" id="user_id" name="user_id">
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        
        </div>
    </div>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script>
    $(function(){
        $('.assign').click(function(e){
            e.preventDefault();
            $('#assign-modal #name').val($(this).parent().parent().find('.class-name').html());
            
            $('#assign-modal #class_id').val($(this).attr('id'));
            $('#assign-modal').toggle();
        });
    })
    </script>
    
  </body>
</html>
