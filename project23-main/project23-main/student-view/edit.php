<?php
include '../functions/google_fonts.php';
require_once '../functions/functions.php';
require_once '../databases/connect.php';
require_once '../databases/database.class.php';

session_start();

$db = new Database();
$user = new User($db);

$id = $_SESSION['ids'];
$submissions = $user->excuse_letters($id);
$list = $user->get_prof();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    includeGoogleFont([
        'Josefin Sans:ital,wght@0,100..700;1,100..700', 
        'Poppins:wght@400;600'
    ]); 
    ?>

    <script src="https://kit.fontawesome.com/3c9d5fece1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="submissionsStyle.css">

</head>
<body>
    
</body>
</html>
<div class="modal fade" id="excuseLetterModal" tabindex="-1" aria-labelledby="excuseLetterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excuseLetterModalLabel">Edit Submission ✎</h5>
                <button type="button" class="fa-regular fa-circle-xmark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSubmissionForm" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input class="form-control" type="hidden" id="editId" name="id">
                        <label for="professor" class="form-label">Professor:</label>
                        <select class="form-select" id="professor" name="prof" required>
                        <option value="" disabled selected>Submit to:</option>
                            <?php foreach($list as $li) {?>
                                <option value="<?=$li['ID']?>"><?=$li['last_name']. ', ' . $li['first_name'] . ' ' . (!empty($li['middle_name']) ? $li['middle_name'] : '')?></option>
                            <?php
                            }
                        ?>
                        </select>
                    </div>

                    <!-- Date of Absence -->
                    <div class="mb-3">
                        <label for="editDateOfAbsent" class="form-label">Date of Absent:</label>
                        <input type="date" class="form-control" id="editDateOfAbsent" name="date_of_absent" required>
                    </div>

                    <!-- Comment Textarea -->
                    <div class="mb-3">
                        <label for="editComment" class="form-label">Comment:</label>
                        <textarea class="form-control" id="editComment" name="comment" rows="3"></textarea>
                    </div>
                    
                    <!-- Photo -->
                    <div class="mb-3">
                        <label for="editPhoto" class="form-label">Photo:</label>
                        <small class="form-text text-muted">Upload a new photo to change (optional).</small>
                        <input type="file" class="form-control" id="editPhoto" name="photo" accept="image/*">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" name="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Pop up yung -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Photo of a documents/proof</h5>
                <button type="button" class="fa-regular fa-circle-xmark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <img id="modalPhoto" src="" alt="Full Image" class="img-fluid w-100 h-100" style="object-fit: contain;">
            </div>
        </div>
    </div>
</div>


<script>
 document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('excuseLetterModal'));

        const editButtons = document.querySelectorAll('.edit-button');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const professor = this.getAttribute('professor');
                const dateAbsent = this.getAttribute('data-date-absent');
                const comment = this.getAttribute('data-comment');

                document.getElementById('professor').value = professor;
                document.getElementById('editDateOfAbsent').value = dateAbsent;
                document.getElementById('editComment').value = comment;
                document.getElementById('editId').value = id;
            });
        });

        const form = document.getElementById('editSubmissionForm');
        form.addEventListener('submit', function (event) {
            // event.preventDefault();
            this.submit();
            
            // Get updated data from the form
            const updatedId = document.getElementById('editId').value;
            const updatedCourse = document.getElementById('professor').value;
            const updatedDateAbsent = document.getElementById('editDateOfAbsent').value;
            const updatedComment = document.getElementById('editComment').value;
            const updatedPhoto = document.getElementById('editPhoto').files[0]; // di ko na alam pag iupdate photo 

            console.log('Updated Data:', {
                id: updatedId,
                course: updatedCourse,
                date_absent: updatedDateAbsent,
                comment: updatedComment ,

            });

            // modal.hide();
        });
    });

</script>