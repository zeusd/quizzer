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

$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
}

$json_ql = file_get_contents("../quizzes/quiz_list.json");
$quiz_list = json_decode($json_ql, true);

for ($i = 0; $i < 100; $i++) {
    foreach ($quiz_list["quizzes"] as $quiz) {
        $quiz_sql = "SELECT * FROM quizzes WHERE NAME=\"" . $quiz . "\";";
        $quiz_res = $conn->query($quiz_sql);
        $res_json = "{";
        $row = $quiz_res->fetch_assoc();
        $q_ans = json_decode($row["ANSWERS"]);
        $max = $row["MAX_SCORE"];

        $score = 0;
        $first = true;
        foreach ($q_ans as $k => $v) {
            $rand_ans = random_int(1, 4);
            if ($rand_ans == $v){
                $score++;
            }
            if ($first) {
                $first = false;
            } else {
                $res_json .= ",";
            }
            $res_json .= "\"q" . $k . "\":\"" . $rand_ans . "\"";
        }

        $res_json .= "}";

        $uid = rand_uid($uid_len);
        $ch_sql = "SELECT * FROM results WHERE UID=\"" . $uid . "\";";
        $ch_res = $conn->query($ch_sql);

        while (mysqli_num_rows($ch_res) != 0) {
            $uid = rand_uid($uid_len);
            $ch_sql = "SELECT * FROM results WHERE UID=\"" . $uid . "\";";
            $ch_res = $conn->query($ch_sql);
        }

        $ins_sql = "INSERT INTO results (UID, QUIZ, ANSWERS, SCORE) VALUES('" . $uid . "', '" . $quiz . "', '" . $res_json . "', '" . $score . "');";
        $conn -> query($ins_sql);
    }
}

echo "Inserted 100 random answers into each quiz<br>";

mysqli_close($conn);
?>
