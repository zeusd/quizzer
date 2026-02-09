<?php
include './conf.php';

$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
}

$json_ql = file_get_contents("../quizzes/quiz_list.json");
$quiz_list = json_decode($json_ql, true);

foreach ($quiz_list["quizzes"] as $quiz_name) {
#   echo $quiz_name . "<br>";
    $check_sql = "SELECT * FROM quizzes WHERE NAME=\"" . $quiz_name . "\";";
#   echo $check . "<br>";
    
    $ch_res = $conn->query($check_sql);
    
    if ($ch_res->num_rows == 0) {
        $json_quiz = file_get_contents("../quizzes/" . $quiz_name . "/" . $quiz_name . ".json");
        $quiz = json_decode($json_quiz, true);
        $qs = "";
        $as = "{";
        $ms = 0;

        $qs = json_encode($quiz["qs"]);
        foreach ($quiz["qs"] as $n => $q) {
            if ($ms > 0) {
                $as .= ", ";
            }
            $ms++;
            $as .= "\"" . $n . "\"" . ": " . $q["c"];
        }
        $as .= "}";
        
        $ins_sql = "INSERT INTO quizzes (NAME, QUESTIONS, ANSWERS, MAX_SCORE) VALUES ('" . $quiz_name . "', '" . $qs . "', '" . $as . "', '" . $ms . "');";
#       echo $ins . "<br>";

        $conn->query($ins_sql);
        $ch_res = $conn->query($check_sql);
        if ($ch_res->num_rows == 1) {
            echo "inserted quiz '" . $quiz_name . "'<br>";
        } else {
            echo "failed to insert quiz '" . $quiz_name . "'<br>";
        }
        
    } else {
        echo "quiz '" . $quiz_name . "' already there<br>";
    }
}

$sql = "SELECT * FROM quizzes;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<br><br>";
    while ($row = $result->fetch_assoc()) {
        echo $row["NAME"] . "<br>";
        echo $row["QUESTIONS"] . "<br>";
        echo $row["ANSWERS"] . "<br>";
        echo $row["MAX_SCORE"] . "<br><br><br>";
    }
} else {
    echo "huh";
}

mysqli_close($conn);
?>

