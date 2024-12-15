<?php
include '../functions/google_fonts.php';
require_once '../functions/functions.php';
require_once '../databases/connect.php';
require_once '../databases/database.class.php';
require_once '../databases/faculty.classes.php';
session_start();

$db = new Database();
$user = new faculty($db);

$id = $_SESSION['department_id'];
$prof_id = $_SESSION['ID'];
// $get_type = $user->get_type();
$array = $user->get_course($id, $prof_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexuse</title>

    <?php 
    includeGoogleFont([
        'Josefin Sans:ital,wght@0,100..700;1,100..700', 
        'Poppins:wght@400;600'
    ]); 
    ?>

    <script src="https://kit.fontawesome.com/3c9d5fece1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="profStyle.css">
   
</head>

<body>
     <!-- SIDEBAR AREA -->
     <div class="sidebar">
        <div class="top">
            <div class="logo">
                <img src="/nexuse/images/Nexuse.svg" class="cat">
                <span class="text-cat">Nexuse.</span>
            </div>
            <i class="fa-solid fa-bars" id="sbtn"></i>
        </div>
        <ul class="sidebar-icons">
    <li>
        <a href="../professor-view/professors.php">
            <i class="fa-solid fa-house-chimney-user"></i>
            <span class="nav-item">Home</span>
        </a>
    </li>
    <li>
        <a href="../professor-view/subProfs.php">
            <i class="fa-solid fa-inbox"></i>
            <span class="nav-item">Submissions</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa-solid fa-gear"></i>
            <span class="nav-item">Settings</span>
        </a>
    </li>
    <li>
        <a href="../login/logout.php">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="nav-item">Logout</span>
        </a>
    </li>
</ul>
</div>
    </div> 
    <!-- NAVBAR TO -->
    <div class="main">
       <nav class="navbar">
        <div class="navbar-brand">
            <a href="#" class="site-title">Home Page.</a>
        </div>
        <div class="navbar-icons">
          <img src="/nexuse/images/therock.png" alt="Profile Icon" class="icon-image">
        </div>
    </nav>

     <!-- MAIN/LAMAN NG CONTAINER -->
     <div class="user-p-cont"> 
              <div class="user-profile">
    <img src="/nexuse/images/therock.png" alt="User Image" class="profile-image">
    <div class="profile-info">
        <h4 class="profile-name" name="name"><?=$_SESSION['last_name'] . ', ' . $_SESSION['first_name'] . ' ' . (!empty($_SESSION['middle_name']) ? $_SESSION['middle_name'] : '') ?></h4>
        <span class="profile-class"><?=clean_input($_SESSION['user_type'])?></span>
    </div>
  </div>

  <div class="subject-container">
    <a class="subject-title">Classes</a>
    <?php
        foreach($array as $arr) {
    ?>
    <div class="subject-area">
    <i class="fa-solid fa-caret-right"></i>
    <div class="subject-code"><?=$arr['acronym']?> |</div>
    <div class="subject-name"><?=$arr['name']?></div>
    <button class="letter-button" type="button" onclick="navigateToClass(this)" data-subject="<?=$arr['name']?>">View Letters</button>
    <span class="pending-badge">
                    <i class="fa-solid fa-clock-rotate-left" title="Pending"></i>
                    <span class="pending-count"><?=$arr['total']?></span>
                </span>
</div>
<?php } ?>

<!-- JS BANDA -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
    // sidebar burger button
     const btn = document.querySelector("#sbtn");
     const sidebar = document.querySelector(".sidebar");
     btn.addEventListener("click", () => {
     sidebar.classList.toggle("active");
    });

    // count ng pending
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pending-count').forEach(function (pendingCountElement) {
        const pendingCount = parseInt(pendingCountElement.textContent);
        const subjectArea = pendingCountElement.closest('.subject-area');
        if (!subjectArea) return;

        const caretIcon = subjectArea.querySelector('.fa-caret-right');
        if (!caretIcon) return;

        if (isNaN(pendingCount) || pendingCount === 0) { 
            // Handle no number or 0 case
            pendingCountElement.classList.add('pending-black');
            pendingCountElement.classList.remove('pending-red');
            caretIcon.classList.add('arrow-black');
            caretIcon.classList.remove('arrow-red');
        } else { 
            // Handle any non-zero number case
            pendingCountElement.classList.add('pending-red');
            pendingCountElement.classList.remove('pending-black');
            caretIcon.classList.add('arrow-red');
            caretIcon.classList.remove('arrow-black');
        }
    });
}); 

function navigateToClass(button) {
        const subject = button.getAttribute('data-subject');
        window.location.href = `viewSubmissions.php?subject=${subject}`;
    }

     document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.letter-button');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const subjectName = this.getAttribute('data-subject');
                // Redirect to faculty-classes.php with the subject name as a query parameter
                window.location.href = `viewSubmissions.php?subject=${encodeURIComponent(subjectName)}`;
            });
        });
    });

//     // button sa letter
//     document.querySelectorAll('.letter-button').forEach(button => {
//     button.addEventListener('click', (event) => {
//         const subjectName = event.currentTarget.getAttribute('data-subject');

//         const modalTitle = document.getElementById('excuseLetterModalLabel');
//         modalTitle.textContent = `${subjectName} | Excuse Letter Form`;

//         const excuseLetterModal = new bootstrap.Modal(document.getElementById('excuseLetterModal'));
//         excuseLetterModal.show();
//     });
// });

    </script>
</body>
</html>
