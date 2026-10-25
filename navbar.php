<?php

$current_path = $_SERVER['PHP_SELF'];

function active($path){

    global $current_path;

    return strpos($current_path, $path) !== false
           ? 'active'
           : '';
}
?>

<style>


/* NAVBAR BRAND */
.navbar-brand{
    font-size: 1.5rem;
    white-space: nowrap;
    transition: 0.3s;
}

/* TABLET */
@media (max-width: 768px){

    .navbar-brand{
        font-size: 1.2rem;
    }

}

/* MOBILE */
@media (max-width: 576px){

    .navbar-brand{
        font-size: 1rem;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

}

</style>



<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a href="index.php"
       class="navbar-brand text-white fw-bold">

        <span class="text-yellow">Smart</span> Student
        Attendance System

    </a>

    <button class="navbar-toggler bg-warning" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">

        <li class="nav-item"><a class="nav-link <?= active('/index.php') ?>" href="index.php">Home</a></li>

        <li class="nav-item"><a class="nav-link <?= active('/services.php') ?>" href="services.php">Services</a></li>

        <li class="nav-item"><a class="nav-link <?= active('/about.php') ?>" href="about.php">About</a></li>

        <li class="nav-item"><a class="nav-link <?= active('/contact.php') ?>" href="contact.php">Contact</a></li>

        <li class="nav-item"><a class="nav-link <?= active('/auth/login.php') ?>" href="auth/login.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>