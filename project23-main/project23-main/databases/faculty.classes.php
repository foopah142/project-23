<?php
class faculty {

    public $email;
    public $password;
    private $pdo;

    function __construct($db) {
        $this->pdo = $db->connect();
    }

    function get_adviser($username) {
        $sql = "SELECT * FROM adviser
        JOIN users ON adviser.user_id = users.ids
        WHERE email = :email
        LIMIT 1;";

        $query = $this->pdo->prepare($sql);

        $query->bindParam(':email', $username);
        
        if ($query->execute()) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                session_start();
                $_SESSION['ids'] = $data['ids'];
                $_SESSION['last_name'] = $data['last_name'];
                $_SESSION['first_name'] = $data['first_name'];
                $_SESSION['middle_name'] = $data['middle_name'];
                $_SESSION['department_id'] = $data['department_id'];
                $_SESSION['user_type'] = $data['user_type'];
                $_SESSION['year_level'] = $data['year_level'];

                return true; 
            }
        }
        
        return false;
    }

    function get_guidance($username) {
        $sql = "SELECT * FROM guidance
        JOIN users ON guidance.user_id = users.ids
        WHERE email = :email
        LIMIT 1";

        $query = $this->pdo->prepare($sql);

        $query->bindParam(':email', $username);
        
        if ($query->execute()) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                session_start();
                $_SESSION['ids'] = $data['ids'];
                $_SESSION['last_name'] = $data['last_name'];
                $_SESSION['first_name'] = $data['first_name'];
                $_SESSION['middle_name'] = $data['middle_name'];
                $_SESSION['user_type'] = $data['user_type'];

                return true; 
            }
        }
        
        return false;
    }
    
    function get_prof($username) {
        $sql = "SELECT * FROM professors
        JOIN users ON professors.user_id = users.ids
        WHERE email = :email
        LIMIT 1";

        $query = $this->pdo->prepare($sql);

        $query->bindParam(':email', $username);
        
        if ($query->execute()) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                session_start();
                $_SESSION['ids'] = $data['ids'];
                $_SESSION['last_name'] = $data['last_name'];
                $_SESSION['first_name'] = $data['first_name'];
                $_SESSION['middle_name'] = $data['middle_name'];
                $_SESSION['department_id'] = $data['department_id'];
                $_SESSION['user_type'] = $data['user_type'];
                $_SESSION['ID'] = $data['ID'];

                return true; 
            }
        }
       
        return false;
    }

    function get_type($id){
        $sql = "SELECT name FROM department
        LEFT JOIN users ON department.id = users.department_id 
        WHERE ids = :id";

        $query = $this->pdo->prepare($sql);

        $query->bindParam(":id", $id); 
        $data = null;

        if ($query->execute()) {
            $data = $query->fetchColumn();  
        }
        return $data;
    }

    function get_dept() {
        $sql = "SELECT department.name as Name, COALESCE(COUNT(DISTINCT approval.noted_adviser), 0) as Count
        FROM department
        LEFT JOIN sections ON department.id = sections.department_id
        LEFT JOIN student ON student.sections_id = sections.id
        LEFT JOIN excuse_letter ON student.student_id = excuse_letter.student_id
        LEFT JOIN approval ON approval.excuse_letter_id = excuse_letter.id AND approval.approved_adviser = 1
        LEFT JOIN adviser ON approval.noted_adviser = adviser.id
        GROUP BY department.id, Name ";

        $query = $this->pdo->prepare($sql);

        $data = null;

        if($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function get_total_year($subject) {
        $sql = "SELECT year_level FROM sections LEFT JOIN department ON department.id = sections.department_id WHERE department.name LIKE :name ORDER BY year_level ASC";

        $query = $this->pdo->prepare($sql);
        $query->bindParam(":name", $subject); 
        $data = null;

        if($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function get_course($id, $prof_id) {
        $sql = "SELECT subject.name, acronym, COUNT(excuse_letter.id) AS total FROM subject 
        LEFT JOIN excuse_letter ON subject.id = excuse_letter.subject_id AND excuse_letter.prof_id = :prof_id
        LEFT JOIN department ON subject.department_id = department.id
        LEFT JOIN approval ON excuse_letter.id = approval.excuse_letter_id
        WHERE (subject.department_id = :id) AND (approval.approved_adviser = 1) AND (approval.approved_guidance = 1)
        GROUP BY subject.id, subject.name, acronym 
        ORDER BY total DESC";

        $query = $this->pdo->prepare($sql);
        $query->bindParam(":id", $id); 
        $query->bindParam(":prof_id", $prof_id); 
        $data = null;

        if($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    
    function get_excuse_letters($subject, $prof_id) {
        $sql = "SELECT DISTINCT CONCAT(last_name, ', ', first_name, IFNULL(CONCAT(' ', middle_name), '')) AS name, date_submitted, date_absent, comment, sections.name as course, reason.type, excuse_letter
        FROM excuse_letter 
        LEFT JOIN course ON excuse_letter.course_id = course.id
        LEFT JOIN student ON excuse_letter.student_id = student.student_id
        LEFT JOIN reason ON excuse_letter.reason_id = reason.id
        LEFT JOIN sections ON student.sections_id = sections.id
        LEFT JOIN users ON student.user_id = users.ids
        WHERE (course.name = :course_name) AND (excuse_letter.prof_id = :prof_id)";

        $query = $this->pdo->prepare($sql);
        $query->bindParam(":course_name", $subject); 
        $query->bindParam(":prof_id", $prof_id); 
        $data = null;

        if($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }

    function get_adviser_excuse_letter($department_id){
        $sql = "SELECT DISTINCT CONCAT(stud_user.last_name, ', ', stud_user.first_name, IFNULL(CONCAT(' ', stud_user.middle_name), '')) AS student_name, sections.name, CONCAT(prof_user.last_name, ', ', prof_user.first_name, IFNULL(CONCAT(' ', prof_user.middle_name), '')) AS professor_name, date_absent, date_submitted, comment, reason.type, excuse_letter, approval.approved_adviser
        FROM excuse_letter
        JOIN student ON excuse_letter.student_id = student.student_id
        JOIN sections ON student.sections_id = sections.id
        JOIN reason ON excuse_letter.reason_id = reason.id
        JOIN users AS stud_user ON student.user_id = stud_user.ids
        JOIN prof_approval ON excuse_letter.id = prof_approval.excuse_letter_id
        JOIN professors ON prof_approval.prof_id = professors.ID
        JOIN users AS prof_user ON professors.user_id = prof_user.ids
        JOIN adviser ON sections.year_level = adviser.year_level
        JOIN department ON sections.department_id = department.id
        JOIN approval ON approval.excuse_letter_id = excuse_letter.id
        WHERE (department.id = :department_id) AND (sections.year_level = adviser.year_level) AND (approval.approved_adviser = 'NULL')";

        $query = $this->pdo->prepare($sql);

        $query->bindParam(":department_id", $department_id); 

        $data = null;

        if($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
}