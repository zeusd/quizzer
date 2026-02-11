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

    $lt_sql = "SELECT COUNT(SCORE) AS LT FROM results WHERE QUIZ=\"" . $q_name . "\" AND SCORE < " . $score . ";";
    $res_lt = $conn->query($lt_sql);
    $lt = $res_lt->fetch_assoc()["LT"];

    $gt_sql = "SELECT COUNT(SCORE) AS GT FROM results WHERE QUIZ=\"" . $q_name . "\" AND SCORE > " . $score . ";";
    $res_gt = $conn->query($gt_sql);
    $gt = $res_gt->fetch_assoc()["GT"];

    $tot_sql = "SELECT COUNT(SCORE) AS TOT FROM results WHERE QUIZ=\"" . $q_name . "\";";
    $res_tot = $conn->query($tot_sql);
    $tot = $res_tot->fetch_assoc()["TOT"];

    # TODO: Add answers to response
    echo "{\"score\": " . $score . ", \"max\": ". $max . ", \"lt\": " . $lt . ", \"gt\": " . $gt . ", \"tot\": " . $tot . "}";
}

mysqli_close($conn);
?>

