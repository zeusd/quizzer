<?php
include './conf.php';

$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
}

$get_sql = "SELECT QUIZ, SCORE FROM results WHERE UID=\"" . $_GET['z'] . "\";";
$res_get = $conn->query($get_sql);

if (mysqli_num_rows($res_get) > 0) {
    $row = $res_get->fetch_assoc();
    $q_name = $row["QUIZ"];
    $score = $row["SCORE"];
    $quiz_sql = "SELECT MAX_SCORE FROM quizzes WHERE NAME=\"" . $q_name . "\";";
    $res_quiz = $conn->query($quiz_sql);
    $max = $res_quiz->fetch_assoc()["MAX_SCORE"];
    # TODO: Add answers to response
    echo "{\"score\": " . $score . ", \"total\": ". $max . "}";
}

mysqli_close($conn);
?>

