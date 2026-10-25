<?php
require_once("../../backend/db.php");

$session_id = $_GET['session_id'] ?? 0;
?>

<!DOCTYPE html>
<html>

<head>

    <title>Scan Attendance</title>

    <link href="../../static/vendor/bootstrap/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../../static/vendor/bootstrap-icons/bootstrap-icons.css">

    <link rel="stylesheet"
          href="../../static/css/style.css">

</head>

<body>

<div class="container-fluid mt-3">

    <div class="d-flex justify-content-between align-items-center">

        <h3>
            Face Recognition Attendance
        </h3>

        <a href="manage_attendance_sessions.php"
           class="btn btn-secondary">

            Back

        </a>

    </div>

    <hr>

    <input type="hidden"
           id="attendance_session_id"
           value="<?php echo $session_id; ?>">

    <div class="row">

        <!-- CAMERA -->
        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-body text-center">

                    <div style="position:relative; width:100%; height:500px;">

                        <video id="video"
                               autoplay
                               muted
                               width="100%"
                               height="500"
                               style="
                                    background:black;
                                    border-radius:10px;
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
                                ">
                        </canvas>

                    </div>


                </div>

            </div>

        </div>

        <!-- LIVE ATTENDANCE -->
        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-header">

                    <h5 class="mb-0">

                        Live Attendance

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <thead>

                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Time</th>
                        </tr>

                        </thead>

                        <tbody id="attendanceTable"></tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../../static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../../static/vendor/sweetalert/js/sweetalert2.all.js"></script>

<script src="../../static/vendor/face/js/face-api.min.js"></script>

<script>

let labeledDescriptors = [];

let faceMatcher;

let scannedStudents = [];

/* --------------------------------
   LOAD MODELS
---------------------------------*/
Promise.all([

    faceapi.nets.tinyFaceDetector.loadFromUri('../../models'),

    faceapi.nets.faceLandmark68Net.loadFromUri('../../models'),

    faceapi.nets.faceRecognitionNet.loadFromUri('../../models')

]).then(async () => {

    await loadFaceDescriptors();

    if(labeledDescriptors.length > 0){

        faceMatcher =
        new faceapi.FaceMatcher(
            labeledDescriptors,
            0.5
        );

        startCamera();

    }else{

        Swal.fire(
            "No Face Data Found",
            "Please capture student faces first",
            "warning"
        );

    }


});

/* --------------------------------
   LOAD FACE DESCRIPTORS
---------------------------------*/
async function loadFaceDescriptors(){

    const response =
    await fetch(
    "../../backend/process.php?action=load_face_descriptors"
    );

    const students =
    await response.json();

    const grouped = {};

    students.forEach(student => {

        if(!grouped[student.student_code]){

            grouped[student.student_code] = [];
        }

        grouped[student.student_code].push(

            new Float32Array(
                student.face_descriptor
            )

        );

    });

    labeledDescriptors = Object.keys(grouped)

    .map(studentCode => {

        return new faceapi.LabeledFaceDescriptors(

            studentCode,

            grouped[studentCode]

        );

    });


}

/* --------------------------------
   START CAMERA
---------------------------------*/
async function startCamera(){

    const video =
    document.getElementById("video");

    const stream =
    await navigator.mediaDevices.getUserMedia({

        video:{
            width: 320,
            height: 240,

            // width: 640,
            // height: 480
        }

    });

    video.srcObject = stream;

}

/* --------------------------------
   FACE RECOGNITION
---------------------------------*/
document.getElementById("video").addEventListener("play", () => {

    const video =
    document.getElementById("video");

    const canvas =
    document.getElementById("overlay");

    const displaySize = {

        width: video.clientWidth,
        height: video.clientHeight

    };

    faceapi.matchDimensions(
        canvas,
        displaySize
    );

    setInterval(async () => {

        const detections =

        await faceapi

        .detectAllFaces(

            video,

            new faceapi.TinyFaceDetectorOptions({

                inputSize:160,
                scoreThreshold:0.5

            })

        )

        .withFaceLandmarks()

        .withFaceDescriptors();

        const resizedDetections =

        faceapi.resizeResults(
            detections,
            displaySize
        );

        // CLEAR OLD DRAWINGS
        const ctx =
        canvas.getContext("2d");

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        resizedDetections.forEach(detection => {

            const match =

            faceMatcher.findBestMatch(
                detection.descriptor
            );

            const box =
            detection.detection.box;

            // DRAW BOX
            const drawBox =

            new faceapi.draw.DrawBox(

                box,

                {

                    label:
                    match.label == "unknown"

                    ? "Unknown"

                    : `${match.label}
                       (${Math.round(
                       (1 - match.distance) * 100
                       )}%)`

                }

            );

            drawBox.draw(canvas);

            // SAVE ATTENDANCE
            if(match.label != "unknown"){

                if(

                    !scannedStudents.includes(
                        match.label
                    )

                ){

                    scannedStudents.push(
                        match.label
                    );

                    markAttendance(
                        match.label
                    );

                }

            }

        });

    }, 1500);

});


/* --------------------------------
   MARK ATTENDANCE
---------------------------------*/
async function markAttendance(studentCode){

    let formData = new FormData();

    formData.append(
        "action",
        "save_attendance"
    );

    formData.append(
        "student_code",
        studentCode
    );

    formData.append(
        "attendance_session_id",

        document.getElementById(
            "attendance_session_id"
        ).value
    );

    const response =

    await fetch(
        "../../backend/process.php",
    {

        method:"POST",
        body:formData

    });

    const data =
    await response.json();

    if(data.status == "success"){

        addAttendanceRow(data);

        Swal.fire({

            toast:true,
            position:"top-end",
            icon:"success",
            title:data.student_name,
            showConfirmButton:false,
            timer:2000

        });

    }

}

/* --------------------------------
   LIVE TABLE
---------------------------------*/
function addAttendanceRow(data){

    const table = document.getElementById("attendanceTable");

    table.innerHTML += `

    <tr>

        <td>
            ${data.student_code}
        </td>

        <td>
            ${data.student_name}
        </td>

        <td>
            ${data.time}
        </td>

    </tr>

    `;

}

</script>

</body>
</html>

