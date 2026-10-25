<?php require_once("backend/db.php"); ?>

<!DOCTYPE html>
<html>
<head>

<title>About Us - Student Attendance System</title>

<link href="static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="static/vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="static/css/style.css">

</head>

<body>

<?php include 'navbar.php'; ?>



<!-- ================= ABOUT SECTION ================= -->
<section class="container mt-5" style="padding-top: 80px;">

	<div class="row align-items-center">

	    <div class="col-md-6">

	        <h2>About Us</h2>

	        <h4 id="aboutTitle"></h4>

	        <p id="aboutDescription"></p>

	        <hr>

	        <h5>Our Mission</h5>

	        <p id="aboutMission"></p>

	        <h5>Our Vision</h5>

	        <p id="aboutVision"></p>

	    </div>





	    <div class="col-md-6">

	        <img id="aboutImage"
	             class="img-fluid rounded shadow"
	             alt="About Image">

	    </div>

	</div>

</section>



<?php include 'footer.html'; ?>

<script src="static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function(){





/*
|--------------------------------------------------------------------------
| LOAD ABOUT DATA
|--------------------------------------------------------------------------
*/

fetch("backend/process.php", {

    method: "POST",
    body: new URLSearchParams({action: "get_about"})

})

.then(res => res.json())

.then(data => {

    if(data){

        document.getElementById("aboutTitle").innerText = data.title;
        document.getElementById("aboutDescription").innerText = data.description;
        document.getElementById("aboutMission").innerText = data.mission;
        document.getElementById("aboutVision").innerText = data.vision;

        document.getElementById("aboutImage").src =
            "uploads/about/" + data.image;

    }

});





});

</script>

</body>
</html>
