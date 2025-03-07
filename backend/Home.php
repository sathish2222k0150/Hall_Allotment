<?php
// Database configuration
$host = 'localhost';
$db = 'exam_hall';
$user = 'root'; // Change to your database username
$pass = '';     // Change to your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch counts
$roomCount = $conn->query("SELECT COUNT(*) AS count FROM rooms")->fetch_assoc()['count'];
$teacherCount = $conn->query("SELECT COUNT(*) AS count FROM teachers")->fetch_assoc()['count'];
$studentCount = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];

// Fetch distinct department count
$deptCount = $conn->query("SELECT COUNT(DISTINCT dept) AS count FROM students")->fetch_assoc()['count'];

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./Hall_Allotment.css" rel="stylesheet" />
    <title>Hall Allotment</title>
    <link rel="icon" type="image/png" sizes="16x48" href="./img/logo-1.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Habibi&family=Halant:wght@300;400;500;600;700&family=Hammersmith+One&family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&family=Hedvig+Letters+Serif:opsz@12..24&family=Kadwa:wght@400;700&family=League+Spartan:wght@100..900&family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header_section">
         <img src="./img/logo-1.png" alt="Logo">
            <div class="LN">
                <h3>  HALL PLANNER  </h3>
                <P>"Success begins with the right exam seat"</P>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ">
                  <li class="nav-item active">
                    <a class="nav-link" href="index.html">Home</a>
                  </li>
                </ul>
                </div>
                <a href="admin.php" class="Admin">Admin</a>
                <a href="#" class="profile-icon">K</a>
    </header>

   <br>                                <!---------- Form_1 ---------->

   <section class="Form_1">
      <img src="./img/Home.png" alt="Home.png">
      <div class="F1">
      <h1>Exam Hall<br>Allotment System</h1>
      <p>Welcome to our comprehensive exam hall allotment website, designed<br> to streamline the process of assigning exam venues for college studen...</p><br><br>
      <a href="room_allocate.php" class="Allocate-Exam-Hall" href="">Allocate Exam Hall</a>
      <a href="insert.php" class="View-Allotment">View Allotment</a>
      </div>
    </section>

    <br><br>                           <!---------- Form_2 ---------->
  
    <section class="Form_2">
        <div class="admin-panel">Admin panel:</div>
            <div class="F2">

                <!-- Card 1: Navigates to staff.html -->
                <div class="card orange">
                    <img src="./img/sta_3.png" alt="Staff">
                    <div class="card-content">
                    <div class="count"><?php echo $teacherCount; ?></div>
                    <p>Total Staff</p>
                    </div>
                </div>

                <!-- Card 2: Navigates to student.html -->
                <div class="card blue-light">
                    <img src="./img/stu_6.png" alt="Student">
                    <div class="card-content">
                    <div class="count"><?php echo $studentCount; ?></div>
                    <p>Total Student</p>
                    </div>
                </div>
                
                <!-- Card 3: Navigates to Class_Room.html -->
                <div class="card blue-dark">
                    <img src="./img/stu_10.png" alt="Classroom">
                    <div class="card-content">
                    <div class="count"><?php echo $roomCount; ?></div>
                    <p>Total Class Room</p>
                    </div>
                </div>
                <!-- Card 4: Navigates to Department.html -->
                <div class="card red">
                    <img src="./img/sta_4.png" alt="Department">
                    <div class="card-content">
                    <div class="count"><?php echo $deptCount; ?></div>
                    <p>Total Department</p>
                    </div>
                </div>
            </div>
    </section>

    <br><br><br>                       <!---------- Form_3 ---------->

    <section class="Form_3">
        <h1>Exam Hall Allocation: <br> Optimizing Student Experience...</h1>
        <p class="description">Our exam hall allotment service is tailored to meet the unique needs of college campuses, leveraging advanced algorithms and data-driven insights to assign exam venues with precision and fairness</p>
        
        <div class="testimonials">
            
            <div class="testimonial">
                <div class="icon student"></div>
                <p class="title">Student<br>Testimonials</p>
            </div>

            <div class="testimonial">
                <div class="icon faculty"></div>
                <p class="title">Faculty<br>Testimonials</p>
            </div>

            <div class="testimonial">
                <div class="icon admin"></div>
                <p class="title">Administrator<br>Testimonials</p>
            </div>
        </div>
    </section>

     <br><br><br>                            <!---------- Form_4 ---------->
    <section class="Form_4">
        <div class="contact-text">
            <h1>Contact Us</h1>
            <a href="#" class="feedback-link">Send Feedback</a>
            <p class="F4_description">
                At our exam hall allotment website, we prioritize seamless communication, timely updates, and personalized support to ensure a stress-free exam experience for all students.
            </p>
            <button class="submit-button">Submit Request</button>
        </div>
        <div class="contact-image">
            <img src="./img/Contact_Us.png" alt="Customer Support" width="350px" height="550px"> <!-- Replace with your actual image path -->
        </div>
    </section>

    <br><br>

    <section class="footer">

        <div class="FF">
            <p>Thank you for choosing our website to allocate your exam hall. We're glad to assist you with an easy and efficient process. We hope this service meets your needs and makes exam preparation smoother. If you have any questions, feel free to ask!</p>
        </div>

            <img src="./img/footer.png" alt="footer" class="F-img">

    </section>

                        <!----------// script-------------> 

                        <button id="scrollToTopBtn">↑</button>

    <script>
        // Show button on scroll
window.onscroll = function() {
    const btn = document.getElementById("scrollToTopBtn");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        btn.classList.add("show");
    } else {
        btn.classList.remove("show");
    }
};

// Scroll to top when button is clicked
document.getElementById("scrollToTopBtn").onclick = function() {
    window.scrollTo({
        top: 0,
        behavior: "smooth"  // This enables the smooth scroll effect
    });
};

        </script>

</body>
</html>