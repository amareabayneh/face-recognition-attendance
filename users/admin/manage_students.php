<?php
session_start();
require_once("../../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Students</title>

    <link href="../../static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../static/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../static/css/style.css">

<style> 
    /* NAVBAR */
    .navbar{
        position: relative;
        z-index: 1000;
        overflow: visible !important;
    }

    /* DROPDOWN */
    .dropdown{
        position: relative;
    }

    .dropdown-menu{
        z-index: 3000 !important;
        position: absolute !important;
    }

    /* CONTENT */
    .dashboard-content{
        overflow: visible !important;
    }



</style>


</head>

<body>

<!-- SIDEBAR -->
<?php include 'sidebar.php'; ?>


<!-- MAIN CONTENT -->
<div class="dashboard-content">

    <nav class="navbar bg-light shadow-sm d-flex justify-content-between px-3">

        <div class="d-flex align-items-center">
            <i id="sidebarToggle"
               class="bi bi-filter-right fs-1 me-3"
               style="cursor:pointer;"></i>

            <span>Manage Students</span>
        </div>

        <div class="dropdown">

            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">

                <img id="navProfilePhoto"
                     src="../../static/img/default.jpg"
                     width="40"
                     height="40"
                     class="rounded-circle border object-fit-cover">


                <span id="navUserName">
                    Admin
                </span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <a class="dropdown-item"
                       href="#"
                       onclick="openProfileModal()">

                        <i class="bi bi-person"></i>
                        My Profile

                    </a>
                </li>

                <li>
                    <a class="dropdown-item"
                       href="#"
                       onclick="openChangePasswordModal()">

                        <i class="bi bi-key"></i>
                        Change Password

                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item text-danger"
                       href="../../auth/logout.php">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </a>
                </li>

            </ul>

        </div>

    </nav>

    <div class="container mt-4">

        <div style="float:right;">
            <button class="btn btn-primary mb-3"
                    onclick="openCreateModal()"
                    data-bs-toggle="modal"
                    data-bs-target="#studentModal">

                + Add Student

            </button>
        </div>

        <table class="table table-bordered">

            <thead>

            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Student Code</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Department</th>
                <th>Class</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            </thead>

            <tbody id="studentTable"></tbody>

        </table>

    </div>

</div>

<!-- STUDENT MODAL -->
<div class="modal fade" id="studentModal" data-bs-backdrop='static'>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">
                <h5 id="modalTitle">Add Student</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="student_id">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>First Name</label>

                        <input type="text"
                               id="first_name"
                               class="form-control">

                        <small class="text-danger"
                               id="first_name_error"></small>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Last Name</label>

                        <input type="text"
                               id="last_name"
                               class="form-control">

                        <small class="text-danger"
                               id="last_name_error"></small>

                    </div>

                </div>

                <div class="row">

                    <!-- <div class="col-md-6 mb-3">
                        <label>Student Code</label>
                        <input type="text"
                               id="student_code"
                               class="form-control">
                        <small class="text-danger"
                               id="student_code_error"></small>
                    </div> -->

                    <div class="col-md-6 mb-3">

                        <label>Gender</label>

                        <select id="gender"
                                class="form-control">

                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>

                        </select>

                        <small class="text-danger"
                               id="gender_error"></small>

                    </div>
                    <div class="col-md-6 mb-3">

                        <label>Date of Birth</label>

                        <input type="date"
                               id="date_of_birth"
                               class="form-control">

                    </div>

                </div>


                <div class="row">


                    <div class="col-md-6 mb-3">
                        <div class="mb-3">

                            <label>Address</label>

                            <textarea id="address"
                                      class="form-control"></textarea>

                        </div>
                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Enrollment Year</label>

                        <input type="number"
                               id="enrollment_year"
                               class="form-control" required>

                        <small class="text-danger"
                               id="enrollment_year_error"></small>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Department</label>

                        <select id="department_id"
                                class="form-control" required></select>
                        <small class="text-danger"
                               id="department_error"></small>
                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Class</label>

                        <select id="class_id"
                                class="form-control" required></select>
                        <small class="text-danger"
                               id="class_error"></small>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Status</label>

                        <select id="status"
                                class="form-control">

                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Graduated">Graduated</option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Photo</label>

                        <input type="file"
                               id="photo"
                               class="form-control">

                        <small class="text-danger"
                               id="photo_error"></small>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Close

                </button>

                <button class="btn btn-success"
                        onclick="saveStudent()">

                    Save

                </button>

            </div>

        </div>

    </div>

</div>

<!-- FACE CAPTURE MODAL -->
<div class="modal fade" id="faceModal" data-bs-backdrop='static'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Capture Student Face</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">

                <input type="hidden"
                       id="face_student_code">

                <div style="position:relative; width:100%;">

                    <video id="video"
                           width="100%"
                           height="400"
                           autoplay
                           muted
                           style="
                                border-radius:10px;
                                background:black;
                                object-fit:cover;
                                ">
                    </video>

                    <canvas id="overlay" 
                            style="
                                position:absolute;
                                top:0;
                                left:0;
                                width:100%;
                                height:100%;
                                z-index:10;
                            "
                    >
                    </canvas>

                </div>

                <div class="d-flex justify-content-between">

                    <div class="mt-3 w-50">

                        <select id="capture_type"
                                class="form-control">

                            <option value="Front">
                                Front Face
                            </option>

                            <option value="Left">
                                Left Face
                            </option>

                            <option value="Right">
                                Right Face
                            </option>

                            <option value="Up">
                                Up Face
                            </option>

                            <option value="Smile">
                                Smiling
                            </option>

                        </select>

                    </div>

                    <div class="mt-3">

                        Captured:
                        <span id="captureCount">0</span>

                    </div>

                    <div class="mt-3">

                        <button class="btn btn-primary"
                                onclick="startCamera()">

                            Open Camera

                        </button>

                        <button class="btn btn-success"
                                onclick="captureFaceData()">

                            Capture Face

                        </button>

                    </div>

                </div>

            </div>

            <!-- <div class="modal-footer">

                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div> -->

        </div>

    </div>

</div>



<!-- FACE MANAGEMENT MODAL -->
<div class="modal fade"
     id="faceManagementModal"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5>
                    Manage Student Faces
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="d-flex justify-content-between mb-3">

                    <div>
                        Student:
                        <strong id="faceStudentName"></strong>
                    </div>

                    <div>
                        Total Faces:
                        <span class="badge bg-primary"
                              id="totalFaceRecords">
                              0
                        </span>
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>

                        <tr>
                            <th>ID</th>
                            <th>Capture Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>

                        </thead>

                        <tbody id="faceTableBody"></tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-danger"
                        onclick="deleteAllFaces()">

                    Delete All Faces

                </button>

                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>



<button id="scrollTop">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>
<script src="../../static/vendor/face/js/face-api.min.js"></script>

<script src="../../static/js/main.js"></script>

<?php include 'profile.html'; ?>


<script>


    let cameraStream = null;
    let totalCaptures = 0;
    let detectionInterval;
    let currentFaceStudentCode = "";

    Promise.all([

        faceapi.nets.tinyFaceDetector.loadFromUri('../../models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('../../models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('../../models')

    ]).then(() => {

        console.log("Face API Loaded");

    });


    function captureFace(studentCode){
        console.log(studentCode)
        document.getElementById("face_student_code").value =
        studentCode;

        document.getElementById("captureCount").innerText = 0;

        new bootstrap.Modal(
            document.getElementById("faceModal")
        ).show();

    }

    async function startCamera(){

        const video =
        document.getElementById("video");

        cameraStream =
        await navigator.mediaDevices.getUserMedia({

            video:{
                width:720,
                height:400
            }
        });

        video.srcObject = cameraStream;

    }


    document.getElementById("video").addEventListener("play", () => {

        const video =
        document.getElementById("video");

        const canvas =
        document.getElementById("overlay");

        const displaySize = {

            width: video.videoWidth,
            height: video.videoHeight
        };

        faceapi.matchDimensions(
            canvas,
            displaySize
        );

        detectionInterval = setInterval(async () => {

            const detections =

            await faceapi

            .detectAllFaces(
                video,
                new faceapi.TinyFaceDetectorOptions({
                    inputSize:224,
                    scoreThreshold:0.5
                })
            )

            .withFaceLandmarks();

            const resizedDetections =

            faceapi.resizeResults(
                detections,
                displaySize
            );

            const ctx =
            canvas.getContext("2d");

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );

            /*
            DRAW FACE BOX
            */
            // faceapi.draw.drawDetections(
            //     canvas,
            //     resizedDetections
            // );
            resizedDetections.forEach(detection => {

                const box =
                detection.detection.box;

                ctx.beginPath();

                ctx.lineWidth = 3;

                ctx.strokeStyle = "#00ff00";

                ctx.rect(
                    box.x,
                    box.y,
                    box.width,
                    box.height
                );

                ctx.stroke();

            });


            /*
            DRAW LANDMARKS
            */
            faceapi.draw.drawFaceLandmarks(
                canvas,
                resizedDetections
            );

            /*
            FACE STATUS
            */
            resizedDetections.forEach(detection => {

                const box =
                detection.detection.box;

                /*
                CONFIDENCE
                */
                const confidence = Math.round(
                    detection.detection.score * 100
                );

                /*
                LABEL TEXT
                */
                const label =
                `Face Detected (${confidence}%)`;

                /*
                FONT
                */
                ctx.font = "bold 18px Arial";

                /*
                TEXT WIDTH
                */
                const textWidth =
                ctx.measureText(label).width;

                /*
                LABEL POSITION
                */
                let x = box.x;
                let y = box.y - 15;

                /*
                PREVENT TEXT GOING OUTSIDE TOP
                */
                if(y < 30){
                    y = box.y + 25;
                }

                /*
                BACKGROUND
                */
                ctx.fillStyle =
                "rgba(0,0,0,0.7)";

                ctx.fillRect(
                    x - 5,
                    y - 20,
                    textWidth + 12,
                    28
                );

                /*
                TEXT COLOR
                */
                ctx.fillStyle = "#00ff00";

                /*
                DRAW TEXT
                */
                ctx.fillText(
                    label,
                    x,
                    y
                );

            });


        }, 100);

    });




   

    async function captureFaceData(){

        const video =
        document.getElementById("video");

        if(!video.srcObject){

            Swal.fire(
                "Camera not started",
                "",
                "warning"
            );

            return;
        }


        const detections =
        await faceapi

        .detectSingleFace(
            video,
            new faceapi.TinyFaceDetectorOptions()
        )

        .withFaceLandmarks()
        .withFaceDescriptor();

        if(!detections){

            Swal.fire(
                "No Face Detected",
                "",
                "warning"
            );

            return;
        }

        const descriptor =
        Array.from(detections.descriptor);

        const studentCode =
        document.getElementById("face_student_code").value;

        let formData = new FormData();

        formData.append("action", "save_face_data");
        formData.append("student_code", studentCode);
        formData.append("descriptor", JSON.stringify(descriptor));
        formData.append("capture_type", document.getElementById("capture_type").value);



        fetch("../../backend/process.php",{
            method:"POST",
            body:formData
        })

        .then(async res => {

            const text = await res.text();

            console.log(text);

            return JSON.parse(text);

        })

        .then(data => {

            if(data.status == "error"){

                Swal.fire(
                    "Error",
                    data.message,
                    "error"
                );

                return;
            }

            totalCaptures++;

            document.getElementById("captureCount")
            .innerText = totalCaptures;

            Swal.fire(
                "Success",
                data.message,
                "success"
            );

        });


    }



/* --------------------------------
   MANAGE FACES
-------------------------------- */
function manageFaces(studentCode, studentName){

    currentFaceStudentCode = studentCode;

    document.getElementById("faceStudentName")
    .innerText = studentName;

    loadFaceRecords();

    new bootstrap.Modal(
        document.getElementById("faceManagementModal")
    ).show();

}

/* --------------------------------
   LOAD FACE RECORDS
-------------------------------- */
function loadFaceRecords(){

    fetch(
    "../../backend/process.php?action=get_student_faces&student_code="
    + currentFaceStudentCode)

    .then(res => res.json())

    .then(data => {

        let rows = "";

        if(data.length == 0){

            rows = `
            <tr>
                <td colspan="4"
                    class="text-center">

                    No face data found

                </td>
            </tr>
            `;
        }

        data.forEach(face => {

            rows += `

            <tr>

                <td>${face.id}</td>

                <td>
                    <span class="badge bg-primary">
                        ${face.capture_type}
                    </span>
                </td>

                <td>${face.created_at}</td>

                <td>

                    <button class="btn btn-sm btn-danger"
                    onclick="deleteFace(${face.id})">

                        Delete

                    </button>

                </td>

            </tr>

            `;

        });

        document.getElementById("faceTableBody")
        .innerHTML = rows;

        document.getElementById("totalFaceRecords")
        .innerText = data.length;

    });

}

/* --------------------------------
   DELETE SINGLE FACE
-------------------------------- */
function deleteFace(faceId){

    Swal.fire({

        title: "Delete Face?",
        text: "This face data will be removed",
        icon: "warning",
        showCancelButton: true

    })

    .then(result => {

        if(result.isConfirmed){

            fetch(
            "../../backend/process.php?action=delete_face&id="
            + faceId)

            .then(res => res.json())

            .then(data => {

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadFaceRecords();

            });

        }

    });

}

/* --------------------------------
   DELETE ALL FACES
-------------------------------- */
function deleteAllFaces(){

    Swal.fire({

        title: "Delete All Faces?",
        text: "All face records will be deleted",
        icon: "warning",
        showCancelButton: true

    })

    .then(result => {

        if(result.isConfirmed){

            fetch(
            "../../backend/process.php?action=delete_all_faces&student_code="
            + currentFaceStudentCode)

            .then(res => res.json())

            .then(data => {

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadFaceRecords();

            });

        }

    });

}






    function stopCamera(){

        const video =
        document.getElementById("video");

        if(cameraStream){

            cameraStream
            .getTracks()
            .forEach(track => track.stop());

            video.srcObject = null;

            cameraStream = null;
        }

    }

    document.getElementById("faceModal").addEventListener("hidden.bs.modal", function () {

        stopCamera();

        clearInterval(
            detectionInterval
        );

        const canvas =
        document.getElementById("overlay");

        const ctx =
        canvas.getContext("2d");

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        totalCaptures = 0;

        document.getElementById(
            "captureCount"
        ).innerText = 0;

    });




/* -----------------------------
   CLEAR ERRORS
------------------------------*/
function clearErrors(){

    document.querySelectorAll("small")
    .forEach(el => el.innerText = "");

}

/* -----------------------------
   LOAD STUDENTS
------------------------------*/
function loadStudents(){

    fetch("../../backend/process.php?action=read_students")

    .then(res => res.json())
    // .then(res => console.log(res))

    .then(data => {

        let rows = "";

        data.forEach(student => {
            // console.log(student.id)

            rows += `

            <tr>

                <td>${student.id}</td>

                <td>

                    <img src="../../uploads/students/${student.photo}"

                         width="50"
                         height="50"
                         style="object-fit:cover; border-radius:50%;">

                </td>

                <td>${student.student_code}</td>

                <td>
                    ${student.first_name}
                    ${student.last_name}
                </td>

                <td>${student.gender}</td>

                <td>${student.department_name ?? ''}</td>

                <td>${student.class_name ?? ''}</td>

                <td>${student.status}</td>

                <td>


                    <button class="btn btn-sm btn-info"
                    onclick="captureFace('${student.student_code}')">

                        Capture

                    </button>

                    <button class="btn btn-sm btn-dark"
                    onclick="manageFaces(
                        '${student.student_code}',
                        '${student.first_name} ${student.last_name}'
                    )">

                        Faces

                    </button>



                    <button class="btn btn-sm btn-warning"
                    onclick="editStudent(${student.id})">

                        Edit

                    </button>

                    <button class="btn btn-sm btn-danger"
                    onclick="deleteStudent(${student.id})">

                        Delete

                    </button>

                </td>


            </tr>

            `;

        });

        document.getElementById("studentTable")
        .innerHTML = rows;

    });

}

/* -----------------------------
   LOAD DEPARTMENTS
------------------------------*/
function loadDepartments(){

    fetch("../../backend/process.php?action=departments")

    .then(res => res.json())

    .then(data => {

        let options =
        `<option value="">Select Department</option>`;

        data.forEach(department => {

            options += `

            <option value="${department.id}">
                ${department.department_name}
            </option>

            `;

        });

        document.getElementById("department_id")
        .innerHTML = options;

    });

}

/* -----------------------------
   LOAD CLASSES
------------------------------*/
function loadClasses(){

    fetch("../../backend/process.php?action=classes")

    .then(res => res.json())

    .then(data => {

        let options =
        `<option value="">Select Class</option>`;

        data.forEach(classItem => {

            options += `

            <option value="${classItem.id}">
                ${classItem.class_name}
            </option>

            `;

        });

        document.getElementById("class_id")
        .innerHTML = options;

    });

}

/* -----------------------------
   OPEN CREATE MODAL
------------------------------*/
function openCreateModal(){

    clearErrors();

    document.getElementById("modalTitle")
    .innerText = "Add Student";

    document.getElementById("student_id").value = "";

    // document.getElementById("student_code").value = "";

    document.getElementById("first_name").value = "";

    document.getElementById("last_name").value = "";

    document.getElementById("gender").value = "";

    document.getElementById("date_of_birth").value = "";

    document.getElementById("address").value = "";

    document.getElementById("department_id").value = "";

    document.getElementById("class_id").value = "";

    document.getElementById("enrollment_year").value = "";

    document.getElementById("status").value = "Active";

    document.getElementById("photo").value = "";

}

/* -----------------------------
   SAVE STUDENT
------------------------------*/
function saveStudent(){

    clearErrors();

    let id =
    document.getElementById("student_id").value;

    // let student_code =document.getElementById("student_code").value.trim();

    let first_name =
    document.getElementById("first_name").value.trim();

    let last_name =
    document.getElementById("last_name").value.trim();

    let gender =
    document.getElementById("gender").value;

    let enrollment_year =
    document.getElementById("enrollment_year").value;

    let department_id =
    document.getElementById("department_id").value;

    let class_id =
    document.getElementById("class_id").value;



    let hasError = false;


    if(first_name == ""){

        document.getElementById("first_name_error")
        .innerText = "First name is required";

        hasError = true;
    }

    if(last_name == ""){

        document.getElementById("last_name_error")
        .innerText = "Last name is required";

        hasError = true;
    }

    if(gender == ""){

        document.getElementById("gender_error")
        .innerText = "Gender is required";

        hasError = true;
    }



    if(enrollment_year == ""){

        document.getElementById("enrollment_year_error")
        .innerText = "Enrollment year is required";

        hasError = true;
    }

    if(department_id == ""){

        document.getElementById("department_error")
        .innerText = "Department is required";

        hasError = true;
    }

    if(class_id == ""){

        document.getElementById("class_error")
        .innerText = "class is required";

        hasError = true;
    }

    if(hasError){
        return;
    }

    let formData = new FormData();

    formData.append("id", id);

    // formData.append("student_code", student_code);

    formData.append("first_name", first_name);

    formData.append("last_name", last_name);

    formData.append("gender", gender);

    formData.append(
        "date_of_birth",
        document.getElementById("date_of_birth").value
    );

    formData.append(
        "address",
        document.getElementById("address").value
    );

    formData.append(
        "department_id",
        department_id
    );

    formData.append(
        "class_id",
        class_id
    );

    formData.append(
        "enrollment_year",
        enrollment_year
    );

    formData.append(
        "status",
        document.getElementById("status").value
    );

    formData.append(
        "photo",
        document.getElementById("photo").files[0]
    );

    let action = id
    ? "update_student"
    : "create_student";

    formData.append("action", action);

    fetch("../../backend/process.php",{

        method:"POST",
        body:formData

    })

    .then(res => res.json())
    // .then(res => console.log(res))
    .then(data => {

        if(data.status == "error"){

            Swal.fire("Error", data.message, "error");
            return;
        }

        Swal.fire(
            "Success",
            data.message,
            "success"
        );

        bootstrap.Modal
        .getInstance(
            document.getElementById("studentModal")
        ).hide();

        loadStudents();

    });

}

/* -----------------------------
   EDIT STUDENT
------------------------------*/
function editStudent(id){

    clearErrors();

    fetch(
    "../../backend/process.php?action=single_student&id="
    + id)

    .then(res => res.json())

    .then(student => {

        document.getElementById("modalTitle")
        .innerText = "Edit Student";

        document.getElementById("student_id").value =
        student.id;

        // document.getElementById("student_code").value =student.student_code;

        document.getElementById("first_name").value =
        student.first_name;

        document.getElementById("last_name").value =
        student.last_name;

        document.getElementById("gender").value =
        student.gender;

        document.getElementById("date_of_birth").value =
        student.date_of_birth;

        document.getElementById("address").value =
        student.address;

        document.getElementById("department_id").value =
        student.department_id;

        document.getElementById("class_id").value =
        student.class_id;

        document.getElementById("enrollment_year").value =
        student.enrollment_year;

        document.getElementById("status").value =
        student.status;

        new bootstrap.Modal(
            document.getElementById("studentModal")
        ).show();

    });

}

/* -----------------------------
   DELETE STUDENT
------------------------------*/
function deleteStudent(id){

    Swal.fire({

        title: "Delete Student?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true

    })

    .then(result => {

        if(result.isConfirmed){

            fetch(
            "../../backend/process.php?action=delete_student&id="
            + id)

            .then(res => res.json())

            .then(data => {

                Swal.fire(
                    "Deleted",
                    data.message,
                    "success"
                );

                loadStudents();

            });

        }

    });

}

/* INIT */
loadStudents();
loadDepartments();
loadClasses();

</script>

</body>
</html>
