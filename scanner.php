<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System</title>

     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Poppins:wght@500&display=swap" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to bottom, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0.15) 100%), radial-gradient(at top center, rgba(255,255,255,0.40) 0%, rgba(0,0,0,0.40) 120%) #989898;
            background-blend-mode: multiply,multiply;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 91.5vh;
        }

        .attendance-container {
            height: 90%;
            width: 90%;
            border-radius: 20px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .attendance-container > div {
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 10px;
            padding: 30px;
        }

        .attendance-container > div:last-child {
            width: 64%;
            margin-left: auto;
        }

        .alert-container {
            margin-bottom: 15px;
        }
          /* Digital Clock Styles */
          #digital-clock {
            font-size: 70px;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #2c3e50, #4ca1af, #d3d3d3);


            text-align: center;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(255, 0, 102, 0.5), 0 0 25px rgba(255, 0, 102, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        #digital-clock:hover {
            box-shadow: 0 0 30px rgba(255, 0, 102, 0.75), 0 0 50px rgba(255, 0, 102, 0.75);
            transition: box-shadow 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ml-4" href="scanner.php">QR Code Attendance </a>
        <a class="navbar-brand ml-4" href="landing_page.php">Landing Page </a>
    </nav>

    <div class="main">
        <div class="attendance-container row">
            <!-- Success Message -->
           
            <div class="qr-container col-4">
                <div class="scanner-con">
                <div class="logo-container text-center mb-4">
        <img src="prod/assets/img/lgu.jpg" alt="LGU Logo" class="logo img-fluid rounded" style="margin-right: 20px; height: 90px; width: 90px; object-fit: cover;">
        <img src="prod/assets/img/astig.jpg" alt="Astig Logo" class="logo img-fluid rounded" style="height: 90px; width: 90px; object-fit: cover;">
    </div>
                    <h5 class="text-center">Scan your QR Code here for your attendance</h5>
                    <!-- Dropdown to choose attendance action -->
                    <div class="form-group">
                        <label for="attendance-type">Select Attendance Action:</label>
                        <select id="attendance-type" name="attendance_type" class="form-control">
                            <option value="time_in">Time In</option>
                            <option value="break_out">Break Out</option>
                            <option value="break_in">Break In</option>
                            <option value="time_out">Time Out</option>
                        </select>
                    </div>
                    <video id="interactive" class="viewport" width="100%"></video>
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form id="attendanceForm" action="add-attendance.php" method="POST">
                        <h4 class="text-center">QR Code Detected!</h4>
                        <!-- Hidden fields -->
                        <input type="hidden" id="detected-qr-code" name="qr_code">
                        <input type="hidden" id="detected-attendance-type" name="attendance_type">
                    </form>
                </div>
            </div>

            <div class="attendance-list">

            <div class="col-12 text-center mb-4">
               <H1></H1> <div id="digital-clock"></div>
            </div>

        <script>
    function updateClock() {
        const clock = document.getElementById("digital-clock");
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, "0");
        const minutes = String(now.getMinutes()).padStart(2, "0");
        const seconds = String(now.getSeconds()).padStart(2, "0");
        clock.textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Update the clock every second
    setInterval(updateClock, 1000);
    // Initialize the clock immediately
    updateClock();
</script>
                <!-- <h4>List of Present Employees</h4> -->
               <!-- Success Message -->
               <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <div class="col-12 alert-container">
        <div class="alert alert-success text-center" id="successAlert">
            <?php
            $action = $_GET['action'];
            $name = isset($_GET['name']) ? $_GET['name'] : 'Employee';
            $position = isset($_GET['position']) ? $_GET['position'] : 'Position';

            switch ($action) {
                case 'time_in':
                    echo "$name ($position) Time In Successfully. Thank you!";
                    break;
                case 'time_out':
                    echo "$name ($position) Time Out Successfully. Thank you!";
                    break;
                case 'break_in':
                    echo "$name ($position) Break In Successfully. Thank you!";
                    break;
                case 'break_out':
                    echo "$name ($position) Break Out Successfully. Thank you!";
                    break;
            }
            ?>
        </div>
    </div>
<?php endif; ?>

<script>
    // Automatically hide success alert after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';

                // Completely remove the element after fading out
                setTimeout(() => {
                    successAlert.remove();
                }, 500);
            }, 5000);
        }
    });
</script>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- instascan Js -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>
        let scanner;

        function startScanner() {
            scanner = new Instascan.Scanner({ video: document.getElementById('interactive') });

            scanner.addListener('scan', function (content) {
                // Get the selected attendance type
                const attendanceType = document.getElementById("attendance-type").value;

                // Set the QR code and attendance type to the hidden fields
                document.getElementById("detected-qr-code").value = content;
                document.getElementById("detected-attendance-type").value = attendanceType;

                // Submit the form automatically
                document.getElementById("attendanceForm").submit();
            });

            Instascan.Camera.getCameras()
                .then(function (cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        console.error('No cameras found.');
                        alert('No cameras found.');
                    }
                })
                .catch(function (err) {
                    console.error('Camera access error:', err);
                    alert('Camera access error: ' + err);
                });
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>
</body>
</html>

