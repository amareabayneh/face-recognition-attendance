<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>

    <title>Smart Student Attendance System</title>

    <link href="../static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../static/css/style.css">

    <link rel="stylesheet" href="../static/vendor/bootstrap-icons/bootstrap-icons.css">



</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a href="../index.php" class="navbar-brand text-white">
      <span class="text-yellow">Smart </span> Student Attendance System
    </a>

    <button class="navbar-toggler bg-warning"
            data-bs-toggle="collapse"
            data-bs-target="#nav">

      <span class="navbar-toggler-icon"></span>

    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="../index.php">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../services.php">Services</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../about.php">About</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../contact.php">Contact</a>
        </li>

        <li class="nav-item">
          <a class="nav-link active" href="login.php">Login</a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<section class="container mt-5">

    <div class="login-card card">

        <div class="card-body">

            <h3 class="text-center mb-4">Login</h3>

            <form id="loginForm">

                <!-- Email -->
                <input type="email"
                       name="email"
                       id="email"
                       class="form-control mb-1"
                       placeholder="Email">

                <small id="emailError"
                       class="text-danger"></small>

                <!-- Password -->
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control mb-1 mt-3"
                       placeholder="Password">

                <small id="passwordError"
                       class="text-danger"></small>

                <button type="submit"
                        class="btn btn-warning w-100 mt-3">

                    Login

                </button>

            </form>

        </div>

    </div>

</section>

<!-- Footer -->
<footer class="footer mt-5">

  <div class="container py-3">

    <div class="row">

      <div class="col-md-4">
        <h4>
          <span class="text-yellow">Smart </span>
          Student Attendance System
        </h4>

        <p>
          Student Attendance System is a modern platform
          for Handling student attendance.
        </p>
      </div>

      <div class="col-md-3">

        <h5>Quick Links</h5>

        <a href="../index.php">Home</a><br>
        <a href="../services.php">Services</a><br>
        <a href="../about.php">About</a><br>
        <a href="../contact.php">Contact</a>

      </div>

      <div class="col-md-3">

        <h5>Contact</h5>

        <p>
          <i class="bi bi-geo-alt"></i>
          Addis Ababa, Ethiopia
        </p>

        <p>
          <i class="bi bi-envelope"></i>
          info@smartattendance.com
        </p>

        <p>
          <i class="bi bi-phone"></i>
          +251900000000
        </p>

      </div>

      <div class="col-md-2">

        <h5>Follow Us</h5>

        <a href="#"><i class="bi bi-facebook"></i> Facebook</a><br>
        <a href="#"><i class="bi bi-twitter"></i> Twitter</a><br>
        <a href="#"><i class="bi bi-instagram"></i> Instagram</a>

      </div>

    </div>

    <hr>

    <p class="text-center mb-3">
      © 2026 Smart Attendance System.
      All rights reserved.
    </p>

    <p class="text-center">
      Developed By:
      <a href="#">
        <span class="text-yellow">
          Anjones Tech Solution PLC
        </span>
      </a>
    </p>

  </div>

</footer>

<button id="scrollTop">
  <i class="bi bi-arrow-up"></i>
</button>

<script src="../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert -->
<script src="../static/vendor/sweetalert/js/sweetalert2.all.js"></script>
<script src="../static/js/main.js"></script>

<!-- LOGIN AJAX -->
<script>

document.getElementById("loginForm").addEventListener("submit", async function(e){

    e.preventDefault();

    // Clear Errors
    document.getElementById("emailError").innerHTML = "";
    document.getElementById("passwordError").innerHTML = "";

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    let hasError = false;

    // Validation
    if(email == ""){

        document.getElementById("emailError")
        .innerHTML = "Email is required";

        hasError = true;
    }

    if(password == ""){

        document.getElementById("passwordError")
        .innerHTML = "Password is required";

        hasError = true;
    }

    if(hasError){
        return;
    }

    // FormData
    let formData = new FormData();

    formData.append("action", "login");
    formData.append("email", email);
    formData.append("password", password);

    try{

        let response = await fetch("../backend/process.php", {

            method: "POST",
            body: formData

        });

        let data = await response.json();
        // console.log(data.status)
        // console.log(data.message)
        // console.log(data.redirect)
        // console.log(data)

        if(data.status == "success"){

            Swal.fire({
                icon: "success",
                title: "Success",
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });

            setTimeout(() => {

                window.location.href = data.redirect;

            }, 2000);

        }else{

            if(data.email_error){

                document.getElementById("emailError")
                .innerHTML = data.email_error;

            }

            if(data.password_error){

                document.getElementById("passwordError")
                .innerHTML = data.password_error;

            }

            if(data.message){

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.message
                });

            }

        }

    }catch(error){

        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Something went wrong"
        });

    }

});

</script>

</body>
</html>
