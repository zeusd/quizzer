<?php

include './conf.php';

$uid_len = 12;

function rand_uid($len) {
    $chars = '0123456789_-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $char_len = strlen($chars);
    $rand_str = '';

    for ($i = 0; $i < $len; $i++) {
        $rand_ind = random_int(0, $char_len - 1);
        $rand_str .= $chars[$rand_ind];
    }

    return $rand_str;
}

$json_req = file_get_contents('php://input');
$req = json_decode($json_req, true);
$quiz = $req['quiz'];
$ans = $req['answers'];


$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
}

#   echo $req['quiz'] . "<br>";

$score = 0;
$ans_sql = "SELECT * FROM quizzes WHERE NAME=\"" . $quiz . "\";";
$res_ans = $conn->query($ans_sql);

if ($res_ans->num_rows > 0) {
    $row = $res_ans->fetch_assoc();
    $q_ans = json_decode($row["ANSWERS"]);
    $max = $row["MAX_SCORE"];
    foreach ($q_ans as $k => $v) {
#           echo $k . " : " . $v . "<br>";
        if ($ans['q' . $k] && $ans['q' . $k] == $v) {
#               echo ".<br>";
            $score++;
        }
    }
}

$uid = rand_uid($uid_len);
$ch_sql = "SELECT * FROM results WHERE UID=\"" . $uid . "\";";
$ch_res = $conn->query($ch_sql);

while (mysqli_num_rows($ch_res) != 0) {
    $uid = rand_uid($uid_len);
    $ch_sql = "SELECT * FROM results WHERE UID=\"" . $uid . "\";";
    $ch_res = $conn->query($ch_sql);
}

#   echo $ch_sql . "<br>";

$ins_sql = "INSERT INTO results (UID, QUIZ, ANSWERS, SCORE) VALUES ('" . $uid . "', '" . $quiz . "', '" . json_encode($ans) . "', '" . $score . "');";
$conn->query($ins_sql);

#   echo $ins_sql . "<br>";

$res = array('uid' => $uid);
echo json_encode($res);

mysqli_close($conn);
?>

