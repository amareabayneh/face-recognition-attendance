<?php

header('Content-Type: application/json');
// header('Content-Type: text/HTML');
session_start();
require_once(__DIR__ . "/db.php");
require_once("log_helper.php");


/*
|------------------------------------
| DEFAULT ADMIN INFORMATION
|------------------------------------
*/

$username = "admin1";
$email = "amare0986@gmail.com";
$phone = "0911000000";

$plainPassword = "a";

/*
|-------------------------
| HASH PASSWORD
|-------------------------
*/

$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

/*
|--------------------------
| GET ADMIN ROLE ID
|--------------------------
*/

$roleQuery = mysqli_query($conn,
"SELECT id FROM roles WHERE role_name='admin'");

$role = mysqli_fetch_assoc($roleQuery);

$role_id = $role['id'];

/*
|----------------------------------
| CHECK IF ADMIN ALREADY EXISTS
|----------------------------------
*/

$checkAdmin = mysqli_query($conn,
"SELECT * FROM users WHERE email='amare0986@gmail.com'");

if(mysqli_num_rows($checkAdmin) > 0){

    // echo "Admin already exists.";

}else{

    /*
    |---------------------------
    | INSERT ADMIN USER
    |---------------------------
    */

    $insertAdmin = "
    INSERT INTO users(
        role_id,
        username,
        email,
        phone,
        password,
        is_active
    )
    VALUES(
        '$role_id',
        '$username',
        '$email',
        '$phone',
        '$hashedPassword',
        1
    )
    ";

    if(mysqli_query($conn, $insertAdmin)){

        // echo "<h2>Default Admin Created Successfully</h2>";

        // echo "<br>";
        // echo "<strong>Username:</strong> admin";
        // echo "<br>";
        // echo "<strong>Password:</strong> admin123";

    }else{

        // echo "Error: " . mysqli_error($conn);

    }
}








$action = $_GET['action'] ?? $_POST['action'];


/*
|--------------------
| LOGIN
|--------------------
*/

if($action == "login"){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $errors = [];

    /*
    |------------------
    | VALIDATION
    |------------------
    */

    if(empty($email)){

        $errors['email_error'] = "Email is required";

    }

    if(empty($password)){

        $errors['password_error'] = "Password is required";

    }

    if(!empty($errors)){

        echo json_encode($errors);
        exit;

    }

    /*
    |------------------
    | CHECK USER
    |------------------
    */

    $query = "
    SELECT users.*, roles.role_name
    FROM users
    INNER JOIN roles
    ON users.role_id = roles.id
    WHERE users.email = '$email' 
    AND users.is_active = 1
    LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        /*
        |-------------------------
        | VERIFY PASSWORD
        |-------------------------
        */

        if(password_verify($password, $user['password'])){

            createSystemLog(
                $conn,
                $user['id'],
                "LOGIN_SUCCESS",
                "User logged in successfully"

            );

            /*
            |----------------------
            | SAVE SESSION
            |----------------------
            */

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];


            /*
            |---------------------------
            | UPDATE LAST LOGIN
            |---------------------------
            */

            mysqli_query($conn,
            "UPDATE users
             SET last_login = NOW()
             WHERE id = '".$user['id']."'");

            /*
            |-------------------------------
            | REDIRECT BASED ON ROLE
            |-------------------------------
            */

            $redirect = "";

            if($user['role_name'] == "admin"){

                $redirect = "../users/admin/dashboard.php";

            }elseif($user['role_name'] == "instructor"){

                $redirect = "../users/instructor/dashboard.php";

            }elseif($user['role_name'] == "student"){

                $redirect = "../users/student/dashboard.php";

            }

            echo json_encode([

                "status" => "success",
                "message" => "Login successful",
                "redirect" => $redirect

            ]);

        }else{

            createSystemLog(
                $conn,
                null,
                "LOGIN_FAILED",
                "Failed login attempt for email: ".$email,
                "Warning"
            );

            echo json_encode([

                "status" => "error",
                "password_error" => "Incorrect password"

            ]);

        }

    }else{

        $checkInactive = mysqli_query($conn,
        "SELECT * FROM users
         WHERE email='$email'
         AND is_active = 0");

        if(mysqli_num_rows($checkInactive) > 0){

            echo json_encode([

                "status" => "error",
                "email_error" =>
                "Your account is deactivated"

            ]);

        }else{

            echo json_encode([

                "status" => "error",
                "email_error" => "Email not found"

            ]);

        }
    }
}





/* -------------------------
   READ USERS
--------------------------*/
elseif($action == "read"){

    $query = "
    SELECT users.*, roles.role_name
    FROM users
    INNER JOIN roles ON users.role_id = roles.id
    ORDER BY users.id DESC
    ";

    $result = mysqli_query($conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }

    echo json_encode($data);
}

/* -------------------------
   GET SINGLE USER
--------------------------*/
elseif($action == "single"){

    $id = $_GET['id'];

    $query = "SELECT * FROM users WHERE id='$id'";
    $result = mysqli_query($conn, $query);

    echo json_encode(mysqli_fetch_assoc($result));
}

/* -------------------------
   GET ROLES
--------------------------*/
elseif($action == "roles"){

    $result = mysqli_query($conn, "SELECT * FROM roles");

    $data = [];

    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }

    echo json_encode($data);
}

/* -------------------------
   CREATE USER
--------------------------*/
elseif($action == "create"){

    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];

    $errors = [];

    if(empty($username)) $errors['username_error'] = "Username required";
    if(empty($email)) $errors['email_error'] = "Email required";
    if(empty($password)) $errors['password_error'] = "Password required";


    /* -------------------------
       CHECK DUPLICATE EMAIL
    --------------------------*/

    $checkEmail = mysqli_query($conn,

    "SELECT id FROM users
     WHERE email='$email'");

    if(mysqli_num_rows($checkEmail) > 0){

        $errors['email_error'] =
        "Email already exists";
    }

    /* -------------------------
       CHECK DUPLICATE USERNAME
    --------------------------*/

    $checkUsername = mysqli_query($conn,

    "SELECT id FROM users
     WHERE username='$username'");

    if(mysqli_num_rows($checkUsername) > 0){

        $errors['username_error'] =
        "Username already exists";
    }




    if($errors){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }



    $hashed = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($conn, "
        INSERT INTO users(role_id, username, email, phone, password)
        VALUES('$role_id','$username','$email','$phone','$hashed')
    ");

    echo json_encode(["status"=>"success","message"=>"User created successfully"]);
}

/* -------------------------
   UPDATE USER
--------------------------*/
elseif($action == "update"){

    $id = trim($_POST['id']);

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role_id = trim($_POST['role_id']);

    $errors = [];

    /* -------------------------
       VALIDATION
    --------------------------*/

    if(empty($username)){

        $errors['username_error'] =
        "Username is required";
    }

    if(empty($email)){

        $errors['email_error'] =
        "Email is required";
    }

    /* -------------------------
       CHECK DUPLICATE EMAIL
    --------------------------*/

    $checkEmail = mysqli_query($conn,

    "SELECT id FROM users
     WHERE email='$email'
     AND id != '$id'");

    if(mysqli_num_rows($checkEmail) > 0){

        $errors['email_error'] =
        "Email already exists";
    }

    /* -------------------------
       CHECK DUPLICATE USERNAME
    --------------------------*/

    $checkUsername = mysqli_query($conn,

    "SELECT id FROM users
     WHERE username='$username'
     AND id != '$id'");

    if(mysqli_num_rows($checkUsername) > 0){

        $errors['username_error'] =
        "Username already exists";
    }

    /* -------------------------
       RETURN ERRORS
    --------------------------*/

    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    /* -------------------------
       UPDATE USER
    --------------------------*/

    $update = mysqli_query($conn,

    "UPDATE users SET

        username='$username',
        email='$email',
        phone='$phone',
        role_id='$role_id'

     WHERE id='$id'");

    if($update){

        echo json_encode([

            "status" => "success",
            "message" => "User updated successfully"

        ]);

    }else{

        echo json_encode([

            "status" => "error",
            "message" => mysqli_error($conn)

        ]);

    }

}

/* -------------------------
   DELETE USER
--------------------------*/
elseif($action == "delete"){

    $id = intval($_GET['id']);

    /*
    --------------------------------
    PREVENT ADMIN SELF DELETE
    --------------------------------
    */
    if($id == $_SESSION['user_id']){

        echo json_encode([
            "status" => "error",
            "message" => "You cannot delete your own account"
        ]);

        exit;
    }

    /*
    --------------------------------
    CHECK USER EXISTS
    --------------------------------
    */
    $checkUser = mysqli_query($conn,

    "SELECT * FROM users
     WHERE id='$id'");

    if(mysqli_num_rows($checkUser) == 0){

        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);

        exit;
    }

    $user = mysqli_fetch_assoc($checkUser);

    mysqli_begin_transaction($conn);

    try{

        /*
        --------------------------------
        DELETE STUDENT RELATED DATA
        --------------------------------
        */
        $studentQuery = mysqli_query($conn,

        "SELECT id FROM students
         WHERE user_id='$id'");

        if(mysqli_num_rows($studentQuery) > 0){

            $student =
            mysqli_fetch_assoc($studentQuery);

            $student_id =
            $student['id'];

            /*
            DELETE FACE DATA
            */
            mysqli_query($conn,

            "DELETE FROM face_data
             WHERE student_id='$student_id'");

            /*
            DELETE ATTENDANCE
            */
            mysqli_query($conn,

            "DELETE FROM attendance
             WHERE student_id='$student_id'");




            /*
            DELETE ENROLLMENTS
            */
            mysqli_query($conn,

            "DELETE FROM enrollments
             WHERE student_id='$student_id'");

            /*
            DELETE STUDENT
            */
            mysqli_query($conn,

            "DELETE FROM students
             WHERE user_id='$id'");
        }

        /*
        --------------------------------
        DELETE INSTRUCTOR RELATED DATA
        --------------------------------
        */
        $instructorQuery = mysqli_query($conn,

        "SELECT id FROM instructors
         WHERE user_id='$id'");

        if(mysqli_num_rows($instructorQuery) > 0){

            $instructor =
            mysqli_fetch_assoc($instructorQuery);

            $instructor_id =
            $instructor['id'];

            /*
            DELETE CLASS SUBJECTS
            */
            mysqli_query($conn,

            "DELETE FROM class_subjects
             WHERE instructor_id='$instructor_id'");

            /*
            DELETE INSTRUCTOR
            */
            mysqli_query($conn,

            "DELETE FROM instructors
             WHERE user_id='$id'");
        }

        /*
        --------------------------------
        DELETE USER
        --------------------------------
        */
        mysqli_query($conn,

        "DELETE FROM users
         WHERE id='$id'");

        mysqli_commit($conn);

        echo json_encode([
            "status"=>"success",
            "message"=>"User deleted successfully"
        ]);

    }catch(Exception $e){

        mysqli_rollback($conn);

        echo json_encode([
            "status"=>"error",
            "message"=>$e->getMessage()
        ]);
    }

}



/* -------------------------
   CHANGE PASSWORD
--------------------------*/
elseif($action == "change_password"){

    $id = $_POST['id'];

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    mysqli_query($conn,
    "UPDATE users
     SET password='$password'
     WHERE id='$id'");

    echo json_encode([
        "status"=>"success",
        "message"=>"Password changed successfully"
    ]);
}


/* -------------------------
   TOGGLE STATUS
--------------------------*/
elseif($action == "toggle_status"){

    $id = $_GET['id'];
    $status = $_GET['status'];

    mysqli_query($conn,
    "UPDATE users
     SET is_active='$status'
     WHERE id='$id'");

    $message =
    $status == 1
    ? "User activated successfully"
    : "User deactivated successfully";

    echo json_encode([
        "status"=>"success",
        "message"=>$message
    ]);
}








/* -------------------------
   READ DEPARTMENTS
--------------------------*/
elseif($action == "read_departments"){

    $query =
    "SELECT * FROM departments
     ORDER BY id DESC";

    $result = mysqli_query($conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);
}

/* -------------------------
   SINGLE DEPARTMENT
--------------------------*/
elseif($action == "single_department"){

    $id = $_GET['id'];

    $query =
    "SELECT * FROM departments
     WHERE id='$id'";

    $result = mysqli_query($conn, $query);

    echo json_encode(
        mysqli_fetch_assoc($result)
    );
}

/* -------------------------
   CREATE DEPARTMENT
--------------------------*/
elseif($action == "create_department"){

    $department_name =
    trim($_POST['department_name']);

    $department_code =
    trim($_POST['department_code']);

    $errors = [];

    if(empty($department_name)){

        $errors['department_name_error'] =
        "Department name is required";
    }

    $checkName = mysqli_query($conn,

    "SELECT id FROM departments
     WHERE department_name='$department_name'");

    if(mysqli_num_rows($checkName) > 0){

        $errors['department_name_error'] =
        "Department already exists";
    }

    if(!empty($department_code)){

        $checkCode = mysqli_query($conn,

        "SELECT id FROM departments
         WHERE department_code='$department_code'");

        if(mysqli_num_rows($checkCode) > 0){

            $errors['department_code_error'] =
            "Department code already exists";
        }
    }

    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    mysqli_query($conn,

    "INSERT INTO departments(

        department_name,
        department_code

    )

    VALUES(

        '$department_name',
        '$department_code'

    )");

    echo json_encode([

        "status"=>"success",
        "message"=>"Department created successfully"

    ]);
}

/* -------------------------
   UPDATE DEPARTMENT
--------------------------*/
elseif($action == "update_department"){

    $id = $_POST['id'];

    $department_name =
    trim($_POST['department_name']);

    $department_code =
    trim($_POST['department_code']);

    $errors = [];

    if(empty($department_name)){

        $errors['department_name_error'] =
        "Department name is required";
    }

    $checkName = mysqli_query($conn,

    "SELECT id FROM departments

     WHERE department_name='$department_name'

     AND id != '$id'");

    if(mysqli_num_rows($checkName) > 0){

        $errors['department_name_error'] =
        "Department already exists";
    }

    if(!empty($department_code)){

        $checkCode = mysqli_query($conn,

        "SELECT id FROM departments

         WHERE department_code='$department_code'

         AND id != '$id'");

        if(mysqli_num_rows($checkCode) > 0){

            $errors['department_code_error'] =
            "Department code already exists";
        }
    }

    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    mysqli_query($conn,

    "UPDATE departments SET

        department_name='$department_name',
        department_code='$department_code'

     WHERE id='$id'
    ");

    echo json_encode([

        "status"=>"success",
        "message"=>"Department updated successfully"

    ]);
}

/* -------------------------
   DELETE DEPARTMENT
--------------------------*/
elseif($action == "delete_department"){

    $id = $_GET['id'];

    mysqli_query($conn,

    "DELETE FROM departments
     WHERE id='$id'");

    echo json_encode([

        "status"=>"success",
        "message"=>"Department deleted successfully"

    ]);
}




/* -------------------------
   READ CLASSES
--------------------------*/
elseif($action == "read_classes"){

    $query = "

    SELECT

    classes.*,
    departments.department_name

    FROM classes

    LEFT JOIN departments
    ON classes.department_id = departments.id

    ORDER BY classes.id DESC

    ";

    $result = mysqli_query($conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);
}

/* -------------------------
   SINGLE CLASS
--------------------------*/
elseif($action == "single_class"){

    $id = $_GET['id'];

    $query =
    "SELECT * FROM classes
     WHERE id='$id'";

    $result = mysqli_query($conn, $query);

    echo json_encode(
        mysqli_fetch_assoc($result)
    );
}

/* -------------------------
   CREATE CLASS
--------------------------*/
elseif($action == "create_class"){

    $department_id =
    trim($_POST['department_id']);

    $class_name =
    trim($_POST['class_name']);

    $section =
    trim($_POST['section']);

    $academic_year =
    trim($_POST['academic_year']);

    $errors = [];

    if(empty($department_id)){

        $errors['department_id_error'] =
        "Department is required";
    }

    if(empty($class_name)){

        $errors['class_name_error'] =
        "Class name is required";
    }

    $check = mysqli_query($conn,

    "SELECT id FROM classes

     WHERE department_id='$department_id'

     AND class_name='$class_name'

     AND section='$section'

     AND academic_year='$academic_year'
    ");

    if(mysqli_num_rows($check) > 0){

        $errors['class_name_error'] =
        "Class already exists";
    }

    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    mysqli_query($conn,

    "INSERT INTO classes(

        department_id,
        class_name,
        section,
        academic_year

    )

    VALUES(

        '$department_id',
        '$class_name',
        '$section',
        '$academic_year'

    )");

    echo json_encode([

        "status"=>"success",
        "message"=>"Class created successfully"

    ]);
}

/* -------------------------
   UPDATE CLASS
--------------------------*/
elseif($action == "update_class"){

    $id = $_POST['id'];

    $department_id =
    trim($_POST['department_id']);

    $class_name =
    trim($_POST['class_name']);

    $section =
    trim($_POST['section']);

    $academic_year =
    trim($_POST['academic_year']);

    $errors = [];

    if(empty($department_id)){

        $errors['department_id_error'] =
        "Department is required";
    }

    if(empty($class_name)){

        $errors['class_name_error'] =
        "Class name is required";
    }

    $check = mysqli_query($conn,

    "SELECT id FROM classes

     WHERE department_id='$department_id'

     AND class_name='$class_name'

     AND section='$section'

     AND academic_year='$academic_year'

     AND id != '$id'
    ");

    if(mysqli_num_rows($check) > 0){

        $errors['class_name_error'] =
        "Class already exists";
    }

    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    mysqli_query($conn,

    "UPDATE classes SET

        department_id='$department_id',
        class_name='$class_name',
        section='$section',
        academic_year='$academic_year'

     WHERE id='$id'
    ");

    echo json_encode([

        "status"=>"success",
        "message"=>"Class updated successfully"

    ]);
}

/* -------------------------
   DELETE CLASS
--------------------------*/
elseif($action == "delete_class"){

    $id = $_GET['id'];

    mysqli_query($conn,

    "DELETE FROM classes
     WHERE id='$id'");

    echo json_encode([

        "status"=>"success",
        "message"=>"Class deleted successfully"

    ]);
}







/* -------------------------
   READ STUDENTS
--------------------------*/
elseif($action == "read_students"){

    $query = "

    SELECT

    students.*,
    departments.department_name,
    classes.class_name

    FROM students

    LEFT JOIN departments
    ON students.department_id = departments.id

    LEFT JOIN classes
    ON students.class_id = classes.id

    ORDER BY students.id DESC

    ";

    $result = mysqli_query($conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);
}

/* -------------------------
   SINGLE STUDENT
--------------------------*/
elseif($action == "single_student"){

    $id = $_GET['id'];

    $query =
    "SELECT * FROM students WHERE id='$id'";

    $result = mysqli_query($conn, $query);

    echo json_encode(
        mysqli_fetch_assoc($result)
    );
}

/* -------------------------
   LOAD DEPARTMENTS
--------------------------*/
elseif($action == "departments"){

    $result =
    mysqli_query($conn,
    "SELECT * FROM departments");

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);
}

/* -------------------------
   LOAD CLASSES
--------------------------*/
elseif($action == "classes"){

    $result =
    mysqli_query($conn,
    "SELECT * FROM classes");

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);
}



/* -------------------------
   CREATE STUDENT
--------------------------*/
elseif($action == "create_student"){

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = trim($_POST['gender']);
    $date_of_birth = $_POST['date_of_birth'];
    $address = trim($_POST['address']);
    $department_id = $_POST['department_id'];
    $class_id = $_POST['class_id'];
    $enrollment_year = $_POST['enrollment_year'];
    $status = $_POST['status'];

    if(empty($class_id)){

        echo json_encode([
            "status"=>"error",
            "message"=>"Please select Class"
        ]);
        exit;
    }
    /*
    |-----------------------------------
    | GET DEPARTMENT CODE
    |-----------------------------------
    */

    if(empty($department_id)){

        echo json_encode([
            "status"=>"error",
            "message"=>"Please select department"
        ]);
        exit;
    }

    $dept_query = mysqli_query($conn,
    "SELECT department_code
     FROM departments
     WHERE id='$department_id'");

    $dept_data = mysqli_fetch_assoc($dept_query);

    $dept_code = $dept_data['department_code'];

    /*
    |-----------------------------------
    | GENERATE STUDENT CODE
    |-----------------------------------
    */

    $prefix = $enrollment_year . "-" . $dept_code;

    $result = mysqli_query($conn,
    "SELECT student_code
     FROM students
     WHERE student_code LIKE '$prefix%'
     ORDER BY id DESC
     LIMIT 1");

    $last_number = 0;

    if(mysqli_num_rows($result) > 0){

        $row = mysqli_fetch_assoc($result);

        $parts = explode("-", $row['student_code']);

        $last_number = intval(end($parts));
    }

    $new_number = str_pad(
        $last_number + 1,
        4,
        "0",
        STR_PAD_LEFT
    );

    $student_code = $prefix . "-" . $new_number;

    /*
    |-----------------------------------
    | USER ACCOUNT DETAILS
    |-----------------------------------
    */

    $username = $student_code;

    $email = strtolower($student_code) . "@gmail.com";

    $plain_password = $dept_code;

    $password = password_hash(
        $plain_password,
        PASSWORD_DEFAULT
    );

    /*
    |-----------------------------------
    | VALIDATION
    |-----------------------------------
    */

    $errors = [];

    $check = mysqli_query($conn,
    "SELECT id
     FROM students
     WHERE student_code='$student_code'");

    if(mysqli_num_rows($check) > 0){

        $errors['student_code_error'] =
        "Student code already exists";
    }

    $checkUser = mysqli_query($conn,
    "SELECT id
     FROM users
     WHERE username='$username'
     OR email='$email'");

    if(mysqli_num_rows($checkUser) > 0){

        $errors['user_error'] =
        "User account already exists";
    }

    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    /*
    |-----------------------------------
    | PHOTO UPLOAD
    |-----------------------------------
    */

    $photoName = "";

    if(isset($_FILES['photo']) &&
       $_FILES['photo']['name'] != ""){

        $photoName =
        time() . "_" .
        $_FILES['photo']['name'];

        move_uploaded_file(

            $_FILES['photo']['tmp_name'],

            "../uploads/students/" .
            $photoName
        );
    }

    /*
    |-----------------------------------
    | GET STUDENT ROLE ID
    |-----------------------------------
    */

    $roleQuery = mysqli_query($conn,
        "SELECT id
         FROM roles
         WHERE role_name='student'");

    $roleData = mysqli_fetch_assoc($roleQuery);

    $role_id = $roleData['id'];

    /*
    |-----------------------------------
    | CREATE USER ACCOUNT
    |-----------------------------------
    */


    mysqli_query($conn,

    "INSERT INTO users(

        role_id,

        first_name,
        last_name,
        gender,
        date_of_birth,
        address,

        username,
        email,

        password,
        profile_photo,
        is_active

    )

    VALUES(

        '$role_id',

        '$first_name',
        '$last_name',
        '$gender',
        '$date_of_birth',
        '$address',

        '$username',
        '$email',
        '$password',
        '$photoName',
        '1'

    )");

    $user_id = mysqli_insert_id($conn);

    /*
    |-----------------------------------
    | CREATE STUDENT
    |-----------------------------------
    */

    mysqli_query($conn,

    "INSERT INTO students(

        user_id,
        student_code,
        first_name,
        last_name,
        gender,
        date_of_birth,
        address,
        photo,
        department_id,
        class_id,
        enrollment_year,
        status

    )

    VALUES(

        '$user_id',
        '$student_code',
        '$first_name',
        '$last_name',
        '$gender',
        '$date_of_birth',
        '$address',
        '$photoName',
        '$department_id',
        '$class_id',
        '$enrollment_year',
        '$status'

    )");

    createSystemLog(
        $conn,
        $_SESSION['user_id'],
        "CREATE_STUDENT",
        "Created student: ".$student_code

    );


    echo json_encode([

        "status"=>"success",
        "message"=>"Student created successfully"

    ]);
}



/* -------------------------
   UPDATE STUDENT
--------------------------*/
elseif($action == "update_student"){

    $id = $_POST['id'];

    // $student_code = trim($_POST['student_code']);

    $first_name =
    trim($_POST['first_name']);

    $last_name =
    trim($_POST['last_name']);

    $gender =
    trim($_POST['gender']);

    $date_of_birth =
    $_POST['date_of_birth'];

    $address =
    trim($_POST['address']);

    $department_id =
    $_POST['department_id'];

    $class_id =
    $_POST['class_id'];

    $enrollment_year =
    $_POST['enrollment_year'];

    $status =
    $_POST['status'];

    $errors = [];


    if(!empty($errors)){

        $errors['status'] = "error";

        echo json_encode($errors);

        exit;
    }

    $photoUpdate = "";

    if(isset($_FILES['photo']) &&
       $_FILES['photo']['name'] != ""){

        $photoName =
        time() . "_" .
        $_FILES['photo']['name'];

        move_uploaded_file(

            $_FILES['photo']['tmp_name'],

            "../uploads/students/" .
            $photoName

        );

        $photoUpdate =
        ", photo='$photoName'";
    }

    mysqli_query($conn,

    "UPDATE students SET

        first_name='$first_name',
        last_name='$last_name',
        gender='$gender',
        date_of_birth='$date_of_birth',
        address='$address',
        department_id='$department_id',
        class_id='$class_id',
        enrollment_year='$enrollment_year',
        status='$status'

        $photoUpdate

     WHERE id='$id'
    ");

    // createSystemLog(
    //     $conn,
    //     $_SESSION['user_id'],
    //     "UPDATE_STUDENT",
    //     "Updated student: ".$first_name
    // );

    echo json_encode([

        "status"=>"success",
        "message"=>"Student updated successfully"

    ]);

}





/* -------------------------
   DELETE STUDENT
--------------------------*/
elseif($action == "delete_student"){

    $id = $_GET['id'];

    createSystemLog(
        $conn,
        $_SESSION['user_id'],
        "DELETE_STUDENT",
        "Deleted student ID: ".$id,
        "Critical"
    );

    mysqli_query($conn,
    "DELETE FROM students WHERE id='$id'");

    echo json_encode([

        "status"=>"success",
        "message"=>"Student deleted successfully"

    ]);
}







elseif($action == "read_instructors"){
    $query = "
    SELECT instructors.*,
    users.email,
    departments.department_name
    FROM instructors
    LEFT JOIN users ON instructors.user_id = users.id
    LEFT JOIN departments ON instructors.department_id = departments.id
    ORDER BY instructors.id DESC
    ";

    $res = mysqli_query($conn,$query);

    $data=[];

    while($row=mysqli_fetch_assoc($res)){
    $data[]=$row;
    }

    echo json_encode($data);
}


// SINGLE
elseif($action == "single_instructor"){

    $id = $_GET['id'];

    $res = mysqli_query($conn,
    "SELECT * FROM instructors WHERE id='$id'");

    echo json_encode(mysqli_fetch_assoc($res));
}



/* --------------------------------
   CREATE INSTRUCTOR
---------------------------------*/
elseif($action == "create_instructor"){

    $first_name =
    trim($_POST['first_name']);

    $last_name =
    trim($_POST['last_name']);

    $code =
    trim($_POST['instructor_code']);

    $phone =
    trim($_POST['phone']);

    $department_id =
    $_POST['department_id'];

    $specialization =
    trim($_POST['specialization']);

    /*
    |-----------------------------------
    | GET DEPARTMENT CODE
    |-----------------------------------
    */
    if(empty($department_id)){

        echo json_encode([
            "status"=>"error",
            "message"=>"Please select department"
        ]);
        exit;
    }

    $deptQuery = mysqli_query($conn,
    "SELECT department_code
     FROM departments
     WHERE id='$department_id'");

    $deptData = mysqli_fetch_assoc($deptQuery);

    if(!$deptData){
        echo json_encode([
            "status"=>"error",
            "message"=>"Invalid department selected"
        ]);

        exit;
    }
    $dept_code = $deptData['department_code'];



    /*
    |-----------------------------------
    | USER ACCOUNT DETAILS
    |-----------------------------------
    */

    $username = $code;

    $email = strtolower($code) . "@gmail.com";

    $plain_password = $dept_code;

    $password = password_hash(
        $plain_password,
        PASSWORD_DEFAULT
    );

    /*
    |-----------------------------------
    | VALIDATION
    |-----------------------------------
    */

    $errors = [];

    if(empty($code)){

        $errors["message"] =
        "Instructor code required";
    }

    $checkInstructor = mysqli_query($conn,
    "SELECT id
     FROM instructors
     WHERE instructor_code='$code'");

    if(mysqli_num_rows($checkInstructor) > 0){

        $errors["message"] =
        "Instructor code already exists";
    }

    $checkUser = mysqli_query($conn,
    "SELECT id
     FROM users
     WHERE username='$username'
     OR email='$email'");

    if(mysqli_num_rows($checkUser) > 0){

        $errors["message"] =
        "User account already exists";
    }

    if(!empty($errors)){

        echo json_encode([
            "status"=>"error",
            "message"=>$errors["message"]
        ]);

        exit;
    }

    /*
    |-----------------------------------
    | GET INSTRUCTOR ROLE ID
    |-----------------------------------
    */

    $role = mysqli_fetch_assoc(

        mysqli_query($conn,

        "SELECT id
         FROM roles
         WHERE role_name='instructor'")
    );

    /*
    |-----------------------------------
    | PHOTO UPLOAD
    |-----------------------------------
    */

    $photo = "";

    if(isset($_FILES['photo']) &&
       $_FILES['photo']['name'] != ""){

        $photo =
        time() .
        $_FILES['photo']['name'];

        move_uploaded_file(

            $_FILES['photo']['tmp_name'],

            "../uploads/instructors/" .
            $photo
        );
    }


    /*
    |-----------------------------------
    | CREATE USER ACCOUNT
    |-----------------------------------
    */

    mysqli_query($conn,

    "INSERT INTO users(

        role_id,

        first_name,
        last_name,


        username,
        email,
        phone,

        password,
        profile_photo,
        is_active

    )

    VALUES(

        '".$role['id']."',

        '$first_name',
        '$last_name',

        '$username',
        '$email',
        '$phone',
        
        '$password',
        '$photo',
        '1'

    )");

    $user_id = mysqli_insert_id($conn);


    /*
    |-----------------------------------
    | CREATE INSTRUCTOR
    |-----------------------------------
    */

    mysqli_query($conn,

    "INSERT INTO instructors(

        user_id,
        instructor_code,
        first_name,
        last_name,
        phone,
        department_id,
        specialization,
        photo

    )

    VALUES(

        '$user_id',
        '$code',
        '$first_name',
        '$last_name',
        '$phone',
        '$department_id',
        '$specialization',
        '$photo'

    )");



    createSystemLog(
        $conn,
        $_SESSION['user_id'],
        "CREATE_INSTRUCTOR",
        "Created instructor: ".$code

    );


    echo json_encode([

        "status"=>"success",
        "message"=>"Instructor created successfully"

    ]);
}



/* --------------------------------
   UPDATE INSTRUCTOR
---------------------------------*/
elseif($action == "update_instructor"){

    $id =
    $_POST['id'];

    $first_name =
    trim($_POST['first_name']);

    $last_name =
    trim($_POST['last_name']);

    $code =
    trim($_POST['instructor_code']);

    $phone =
    trim($_POST['phone']);

    $department_id =
    $_POST['department_id'];

    $specialization =
    trim($_POST['specialization']);

    /*
    |-----------------------------------
    | GET INSTRUCTOR DATA
    |-----------------------------------
    */

    $instructorQuery = mysqli_query($conn,
    "SELECT *
     FROM instructors
     WHERE id='$id'");

    $instructorData =
    mysqli_fetch_assoc($instructorQuery);

    $user_id =
    $instructorData['user_id'];

    /*
    |-----------------------------------
    | GET DEPARTMENT CODE
    |-----------------------------------
    */

    $deptQuery = mysqli_query($conn,
    "SELECT department_code
     FROM departments
     WHERE id='$department_id'");

    $deptData = mysqli_fetch_assoc($deptQuery);

    $dept_code = $deptData['department_code'];

    /*
    |-----------------------------------
    | USER ACCOUNT DETAILS
    |-----------------------------------
    */

    $username = $code;

    $email = strtolower($code) . "@gmail.com";

    $plain_password = $dept_code;

    $password = password_hash(
        $plain_password,
        PASSWORD_DEFAULT
    );

    /*
    |-----------------------------------
    | PHOTO UPDATE
    |-----------------------------------
    */

    $photoUpdate = "";
    $userPhotoUpdate = "";

    if(isset($_FILES['photo']) &&
       $_FILES['photo']['name'] != ""){

        $photo =
        time() .
        $_FILES['photo']['name'];

        move_uploaded_file(

            $_FILES['photo']['tmp_name'],

            "../uploads/instructors/" .
            $photo
        );

        $photoUpdate =
        ", photo='$photo'";

        $userPhotoUpdate =
        ", profile_photo='$photo'";
    }

    /*
    |-----------------------------------
    | UPDATE USER ACCOUNT
    |-----------------------------------
    */

    mysqli_query($conn,

    "UPDATE users SET

        username='$username',
        email='$email',
        password='$password'

        $userPhotoUpdate

     WHERE id='$user_id'
    ");

    /*
    |-----------------------------------
    | UPDATE INSTRUCTOR
    |-----------------------------------
    */

    mysqli_query($conn,

    "UPDATE instructors SET

        first_name='$first_name',
        last_name='$last_name',
        instructor_code='$code',
        phone='$phone',
        department_id='$department_id',
        specialization='$specialization'

        $photoUpdate

     WHERE id='$id'
    ");

    echo json_encode([

        "status"=>"success",
        "message"=>"Instructor updated successfully"

    ]);
}



elseif($action == "delete_instructor"){

    $id=$_GET['id'];

    mysqli_query($conn,"DELETE FROM instructors WHERE id='$id'");

    echo json_encode(["status"=>"success","message"=>"Deleted"]);
}







// Read SUBJECT
elseif($action == "read_subjects"){

    $res = mysqli_query($conn,"
    SELECT * FROM subjects ORDER BY id DESC
    ");

    $data=[];

    while($row=mysqli_fetch_assoc($res)){
    $data[]=$row;
    }

    echo json_encode($data);
}



// SINGLE SUBJECT
elseif($action == "single_subject"){

    $id=$_GET['id'];

    $res=mysqli_query($conn,"
    SELECT * FROM subjects WHERE id='$id'
    ");

    echo json_encode(mysqli_fetch_assoc($res));
}


// CREATE SUBJECT
elseif($action == "create_subject"){

    $name = $_POST['subject_name'];
    $code = $_POST['subject_code'];
    $credit = $_POST['credit_hour'];

    $errors=[];

    /* duplicate check */
    $check = mysqli_query($conn,"
    SELECT id FROM subjects WHERE subject_code='$code'
    ");

    if(mysqli_num_rows($check)>0){

    $errors['subject_code_error']="Subject code already exists";
    echo json_encode(["status"=>"error"] + $errors);
    exit;
    }

    mysqli_query($conn,"
    INSERT INTO subjects(subject_name,subject_code,credit_hour)
    VALUES('$name','$code','$credit')
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Subject created successfully"
    ]);

}


// UPDATE SUBJECT
elseif($action == "update_subject"){

    $id=$_POST['id'];
    $name=$_POST['subject_name'];
    $code=$_POST['subject_code'];
    $credit=$_POST['credit_hour'];

    $check=mysqli_query($conn,"
    SELECT id FROM subjects
    WHERE subject_code='$code'
    AND id!='$id'
    ");

    if(mysqli_num_rows($check)>0){

    echo json_encode([
    "status"=>"error",
    "subject_code_error"=>"Subject code already exists"
    ]);
    exit;

    }

    mysqli_query($conn,"
    UPDATE subjects SET
    subject_name='$name',
    subject_code='$code',
    credit_hour='$credit'
    WHERE id='$id'
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Subject updated successfully"
    ]);

}


// DELETE SUBJECT
elseif($action == "delete_subject"){

    $id=$_GET['id'];

    mysqli_query($conn,"
    DELETE FROM subjects WHERE id='$id'
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Subject deleted"
    ]);

}





// READ ASSIGNMENTS

elseif($action=="read_assignments"){

    $query="
    SELECT enrollments.*,
    students.first_name,
    students.last_name,
    classes.class_name,
    departments.department_name,
    students.enrollment_year

    FROM enrollments

    JOIN students ON enrollments.student_id=students.id
    JOIN classes ON enrollments.class_id=classes.id
    JOIN departments ON classes.department_id=departments.id

    ORDER BY enrollments.id DESC
    ";

    $res=mysqli_query($conn,$query);

    $data=[];

    while($row=mysqli_fetch_assoc($res)){
    $data[]=$row;
    }

    echo json_encode($data);
}



// STUDENTS LIST

elseif($action=="students"){
    $res=mysqli_query($conn,"
    SELECT id,first_name,last_name FROM students
    ");

    $data=[];

    while($r=mysqli_fetch_assoc($res)){
    $data[]=$r;
    }

    echo json_encode($data);
}


// CLASSES LIST

elseif($action=="classes"){
    $res=mysqli_query($conn,"
    SELECT id,class_name FROM classes
    ");

    $data=[];

    while($r=mysqli_fetch_assoc($res)){
    $data[]=$r;
    }

    echo json_encode($data);
}


// SINGLE ASSIGNMENT

elseif($action=="single_assignment"){

    $id=$_GET['id'];

    $res=mysqli_query($conn,"
    SELECT * FROM enrollments WHERE id='$id'
    ");

    echo json_encode(mysqli_fetch_assoc($res));
}



// CREATE ASSIGNMENT
elseif($action=="create_assignment"){

    $student=$_POST['student_id'];
    $class=$_POST['class_id'];
    $dept=$_POST['department_id'];

    $check=mysqli_query($conn,"
    SELECT id FROM enrollments
    WHERE student_id='$student'
    AND class_id='$class'
    ");

    if(mysqli_num_rows($check)>0){

    echo json_encode([
    "status"=>"error",
    "message"=>"Student already assigned"
    ]);
    exit;
    }

    mysqli_query($conn,"
    INSERT INTO enrollments(student_id,class_id)
    VALUES('$student','$class')
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Student assigned successfully"
    ]);
}



// UPDATE ASSIGNMENT
elseif($action=="update_assignment"){

    $id=$_POST['id'];

    mysqli_query($conn,"
    UPDATE enrollments SET
    student_id='".$_POST['student_id']."',
    class_id='".$_POST['class_id']."'
    WHERE id='$id'
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Assignment updated"
    ]);
}


// DELETE

elseif($action=="delete_assignment"){

    $id=$_GET['id'];

    mysqli_query($conn,"
    DELETE FROM enrollments WHERE id='$id'
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Deleted"
    ]);
}






// READ
elseif($action=="read_instructor_assignments"){

    $query="
    SELECT class_subjects.*,
    instructors.first_name,
    instructors.last_name,
    classes.class_name,
    subjects.subject_name

    FROM class_subjects

    JOIN instructors ON class_subjects.instructor_id=instructors.id
    JOIN classes ON class_subjects.class_id=classes.id
    JOIN subjects ON class_subjects.subject_id=subjects.id

    ORDER BY class_subjects.id DESC
    ";

    $res=mysqli_query($conn,$query);

    $data=[];

    while($row=mysqli_fetch_assoc($res)){
    $data[]=$row;
    }

    echo json_encode($data);
}


// DROPDOWNS
elseif($action=="instructors"){

    $r=mysqli_query($conn,"SELECT id,first_name,last_name FROM instructors");
    $data=[];
    while($row=mysqli_fetch_assoc($r)){
        $data[]=$row;
    }

    echo json_encode($data);
}

elseif($action=="subjects"){

    $r=mysqli_query($conn,"SELECT id,subject_name,subject_code,credit_hour FROM subjects");
    $data=[];
    while($row=mysqli_fetch_assoc($r)){
        $data[]=$row;
    }

    echo json_encode($data);
}


// SINGLE
elseif($action=="single_instructor_assignment"){

    $id=$_GET['id'];

    $r=mysqli_query($conn,"
    SELECT * FROM class_subjects WHERE id='$id'
    ");

    echo json_encode(mysqli_fetch_assoc($r));
}




// CREATE
elseif($action=="create_instructor_assignment"){
    $instructor=$_POST['instructor_id'];
    $class=$_POST['class_id'];
    $subject=$_POST['subject_id'];
    $semester=$_POST['semester'];

    $check=mysqli_query($conn,"
    SELECT id FROM class_subjects
    WHERE instructor_id='$instructor'
    AND class_id='$class'
    AND subject_id='$subject'
    AND semester='$semester'
    ");

    if(mysqli_num_rows($check)>0){

    echo json_encode([
    "status"=>"error",
    "message"=>"Already assigned"
    ]);
    exit;
    }

    mysqli_query($conn,"
    INSERT INTO class_subjects(
    instructor_id,class_id,subject_id,semester
    )
    VALUES(
    '$instructor','$class','$subject','$semester'
    )
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Assigned successfully"
    ]);

}



// UPDATE
elseif($action=="update_instructor_assignment"){

    $id=$_POST['id'];

    mysqli_query($conn,"
    UPDATE class_subjects SET
    instructor_id='".$_POST['instructor_id']."',
    class_id='".$_POST['class_id']."',
    subject_id='".$_POST['subject_id']."',
    semester='".$_POST['semester']."'
    WHERE id='$id'
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Updated successfully"
    ]);

}



// DELETE

elseif($action=="delete_instructor_assignment"){

    $id=$_GET['id'];

    mysqli_query($conn,"
    DELETE FROM class_subjects WHERE id='$id'
    ");

    echo json_encode([
    "status"=>"success",
    "message"=>"Deleted successfully"
    ]);

}



/* --------------------------------
   SAVE FACE DATA
---------------------------------*/
elseif($action == "save_face_data"){

    $student_code = trim($_POST['student_code']);
    $capture_type = trim($_POST['capture_type']);
    $descriptor = $_POST['descriptor'];


    /* STUDENT */
    $studentQuery = mysqli_query($conn,

        "SELECT id
         FROM students
         WHERE student_code='$student_code'");

    if(mysqli_num_rows($studentQuery) == 0){

        echo json_encode([

            "status"=>"error",
            "message"=>"Student not found"

        ]);

        exit;
    }

    $student =
    mysqli_fetch_assoc($studentQuery);

    $student_id =
    $student['id'];

    /* INSERT NEW DESCRIPTOR */

    mysqli_query($conn,

    "INSERT INTO face_data(

        student_id,
        capture_type,
        face_descriptor

    )

    VALUES(

        '$student_id',
        '$capture_type',
        '$descriptor'

    )");

    createSystemLog(
        $conn,
        $_SESSION['user_id'],
        "CAPTURE_FACE",
        "Captured face data for student: ".$student_code

    );

    echo json_encode([

        "status"=>"success",

        "message"=>

        $capture_type .
        " face captured successfully"

    ]);

}




/* =========================================================
   READ ATTENDANCE SESSIONS
========================================================= */
elseif($action == "read_attendance_sessions"){

    $query = "

    SELECT

    attendance_sessions.*,

    subjects.subject_name,
    subjects.subject_code,

    classes.class_name,

    instructors.first_name AS instructor_first_name,
    instructors.last_name AS instructor_last_name

    FROM attendance_sessions

    LEFT JOIN class_subjects
    ON attendance_sessions.class_subject_id =
    class_subjects.id

    LEFT JOIN subjects
    ON class_subjects.subject_id =
    subjects.id

    LEFT JOIN classes
    ON class_subjects.class_id =
    classes.id

    LEFT JOIN instructors
    ON class_subjects.instructor_id =
    instructors.id

    ORDER BY attendance_sessions.id DESC

    ";

    $result = mysqli_query($conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);

}



/* =========================================================
   LOAD CLASS SUBJECTS
========================================================= */
elseif($action == "load_class_subjects"){

    $query = "

    SELECT

    class_subjects.id,

    subjects.subject_name,
    subjects.subject_code,

    classes.class_name

    FROM class_subjects

    LEFT JOIN subjects
    ON class_subjects.subject_id = subjects.id

    LEFT JOIN classes
    ON class_subjects.class_id = classes.id

    ORDER BY class_subjects.id DESC

    ";

    $result = mysqli_query($conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;
    }

    echo json_encode($data);

}



/* =========================================================
   SINGLE ATTENDANCE SESSION
========================================================= */
elseif($action == "single_attendance_session"){

    $id = $_GET['id'];

    $query = "

    SELECT *
    FROM attendance_sessions
    WHERE id='$id'

    ";

    $result = mysqli_query($conn, $query);

    echo json_encode(
        mysqli_fetch_assoc($result)
    );

}



/* =========================================================
   CREATE ATTENDANCE SESSION
========================================================= */
elseif($action == "create_attendance_session"){

    $class_subject_id =
    $_POST['class_subject_id'];

    $session_name =
    trim($_POST['session_name']);

    $session_date =
    $_POST['session_date'];

    $start_time =
    $_POST['start_time'];

    $end_time =
    $_POST['end_time'];

    $room =
    trim($_POST['room']);

    // $status =
    // $_POST['status'];

    $created_by = 1;

    mysqli_query($conn,

    "INSERT INTO attendance_sessions(

        class_subject_id,
        session_name,
        session_date,
        start_time,
        end_time,
        room,
        created_by

    )

    VALUES(

        '$class_subject_id',
        '$session_name',
        '$session_date',
        '$start_time',
        '$end_time',
        '$room',
        '$created_by'

    )");

    echo json_encode([

        "status" => "success",
        "message" =>
        "Attendance session created successfully"

    ]);

}



/* =========================================================
   UPDATE ATTENDANCE SESSION
========================================================= */
elseif($action == "update_attendance_session"){

    $id = $_POST['id'];

    $class_subject_id =
    $_POST['class_subject_id'];

    $session_name =
    trim($_POST['session_name']);

    $session_date =
    $_POST['session_date'];

    $start_time =
    $_POST['start_time'];

    $end_time =
    $_POST['end_time'];

    $room =
    trim($_POST['room']);

    // $status =
    // $_POST['status'];

    mysqli_query($conn,

    "UPDATE attendance_sessions SET

        class_subject_id='$class_subject_id',
        session_name='$session_name',
        session_date='$session_date',
        start_time='$start_time',
        end_time='$end_time',
        room='$room'

    WHERE id='$id'

    ");

    echo json_encode([

        "status" => "success",
        "message" =>
        "Attendance session updated successfully"

    ]);

}



/* =========================================================
   DELETE ATTENDANCE SESSION
========================================================= */
elseif($action == "delete_attendance_session"){

    $id = $_GET['id'];

    mysqli_query($conn,

    "DELETE FROM attendance_sessions
     WHERE id='$id'");

    echo json_encode([

        "status" => "success",
        "message" =>
        "Attendance session deleted successfully"

    ]);

}








/* --------------------------------
   LOAD FACE DESCRIPTORS
---------------------------------*/
elseif($action == "load_face_descriptors"){

    $query = "

    SELECT

    students.id,
    students.student_code,
    students.first_name,
    students.last_name,
    face_data.face_descriptor

    FROM face_data

    INNER JOIN students
    ON students.id = face_data.student_id

    ";

    $result =
    mysqli_query($conn, $query);

    $data = [];

    while($row =
    mysqli_fetch_assoc($result)){

        $row['face_descriptor'] =

        json_decode(
            $row['face_descriptor']
        );

        $data[] = $row;
    }

    echo json_encode($data);
}



/* --------------------------------
   SAVE ATTENDANCE
---------------------------------*/
elseif($action == "save_attendance"){

    $student_code =
    trim($_POST['student_code']);

    $attendance_session_id =
    intval($_POST['attendance_session_id']);

    /* STUDENT */

    $studentQuery = mysqli_query($conn,

    "SELECT id,
            first_name,
            last_name

     FROM students

     WHERE student_code='$student_code'");

    if(mysqli_num_rows($studentQuery) == 0){

        echo json_encode([

            "status"=>"error",
            "message"=>"Student not found"

        ]);

        exit;
    }

    $student =
    mysqli_fetch_assoc($studentQuery);

    $student_id =
    $student['id'];

    /* DUPLICATE CHECK */

    $check = mysqli_query($conn,

    "SELECT id

     FROM attendance

     WHERE attendance_session_id='$attendance_session_id'

     AND student_id='$student_id'");

    if(mysqli_num_rows($check) > 0){

        echo json_encode([

            "status"=>"duplicate"

        ]);

        exit;
    }

    /* INSERT ATTENDANCE */

    mysqli_query($conn,

    "INSERT INTO attendance(

        attendance_session_id,
        student_id,
        attendance_time,
        status,
        attendance_method,
        recognition_confidence

    )

    VALUES(

        '$attendance_session_id',
        '$student_id',
        NOW(),
        'Present',
        'Face Recognition',
        '98.00'

    )");

    /* LOG */

    mysqli_query($conn,

    "INSERT INTO recognition_logs(

        student_id,
        confidence_score,
        recognition_status

    )

    VALUES(

        '$student_id',
        '98.00',
        'Matched'

    )");

    echo json_encode([

        "status"=>"success",

        "student_code"=>$student_code,

        "student_name"=>

        $student['first_name']
        . " " .
        $student['last_name'],

        "time"=>date("H:i:s")

    ]);
}





elseif($action == 'generate_attendance_report'){

    $report_type = $_POST['report_type'];
    $date = $_POST['date'];
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];


    $where = " WHERE 1=1 ";


    /*
    |-------------------------------------
    | DAILY REPORT
    |-------------------------------------
    */

    if($report_type == 'daily'){

        $where .= " AND DATE(a.attendance_time) = '$date' ";

    }


    /*
    |-------------------------------------
    | WEEKLY REPORT
    |-------------------------------------
    */

    if($report_type == 'weekly'){

        $where .= "
        AND YEARWEEK(a.attendance_time, 1)
        = YEARWEEK('$date', 1)
        ";

    }


    /*
    |-------------------------------------
    | MONTHLY REPORT
    |-------------------------------------
    */

    if($report_type == 'monthly'){

        $where .= "
        AND MONTH(a.attendance_time)
        = MONTH('$date')

        AND YEAR(a.attendance_time)
        = YEAR('$date')
        ";

    }


    /*
    |-------------------------------------
    | STUDENT REPORT
    |-------------------------------------
    */

    if($report_type == 'student'){

        $where .= "
        AND s.id = '$student_id'
        ";

    }


    /*
    |-------------------------------------
    | CLASS REPORT
    |-------------------------------------
    */

    if($report_type == 'class'){

        $where .= "
        AND c.id = '$class_id'
        ";

    }


    $query = mysqli_query($conn, "

        SELECT

        a.*,

        s.student_code,
        s.first_name,
        s.last_name,

        c.class_name,
        c.section,

        attendance_sessions.session_name,
        attendance_sessions.session_date

        FROM attendance a

        INNER JOIN students s
        ON a.student_id = s.id

        INNER JOIN classes c
        ON s.class_id = c.id

        INNER JOIN attendance_sessions
        ON a.attendance_session_id = attendance_sessions.id

        $where

        ORDER BY a.attendance_time DESC

    ");


    echo '

    <div class="table-responsive">

    <table class="table table-bordered table-striped">

        <thead class="table-dark">

            <tr>
                <th>#</th>
                <th>Student Code</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Session</th>
                <th>Date</th>
                <th>Status</th>
                <th>Method</th>
                <th>Confidence</th>
                <th>Attendance Time</th>
            </tr>

        </thead>

        <tbody>

    ';


    if(mysqli_num_rows($query) > 0){

        $count = 1;

        while($row = mysqli_fetch_assoc($query)){

            echo '

            <tr>

                <td>'.$count++.'</td>

                <td>'.$row['student_code'].'</td>

                <td>
                    '.$row['first_name'].'
                    '.$row['last_name'].'
                </td>

                <td>
                    '.$row['class_name'].'
                    ('.$row['section'].')
                </td>

                <td>'.$row['session_name'].'</td>

                <td>'.$row['session_date'].'</td>

                <td>
                    <span class="badge bg-success">
                        '.$row['status'].'
                    </span>
                </td>

                <td>'.$row['attendance_method'].'</td>

                <td>'.$row['recognition_confidence'].'%</td>

                <td>'.$row['attendance_time'].'</td>

            </tr>

            ';

        }

    }else{

        echo '

        <tr>
            <td colspan="10" class="text-center text-danger">
                No Attendance Record Found
            </td>
        </tr>

        ';

    }


    echo '

        </tbody>

    </table>

    </div>

    ';

}



/*
|-----------------------------------
| ADMIN DASHBOARD
|-----------------------------------
*/

elseif($action == "load_admin_dashboard"){

    $totalStudents = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM students")
    )['total'];

    $present = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM attendance
         WHERE status='Present'")
    )['total'];

    $late = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM attendance
         WHERE status='Late'")
    )['total'];

    $absent = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM attendance
         WHERE status='Absent'")
    )['total'];

    $totalAttendance =
        $present + $late + $absent;

    if($totalAttendance == 0){
        $totalAttendance = 1;
    }

    $presentPercent =
        round(($present/$totalAttendance)*100,2);

    $latePercent =
        round(($late/$totalAttendance)*100,2);

    $absentPercent =
        round(($absent/$totalAttendance)*100,2);

    /*
    |-----------------------------------
    | WEEKLY TREND
    |-----------------------------------
    */

    $weekDays = [];
    $weekAttendance = [];

    for($i = 6; $i >=0; $i--){

        $date = date("Y-m-d",
            strtotime("-$i days"));

        $dayName = date("D",
            strtotime($date));

        $count = mysqli_fetch_assoc(
            mysqli_query($conn,
            "SELECT COUNT(*) as total
             FROM attendance
             WHERE DATE(attendance_time)='$date'")
        )['total'];

        $weekDays[] = $dayName;
        $weekAttendance[] = $count;
    }

    echo json_encode([
        "total_students" => $totalStudents,

        "present_count" => $present,
        "late_count" => $late,
        "absent_count" => $absent,

        "present_percent" => $presentPercent,
        "late_percent" => $latePercent,
        "absent_percent" => $absentPercent,

        "week_days" => $weekDays,
        "week_attendance" => $weekAttendance
    ]);

}


elseif($action == "load_instructor_dashboard"){

    $user_id = $_SESSION['user_id'];

    // $instructor = mysqli_fetch_assoc(
    //     mysqli_query($conn,
    //     "SELECT id
    //      FROM instructors
    //      WHERE user_id='$user_id'")
    // );

    // $instructor_id = $instructor['id'];

        $instructorQuery = mysqli_query($conn,
            "SELECT id
             FROM instructors
             WHERE user_id='$user_id'");

        $instructor = mysqli_fetch_assoc($instructorQuery);

        if(!$instructor){

            echo json_encode([
                "present" => 0,
                "late" => 0,
                "absent" => 0,
                "message" => "Instructor record not found"
            ]);

            exit;
        }

        $instructor_id = $instructor['id'];



    $query = "
    SELECT attendance.status,
    COUNT(attendance.id) as total

    FROM attendance

    INNER JOIN attendance_sessions
    ON attendance.attendance_session_id =
    attendance_sessions.id

    INNER JOIN class_subjects
    ON attendance_sessions.class_subject_id =
    class_subjects.id

    WHERE class_subjects.instructor_id =
    '$instructor_id'

    GROUP BY attendance.status
    ";

    $result = mysqli_query($conn, $query);

    $present = 0;
    $late = 0;
    $absent = 0;

    while($row = mysqli_fetch_assoc($result)){

        if($row['status'] == "Present"){
            $present = $row['total'];
        }

        if($row['status'] == "Late"){
            $late = $row['total'];
        }

        if($row['status'] == "Absent"){
            $absent = $row['total'];
        }
    }

    echo json_encode([
        "present"=>$present,
        "late"=>$late,
        "absent"=>$absent
    ]);
}



elseif($action == "load_student_dashboard"){

    $user_id = $_SESSION['user_id'];

    $studentQuery = mysqli_query($conn,
    "SELECT *
     FROM students
     WHERE user_id='$user_id'");

    if(mysqli_num_rows($studentQuery) == 0){

        echo json_encode([
            "present" => 0,
            "late" => 0,
            "absent" => 0,
            "percent" => 0,
            "message" => "Student record not found"
        ]);

        exit;
    }

    $student = mysqli_fetch_assoc($studentQuery);

    $student_id = $student['id'];


    $present = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM attendance
         WHERE student_id='$student_id'
         AND status='Present'")
    )['total'];

    $late = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM attendance
         WHERE student_id='$student_id'
         AND status='Late'")
    )['total'];

    $absent = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM attendance
         WHERE student_id='$student_id'
         AND status='Absent'")
    )['total'];

    $total = $present + $late + $absent;

    if($total == 0){
        $total = 1;
    }

    $percent = round(
        (($present + $late) / $total) * 100,
        2
    );

    echo json_encode([
        "present"=>$present,
        "late"=>$late,
        "absent"=>$absent,
        "percent"=>$percent
    ]);
}








/*
|------------------------------------
| GET STUDENT ATTENDANCE SUMMARY
|------------------------------------
*/

elseif($action == "getStudentAttendanceSummary"){

    $user_id = $_SESSION['user_id'];

    /*
    |--------------------------------------------------------------------------
    | GET STUDENT
    |--------------------------------------------------------------------------
    */

    $studentQuery = mysqli_query($conn,"
    SELECT id
    FROM students
    WHERE user_id = '$user_id'
    ");

    $student = mysqli_fetch_assoc($studentQuery);

    if(!$student){

        echo json_encode([
            "present" => 0,
            "absent" => 0,
            "late" => 0,
            "percentage" => 0
        ]);

        exit;
    }

    $student_id = $student['id'];

    /*
    |--------------------------------------------------------------------------
    | TOTAL PRESENT
    |--------------------------------------------------------------------------
    */

    $presentQuery = mysqli_query($conn,"
    SELECT COUNT(*) as total
    FROM attendance
    WHERE student_id = '$student_id'
    AND status = 'Present'
    ");

    $present =
    mysqli_fetch_assoc($presentQuery)['total'];

    /*
    |--------------------------------------------------------------------------
    | TOTAL ABSENT
    |--------------------------------------------------------------------------
    */

    $absentQuery = mysqli_query($conn,"
    SELECT COUNT(*) as total
    FROM attendance
    WHERE student_id = '$student_id'
    AND status = 'Absent'
    ");

    $absent =
    mysqli_fetch_assoc($absentQuery)['total'];

    /*
    |--------------------------------------------------------------------------
    | TOTAL LATE
    |--------------------------------------------------------------------------
    */

    $lateQuery = mysqli_query($conn,"
    SELECT COUNT(*) as total
    FROM attendance
    WHERE student_id = '$student_id'
    AND status = 'Late'
    ");

    $late =
    mysqli_fetch_assoc($lateQuery)['total'];

    /*
    |--------------------------------------------------------------------------
    | TOTAL SESSIONS
    |--------------------------------------------------------------------------
    */

    $total = $present + $absent + $late;

    $percentage = 0;

    if($total > 0){

        $percentage =
        round(($present / $total) * 100, 2);

    }

    echo json_encode([

        "present" => $present,
        "absent" => $absent,
        "late" => $late,
        "percentage" => $percentage

    ]);

    exit;
}



/*
|------------------------------------
| GET STUDENT ATTENDANCE HISTORY
|------------------------------------
*/

elseif($action == "getStudentAttendanceHistory"){

    $user_id = $_SESSION['user_id'];

    /*
    |--------------------------------------------------------------------------
    | GET STUDENT
    |--------------------------------------------------------------------------
    */

    $studentQuery = mysqli_query($conn,"
    SELECT id
    FROM students
    WHERE user_id = '$user_id'
    ");

    $student = mysqli_fetch_assoc($studentQuery);

    if(!$student){

        echo json_encode([]);

        exit;
    }

    $student_id = $student['id'];

    /*
    |--------------------------------------------------------------------------
    | DATE FILTER
    |--------------------------------------------------------------------------
    */

    $where = "";

    if(
        isset($_GET['start_date']) &&
        isset($_GET['end_date']) &&
        !empty($_GET['start_date']) &&
        !empty($_GET['end_date'])
    ){

        $start_date =
        mysqli_real_escape_string(
            $conn,
            $_GET['start_date']
        );

        $end_date =
        mysqli_real_escape_string(
            $conn,
            $_GET['end_date']
        );

        $where = "
        AND attendance_sessions.session_date
        BETWEEN '$start_date'
        AND '$end_date'
        ";
    }

    /*
    |--------------------------------------------------------------------------
    | GET ATTENDANCE HISTORY
    |--------------------------------------------------------------------------
    */

    $query = mysqli_query($conn,"
    SELECT

        attendance.*,

        attendance_sessions.session_name,
        attendance_sessions.session_date,

        subjects.subject_name

    FROM attendance

    LEFT JOIN attendance_sessions
    ON attendance.attendance_session_id =
       attendance_sessions.id

    LEFT JOIN class_subjects
    ON attendance_sessions.class_subject_id =
       class_subjects.id

    LEFT JOIN subjects
    ON class_subjects.subject_id =
       subjects.id

    WHERE attendance.student_id = '$student_id'

    $where

    ORDER BY attendance.attendance_time DESC
    ");

    $rows = [];

    while($row = mysqli_fetch_assoc($query)){

        $rows[] = [

            "id" => $row['id'],

            "session_name" =>
            $row['session_name'],

            "subject_name" =>
            $row['subject_name'],

            "session_date" =>
            $row['session_date'],

            "attendance_time" =>
            date(
                "h:i A",
                strtotime($row['attendance_time'])
            ),

            "status" =>
            $row['status'],

            "attendance_method" =>
            $row['attendance_method'],

            "recognition_confidence" =>
            $row['recognition_confidence']

        ];
    }

    echo json_encode($rows);

    exit;
}






















/*
|-----------------------------
| SAVE CONTACT MESSAGE
|-----------------------------
*/

elseif($action == "send_contact_message"){

    $errors = [];

    $full_name = trim($_POST['full_name']);

    $email = trim($_POST['email']);

    $subject = trim($_POST['subject']);

    $message = trim($_POST['message']);






    if(empty($full_name)){
        $errors['full_name'] = "Full name is required";
    }

    if(empty($email)){
        $errors['email'] = "Email is required";
    }

    if(empty($subject)){
        $errors['subject'] = "Subject is required";
    }

    if(empty($message)){
        $errors['message'] = "Message is required";
    }






    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $insert = mysqli_query($conn,

        "INSERT INTO contact_messages(

            full_name,
            email,
            subject,
            message,
            status

        )

        VALUES(

            '$full_name',
            '$email',
            '$subject',
            '$message',
            'unread'

        )"

    );






    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "Message sent successfully"
        ]);

    }

}



/*
|--------------------------------
| MARK CONTACT MESSAGE AS READ
|--------------------------------
*/

elseif($action == "mark_contact_message_read"){

    $id = $_POST['id'];

    mysqli_query($conn,

        "UPDATE contact_messages SET

        status = 'read'

        WHERE id = '$id'"

    );






    echo json_encode([
        "status" => "success",
        "message" => "Message marked as read"
    ]);

}



/*
|-------------------------------
| DELETE CONTACT MESSAGE
|-------------------------------
*/

elseif($action == "delete_contact_message"){

    $id = $_POST['id'];






    mysqli_query(
        $conn,
        "DELETE FROM contact_messages WHERE id = '$id'"
    );






    echo json_encode([
        "status" => "success",
        "message" => "Message deleted successfully"
    ]);

}






/*
|---------------------------
| CREATE CONTACT INFO
|----------------------------
*/

elseif($action == "create_contact_info"){

    $errors = [];

    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $facebook = trim($_POST['facebook']);
    $telegram = trim($_POST['telegram']);
    $status = trim($_POST['status']);



    if(empty($email)){
        $errors['email'] = "Email is required";
    }

    if(empty($phone)){
        $errors['phone'] = "Phone number is required";
    }

    if(empty($address)){
        $errors['address'] = "Address is required";
    }






    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $insert = mysqli_query($conn,

        "INSERT INTO contact_info(

            email,
            phone,
            office_address,
            facebook_link,
            telegram_link,
            status

        )

        VALUES(

            '$email',
            '$phone',
            '$address',
            '$facebook',
            '$telegram',
            '$status'

        )"

    );





    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "Contact information created successfully"
        ]);

    }

}






/*
|-----------------------------
| UPDATE CONTACT INFO
|------------------------------
*/

elseif($action == "update_contact_info"){

    $id = $_POST['id'];


    $email = trim($_POST['email']);

    $phone = trim($_POST['phone']);

    $address = trim($_POST['address']);

    $facebook = trim($_POST['facebook']);

    $telegram = trim($_POST['telegram']);

    $status = trim($_POST['status']);






    mysqli_query($conn,

        "UPDATE contact_info SET

        email = '$email',
        phone = '$phone',
        office_address = '$address',
        facebook_link = '$facebook',
        telegram_link = '$telegram',
        status = '$status'

        WHERE id = '$id'"

    );





    echo json_encode([
        "status" => "success",
        "message" => "Contact information updated successfully"
    ]);

}






/*
|------------------------------
| DELETE CONTACT INFO
|------------------------------
*/

elseif($action == "delete_contact_info"){

    $id = $_POST['id'];






    mysqli_query(
        $conn,
        "DELETE FROM contact_info WHERE id = '$id'"
    );






    echo json_encode([
        "status" => "success",
        "message" => "Contact information deleted successfully"
    ]);

}



















/*
|-----------------------------
| CREATE ABOUT
|-----------------------------
*/

elseif($action == "create_about"){

    $errors = [];

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $mission = trim($_POST['mission']);
    $vision = trim($_POST['vision']);
    $status = trim($_POST['status']);





    if(empty($title)){
        $errors['title'] = "Title is required";
    }

    if(empty($description)){
        $errors['description'] = "Description is required";
    }

    if(empty($mission)){
        $errors['mission'] = "Mission is required";
    }

    if(empty($vision)){
        $errors['vision'] = "Vision is required";
    }

    if(empty($_FILES['image']['name'])){
        $errors['image'] = "Image is required";
    }






    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $image_name = time() . "_" . $_FILES['image']['name'];

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "../uploads/about/" . $image_name
    );






    $insert = mysqli_query($conn,

        "INSERT INTO about(

            title,
            description,
            mission,
            vision,
            image,
            status

        )

        VALUES(

            '$title',
            '$description',
            '$mission',
            '$vision',
            '$image_name',
            '$status'

        )"

    );






    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "About created successfully"
        ]);

    }

}






/*
|--------------------------
| UPDATE ABOUT
|--------------------------
*/

elseif($action == "update_about"){

    $id = $_POST['id'];

    $title = trim($_POST['title']);

    $description = trim($_POST['description']);

    $mission = trim($_POST['mission']);

    $vision = trim($_POST['vision']);

    $status = trim($_POST['status']);






    if(!empty($_FILES['image']['name'])){

        $image_name = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/about/" . $image_name
        );






        mysqli_query($conn,

            "UPDATE about SET

            title = '$title',
            description = '$description',
            mission = '$mission',
            vision = '$vision',
            image = '$image_name',
            status = '$status'

            WHERE id = '$id'"

        );

    }else{

        mysqli_query($conn,

            "UPDATE about SET

            title = '$title',
            description = '$description',
            mission = '$mission',
            vision = '$vision',
            status = '$status'

            WHERE id = '$id'"

        );

    }







    echo json_encode([
        "status" => "success",
        "message" => "About updated successfully"
    ]);

}






/*
|------------------------
| DELETE ABOUT
|-------------------------
*/

elseif($action == "delete_about"){

    $id = $_POST['id'];






    $get = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM about WHERE id = '$id'"
    ));






    if($get){

        if(!empty($get['image'])){

            if(file_exists("../uploads/about/" . $get['image'])){

                unlink("../uploads/about/" . $get['image']);

            }

        }






        mysqli_query(
            $conn,
            "DELETE FROM about WHERE id = '$id'"
        );






        echo json_encode([
            "status" => "success",
            "message" => "About deleted successfully"
        ]);

    }

}





/*
|---------------------------------
| CREATE TESTIMONIAL
|---------------------------------`
*/

elseif($action == "create_testimonial"){

    $errors = [];

    $full_name = trim($_POST['full_name']);
    $profession = trim($_POST['profession']);
    $message = trim($_POST['message']);
    $rating = trim($_POST['rating']);
    $status = trim($_POST['status']);





    if(empty($full_name)){
        $errors['full_name'] = "Full name is required";
    }

    if(empty($profession)){
        $errors['profession'] = "Profession is required";
    }

    if(empty($message)){
        $errors['message'] = "Message is required";
    }

    if(empty($_FILES['photo']['name'])){
        $errors['photo'] = "Photo is required";
    }






    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $photo_name = time() . "_" . $_FILES['photo']['name'];

    move_uploaded_file(
        $_FILES['photo']['tmp_name'],
        "../uploads/testimonials/" . $photo_name
    );






    $insert = mysqli_query($conn,

        "INSERT INTO testimonials(

            full_name,
            profession,
            message,
            rating,
            photo,
            status

        )

        VALUES(

            '$full_name',
            '$profession',
            '$message',
            '$rating',
            '$photo_name',
            '$status'

        )"

    );






    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "Testimonial created successfully"
        ]);

    }

}






/*
|----------------------------
| UPDATE TESTIMONIAL
|-----------------------------
*/

elseif($action == "update_testimonial"){

    $id = $_POST['id'];

    $full_name = trim($_POST['full_name']);

    $profession = trim($_POST['profession']);

    $message = trim($_POST['message']);

    $rating = trim($_POST['rating']);

    $status = trim($_POST['status']);






    if(!empty($_FILES['photo']['name'])){

        $photo_name = time() . "_" . $_FILES['photo']['name'];

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            "../uploads/testimonials/" . $photo_name
        );






        mysqli_query($conn,

            "UPDATE testimonials SET

            full_name = '$full_name',
            profession = '$profession',
            message = '$message',
            rating = '$rating',
            photo = '$photo_name',
            status = '$status'

            WHERE id = '$id'"

        );

    }else{

        mysqli_query($conn,

            "UPDATE testimonials SET

            full_name = '$full_name',
            profession = '$profession',
            message = '$message',
            rating = '$rating',
            status = '$status'

            WHERE id = '$id'"

        );

    }






    echo json_encode([
        "status" => "success",
        "message" => "Testimonial updated successfully"
    ]);

}






/*
|---------------------------
| DELETE TESTIMONIAL
|---------------------------
*/

elseif($action == "delete_testimonial"){

    $id = $_POST['id'];






    $get = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM testimonials WHERE id = '$id'"
    ));






    if($get){

        if(!empty($get['photo'])){

            if(file_exists("../uploads/testimonials/" . $get['photo'])){

                unlink("../uploads/testimonials/" . $get['photo']);

            }

        }






        mysqli_query(
            $conn,
            "DELETE FROM testimonials WHERE id = '$id'"
        );






        echo json_encode([
            "status" => "success",
            "message" => "Testimonial deleted successfully"
        ]);

    }

}






/*
|------------------------------
| CREATE HOW IT WORKS
|-------------------------------
*/

elseif($action == "create_how_it_works"){

    $errors = [];

    $step_number = trim($_POST['step_number']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $status = trim($_POST['status']);





    if(empty($step_number)){
        $errors['step_number'] = "Step number is required";
    }

    if(empty($title)){
        $errors['title'] = "Title is required";
    }

    if(empty($description)){
        $errors['description'] = "Description is required";
    }

    if(empty($icon)){
        $errors['icon'] = "Icon is required";
    }






    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $image_name = "";

    if(!empty($_FILES['image']['name'])){

        $image_name = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/how_it_works/" . $image_name
        );

    }






    $insert = mysqli_query($conn,

        "INSERT INTO how_it_works(

            step_number,
            title,
            description,
            icon,
            image,
            status

        )

        VALUES(

            '$step_number',
            '$title',
            '$description',
            '$icon',
            '$image_name',
            '$status'

        )"

    );






    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "Step created successfully"
        ]);

    }

}






/*
|---------------------------------
| UPDATE HOW IT WORKS
|---------------------------------
*/

elseif($action == "update_how_it_works"){

    $id = $_POST['id'];

    $step_number = trim($_POST['step_number']);

    $title = trim($_POST['title']);

    $description = trim($_POST['description']);

    $icon = trim($_POST['icon']);

    $status = trim($_POST['status']);






    if(!empty($_FILES['image']['name'])){

        $image_name = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/how_it_works/" . $image_name
        );






        mysqli_query($conn,

            "UPDATE how_it_works SET

            step_number = '$step_number',
            title = '$title',
            description = '$description',
            icon = '$icon',
            image = '$image_name',
            status = '$status'

            WHERE id = '$id'"

        );

    }else{

        mysqli_query($conn,

            "UPDATE how_it_works SET

            step_number = '$step_number',
            title = '$title',
            description = '$description',
            icon = '$icon',
            status = '$status'

            WHERE id = '$id'"

        );

    }






    echo json_encode([
        "status" => "success",
        "message" => "Step updated successfully"
    ]);

}






/*
|---------------------------------
| DELETE HOW IT WORKS
|---------------------------------
*/

elseif($action == "delete_how_it_works"){

    $id = $_POST['id'];






    $get = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM how_it_works WHERE id = '$id'"
    ));






    if($get){

        if(!empty($get['image'])){

            if(file_exists("../uploads/how_it_works/" . $get['image'])){

                unlink("../uploads/how_it_works/" . $get['image']);

            }

        }






        mysqli_query(
            $conn,
            "DELETE FROM how_it_works WHERE id = '$id'"
        );






        echo json_encode([
            "status" => "success",
            "message" => "Step deleted successfully"
        ]);

    }

}










/*
|-------------------------
| CREATE SERVICE
|-------------------------
*/

elseif($action == "create_service"){

    $errors = [];

    $service_name = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $status = trim($_POST['status']);





    if(empty($service_name)){
        $errors['service_name'] = "Service name is required";
    }

    if(empty($description)){
        $errors['description'] = "Description is required";
    }

    if(empty($icon)){
        $errors['icon'] = "Icon is required";
    }

    if(empty($_FILES['image']['name'])){
        $errors['image'] = "Image is required";
    }






    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $image_name = time() . "_" . $_FILES['image']['name'];

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "../uploads/services/" . $image_name
    );






    $insert = mysqli_query($conn,

        "INSERT INTO services(

            service_name,
            description,
            icon,
            image,
            status

        )

        VALUES(

            '$service_name',
            '$description',
            '$icon',
            '$image_name',
            '$status'

        )"

    );






    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "Service created successfully"
        ]);

    }

}




/*
|----------------------------
| UPDATE SERVICE
|----------------------------
*/

elseif($action == "update_service"){

    $id = $_POST['id'];

    $service_name = trim($_POST['service_name']);

    $description = trim($_POST['description']);

    $icon = trim($_POST['icon']);

    $status = trim($_POST['status']);






    if(!empty($_FILES['image']['name'])){

        $image_name = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/services/" . $image_name
        );






        mysqli_query($conn,

            "UPDATE services SET

            service_name = '$service_name',
            description = '$description',
            icon = '$icon',
            image = '$image_name',
            status = '$status'

            WHERE id = '$id'"

        );

    }else{

        mysqli_query($conn,

            "UPDATE services SET

            service_name = '$service_name',
            description = '$description',
            icon = '$icon',
            status = '$status'

            WHERE id = '$id'"

        );

    }






    echo json_encode([
        "status" => "success",
        "message" => "Service updated successfully"
    ]);

}






/*
|---------------------------
| DELETE SERVICE
|---------------------------
*/

elseif($action == "delete_service"){

    $id = $_POST['id'];






    $get = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM services WHERE id = '$id'"
    ));






    if($get){

        if(file_exists("../uploads/services/" . $get['image'])){

            unlink("../uploads/services/" . $get['image']);

        }






        mysqli_query(
            $conn,
            "DELETE FROM services WHERE id = '$id'"
        );






        echo json_encode([
            "status" => "success",
            "message" => "Service deleted successfully"
        ]);

    }

}






/*
|-------------------------
| CREATE CAROUSEL
|-------------------------
*/

if($action == "create_carousel"){

    $errors = [];

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);

    if(empty($title)){
        $errors['title'] = "Title is required";
    }

    if(empty($description)){
        $errors['description'] = "Description is required";
    }

    if(empty($_FILES['image']['name'])){
        $errors['image'] = "Image is required";
    }

    if(count($errors) > 0){

        echo json_encode([
            "status" => "error",
            "errors" => $errors
        ]);

        exit;
    }






    $image_name = time() . "_" . $_FILES['image']['name'];

    $tmp_name = $_FILES['image']['tmp_name'];

    move_uploaded_file(
        $tmp_name,
        "../uploads/carousel/" . $image_name
    );






    $insert = mysqli_query($conn,

        "INSERT INTO carousel(
            title,
            description,
            image,
            status
        )

        VALUES(
            '$title',
            '$description',
            '$image_name',
            '$status'
        )"

    );






    if($insert){

        echo json_encode([
            "status" => "success",
            "message" => "Carousel created successfully"
        ]);

    }

}



/*
|---------------------------
| UPDATE CAROUSEL
|--------------------------
*/

elseif($action == "update_carousel"){

    $id = $_POST['id'];

    $title = trim($_POST['title']);

    $description = trim($_POST['description']);

    $status = trim($_POST['status']);






    if(!empty($_FILES['image']['name'])){

        $image_name = time() . "_" . $_FILES['image']['name'];

        $tmp_name = $_FILES['image']['tmp_name'];

        move_uploaded_file(
            $tmp_name,
            "../uploads/carousel/" . $image_name
        );






        mysqli_query($conn,

            "UPDATE carousel SET

            title = '$title',
            description = '$description',
            image = '$image_name',
            status = '$status'

            WHERE id = '$id'"

        );

    }else{

        mysqli_query($conn,

            "UPDATE carousel SET

            title = '$title',
            description = '$description',
            status = '$status'

            WHERE id = '$id'"

        );

    }






    echo json_encode([
        "status" => "success",
        "message" => "Carousel updated successfully"
    ]);

}






/*
|------------------------
| DELETE CAROUSEL
|------------------------
*/

elseif($action == "delete_carousel"){

    $id = $_POST['id'];






    $get = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM carousel WHERE id = '$id'"
    ));






    if($get){

        if(file_exists("../uploads/carousel/" . $get['image'])){

            unlink("../uploads/carousel/" . $get['image']);

        }






        mysqli_query(
            $conn,
            "DELETE FROM carousel WHERE id = '$id'"
        );






        echo json_encode([
            "status" => "success",
            "message" => "Carousel deleted successfully"
        ]);

    }

}











/*
|------------------
| GET CAROUSEL
|-------------------
*/

elseif($action == "get_carousel"){

    $data = [];

    $query = mysqli_query($conn,
        "SELECT * FROM carousel WHERE status='active' ORDER BY id ASC");

    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }

    echo json_encode($data);
}






/*
|----------------
| GET SERVICES
|----------------
*/

elseif($action == "get_services"){

    $data = [];

    $query = mysqli_query($conn,
        "SELECT * FROM services WHERE status='active' ORDER BY id ASC");

    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }

    echo json_encode($data);
}






/*
|------------------------
| GET HOW IT WORKS
|------------------------
*/

elseif($action == "get_how_it_works"){

    $data = [];

    $query = mysqli_query($conn,
        "SELECT * FROM how_it_works WHERE status='active' ORDER BY step_number ASC");

    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }

    echo json_encode($data);
}






/*
|----------------------
| GET TESTIMONIALS
|----------------------
*/

elseif($action == "get_testimonials"){

    $data = [];

    $query = mysqli_query($conn,
        "SELECT * FROM testimonials WHERE status='active' ORDER BY id DESC");

    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }

    echo json_encode($data);
}






elseif($action == "get_about"){

    $query = mysqli_query($conn,
        "SELECT * FROM about WHERE status='active' ORDER BY id DESC LIMIT 1");

    $data = mysqli_fetch_assoc($query);

    echo json_encode($data);

}


elseif($action == "get_contact_info"){

    $query = mysqli_query($conn,
        "SELECT * FROM contact_info WHERE status='active' ORDER BY id DESC LIMIT 1");

    $data = mysqli_fetch_assoc($query);

    echo json_encode($data);

}


elseif($action == "get_services"){

    $data = [];

    $query = mysqli_query($conn,
        "SELECT * FROM services WHERE status='active' ORDER BY id ASC");

    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }

    echo json_encode($data);

}






/*
|---------------------------------------
| READ LOGS
|---------------------------------------
*/

elseif($action == "read_logs"){

    $filter_action =
    $_GET['filter_action'] ?? '';

    // $filter_severity =
    // $_GET['filter_severity'] ?? '';

    $filter_date =
    $_GET['filter_date'] ?? '';


    $where = "WHERE 1";


    if($filter_action != ""){

        $where .=
        " AND system_logs.action_type='$filter_action'";
    }

    // if($filter_severity != ""){

    //     $where .=
    //     " AND system_logs.severity='$filter_severity'";
    // }

    if($filter_date != ""){

        $where .=
        " AND DATE(system_logs.created_at)='$filter_date'";
    }





    $query = mysqli_query($conn,

    "SELECT system_logs.*,
            users.username

    FROM system_logs

    LEFT JOIN users
    ON users.id = system_logs.user_id

    $where

    ORDER BY system_logs.id DESC"

    );





    $data = [];

    while($row = mysqli_fetch_assoc($query)){

        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}





/*
|---------------------------------------
| READ INSTRUCTOR STUDENTS
|---------------------------------------
*/

elseif($action == "read_instructor_students"){

    $user_id =
    $_SESSION['user_id'];


    /*
    GET INSTRUCTOR ID
    */
    $instructorQuery = mysqli_query($conn,

    "SELECT id
     FROM instructors
     WHERE user_id='$user_id'");

    $instructor =
    mysqli_fetch_assoc($instructorQuery);





    if(!$instructor){

        echo json_encode([]);
        exit;
    }

    $instructor_id = $instructor['id'];





    /*
    GET STUDENTS
    */
    $query = mysqli_query($conn,

    "SELECT DISTINCT

        students.id,
        students.student_code,
        students.first_name,
        students.last_name,
        students.gender,
        students.photo,

        departments.department_name,
        classes.class_name,

        subjects.subject_name

    FROM class_subjects

    INNER JOIN classes
    ON classes.id = class_subjects.class_id

    INNER JOIN subjects
    ON subjects.id = class_subjects.subject_id

    INNER JOIN enrollments
    ON enrollments.class_id = classes.id

    INNER JOIN students
    ON students.id = enrollments.student_id

    LEFT JOIN departments
    ON departments.id = students.department_id

    WHERE class_subjects.instructor_id='$instructor_id'

    ORDER BY students.id DESC"

    );





    $data = [];

    while($row = mysqli_fetch_assoc($query)){

        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}





/* ===================================
   GET STUDENT FACE RECORDS
=================================== */
elseif($action == "get_student_faces"){

    $student_code =
    mysqli_real_escape_string(
        $conn,
        $_GET['student_code']
    );

    $student =
    mysqli_fetch_assoc(

        mysqli_query(
            $conn,

            "SELECT id
             FROM students
             WHERE student_code='$student_code'"
        )
    );

    $student_id = $student['id'];

    $query = mysqli_query(

        $conn,

        "SELECT *
         FROM face_data
         WHERE student_id='$student_id'
         ORDER BY id DESC"
    );

    $faces = [];

    while($row = mysqli_fetch_assoc($query)){

        $faces[] = $row;
    }

    echo json_encode($faces);
    exit;
}



/* ===================================
   DELETE FACE
=================================== */
elseif($action == "delete_face"){

    $id = intval($_GET['id']);

    mysqli_query(
        $conn,

        "DELETE FROM face_data
         WHERE id='$id'"
    );

    echo json_encode([

        "status" => "success",
        "message" => "Face deleted successfully"

    ]);

    exit;
}


/* ===================================
   DELETE ALL FACES
=================================== */
elseif($action == "delete_all_faces"){

    $student_code =
    mysqli_real_escape_string(
        $conn,
        $_GET['student_code']
    );

    $student =
    mysqli_fetch_assoc(

        mysqli_query(

            $conn,

            "SELECT id
             FROM students
             WHERE student_code='$student_code'"
        )
    );

    $student_id = $student['id'];

    mysqli_query(

        $conn,

        "DELETE FROM face_data
         WHERE student_id='$student_id'"
    );

    echo json_encode([

        "status" => "success",
        "message" => "All faces deleted successfully"

    ]);

    exit;
}






/* ---------------------------------
   MY PROFILE
----------------------------------*/
elseif($action == "my_profile"){

    $id = $_SESSION['user_id'];

    $query = mysqli_query($conn,

    "SELECT * FROM users
     WHERE id='$id'");

    echo json_encode(
        mysqli_fetch_assoc($query)
    );

}




/* ---------------------------------
   UPDATE MY PROFILE
----------------------------------*/
elseif($action == "update_my_profile"){

    $id = $_SESSION['user_id'];

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $address = trim($_POST['address']);
    $bio = trim($_POST['bio']);

    /*
    CHECK DUPLICATES
    */
    $checkUsername = mysqli_query($conn,

    "SELECT id FROM users
     WHERE username='$username'
     AND id != '$id'");

    if(mysqli_num_rows($checkUsername) > 0){

        echo json_encode([
            "status"=>"error",
            "message"=>"Username already exists"
        ]);

        exit;
    }

    $checkEmail = mysqli_query($conn,

    "SELECT id FROM users
     WHERE email='$email'
     AND id != '$id'");

    if(mysqli_num_rows($checkEmail) > 0){

        echo json_encode([
            "status"=>"error",
            "message"=>"Email already exists"
        ]);

        exit;
    }

    /*
    PHOTO
    */
    $photoSQL = "";

    if(isset($_FILES['photo'])
    && $_FILES['photo']['name'] != ""){

        $photo =
        time() . "_" .
        $_FILES['photo']['name'];

        move_uploaded_file(

            $_FILES['photo']['tmp_name'],

            "../uploads/profiles/" . $photo
        );

        $photoSQL =
        ", profile_photo='$photo'";
    }

    mysqli_query($conn,

    "UPDATE users SET

        first_name='$first_name',
        last_name='$last_name',
        username='$username',
        email='$email',
        phone='$phone',
        gender='$gender',
        date_of_birth='$date_of_birth',
        address='$address',
        bio='$bio'

        $photoSQL

     WHERE id='$id'");

    echo json_encode([
        "status"=>"success",
        "message"=>"Profile updated successfully",
        "username"=>$first_name ?: $username
    ]);

}




/* ---------------------------------
   CHANGE MY PASSWORD
----------------------------------*/
elseif($action == "change_my_password"){

    $id = $_SESSION['user_id'];

    $current_password =
    $_POST['current_password'];

    $new_password =
    $_POST['new_password'];

    $query = mysqli_query($conn,

    "SELECT password FROM users
     WHERE id='$id'");

    $user = mysqli_fetch_assoc($query);

    if(!password_verify(
        $current_password,
        $user['password']
    )){

        echo json_encode([
            "status"=>"error",
            "message"=>"Current password incorrect"
        ]);

        exit;
    }

    $hashed =
    password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );

    mysqli_query($conn,

    "UPDATE users SET
     password='$hashed'
     WHERE id='$id'");

    echo json_encode([
        "status"=>"success",
        "message"=>"Password changed successfully"
    ]);

}


mysqli_close($conn);
?>



