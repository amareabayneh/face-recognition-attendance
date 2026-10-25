<?php require_once("backend/db.php"); ?>

<!DOCTYPE html>
<html>
<head>

<title>Contact Us - Student Attendance System</title>

<link href="static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="static/vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="static/css/style.css">

<script src="static/js/sweetalert2.all.min.js"></script>

</head>

<body>

<?php include 'navbar.php'; ?>






<!-- ================= CONTACT SECTION ================= -->
<section class="container mt-5" style="padding-top: 80px;">

<h2 class="text-center mb-4">Contact Us</h2>

<div class="row">




    <!-- FORM -->
    <div class="col-md-6">

        <form id="contactForm">

            <div class="mb-3">

                <input type="text"
                       class="form-control"
                       name="full_name"
                       placeholder="Full Name">

                <small class="text-danger full_name_error"></small>

            </div>





            <div class="mb-3">

                <input type="email"
                       class="form-control"
                       name="email"
                       placeholder="Email">

                <small class="text-danger email_error"></small>

            </div>





            <div class="mb-3">

                <input type="text"
                       class="form-control"
                       name="subject"
                       placeholder="Subject">

                <small class="text-danger subject_error"></small>

            </div>





            <div class="mb-3">

                <textarea class="form-control"
                          name="message"
                          rows="5"
                          placeholder="Message"></textarea>

                <small class="text-danger message_error"></small>

            </div>





            <button class="btn btn-warning w-100">
                Send Message
            </button>

        </form>

    </div>





    <!-- CONTACT INFO -->
    <div class="col-md-6">

        <h4>Our Office</h4>

        <div id="contactInfoBox"></div>

    </div>

</div>

</section>






<?php include 'footer.html'; ?>

<script src="static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="static/vendor/sweetalert/js/sweetalert2.all.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function(){





/*
|--------------------------------------------------------------------------
| LOAD CONTACT INFO
|--------------------------------------------------------------------------
*/

fetch("backend/process.php", {

    method: "POST",
    body: new URLSearchParams({action: "get_contact_info"})

})

.then(res => res.json())
// .then(res => console.log(res))

.then(data => {

    if(data){

        document.getElementById("contactInfoBox").innerHTML = `

            <p><i class="bi bi-geo-alt"></i> ${data.office_address}</p>

            <p><i class="bi bi-envelope"></i> ${data.email}</p>

            <p><i class="bi bi-phone"></i> ${data.phone}</p>

        `;
    }

});






/*
|--------------------------------------------------------------------------
| SEND MESSAGE (AJAX)
|--------------------------------------------------------------------------
*/

document.getElementById("contactForm")
.addEventListener("submit", async function(e){

    e.preventDefault();

    document.querySelector(".full_name_error").innerHTML = "";
    document.querySelector(".email_error").innerHTML = "";
    document.querySelector(".subject_error").innerHTML = "";
    document.querySelector(".message_error").innerHTML = "";

    let formData = new FormData(this);

    formData.append("action", "send_contact_message");

    let response = await fetch("backend/process.php", {

        method: "POST",
        body: formData

    });

    let result = await response.json();





    if(result.status == "success"){

        Swal.fire({
            icon: "success",
            title: "Success",
            text: result.message
        });

        this.reset();

    } else {

        if(result.errors.full_name){
            document.querySelector(".full_name_error").innerHTML = result.errors.full_name;
        }

        if(result.errors.email){
            document.querySelector(".email_error").innerHTML = result.errors.email;
        }

        if(result.errors.subject){
            document.querySelector(".subject_error").innerHTML = result.errors.subject;
        }

        if(result.errors.message){
            document.querySelector(".message_error").innerHTML = result.errors.message;
        }

    }

});





});

</script>

</body>
</html>
