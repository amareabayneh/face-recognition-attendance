<?php

function createSystemLog(
    $conn,
    $user_id,
    $action_type,
    $description,
    $severity = "Info"
){

    $ip_address =
    $_SERVER['REMOTE_ADDR'] ?? '';

    $user_agent =
    $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt = mysqli_prepare($conn,

    "INSERT INTO system_logs
    (
        user_id,
        action_type,
        description,
        ip_address,
        user_agent
    )

    VALUES(?,?,?,?,?)"

    );

    mysqli_stmt_bind_param(

        $stmt,
        "issss",

        $user_id,
        $action_type,
        $description,
        $ip_address,
        $user_agent

    );

    mysqli_stmt_execute($stmt);

}
?>

