<?php
session_start();
include("../server/connection.php");


if (isset($_SESSION['logged_in'])) {
  header('location: ../index.php');
  exit;
} else {

  if (isset($_POST['btn_register'])) {

    $query_user_id = "SELECT * FROM user";
    $stmt_id = $conn->prepare($query_user_id);
    $stmt_id->execute();
    $stmt_id->store_result();

    $user_id = 'U' . $stmt_id->num_rows() + 1;
    $username = $_POST['username_reg'];
    $email = $_POST['email'];
    $password = $_POST['password_reg'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $member = date('Y-m-d');
    $role = 'customer';

    if ($password != $confirm_password) {
      header('location: ../pages/login.html?error=Password don\'t match');
    } else if (strlen($password) < 6) {
      header('location: ../pages/login.html?error=Password must be at least 6 characters');
    } else {
      $password = md5($password);
      $query_check_user = "SELECT COUNT(*) FROM user WHERE email = ?";

      $stmt_check_user = $conn->prepare($query_check_user);
      $stmt_check_user->bind_param('s', $email);
      $stmt_check_user->execute();
      $stmt_check_user->bind_result($num_rows);
      $stmt_check_user->store_result();
      $stmt_check_user->fetch();

      if ($num_rows !== 0) {
        header('location: ../pages/login.html?error=User with this email is already exist');
      } else {
        $query_insert_user = "INSERT INTO user (user_id, username, email, password, alamat, member, role) VALUES(?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert_user = $conn->prepare($query_insert_user);
        $stmt_insert_user->bind_param('sssssss', $user_id, $username, $email, $password, $address, $member, $role);

        if ($stmt_insert_user->execute()) {
          $_SESSION['user_id'] = $user_id;
          $_SESSION['user_name'] = $username;
          $_SESSION['user_email'] = $email;
          $_SESSION['user_address'] = $address;
          $_SESSION['member_date'] = $member;
          $_SESSION['user_role'] = $role;
          $_SESSION['logged_in'] = true;

          header('location: ../index.php?register_success=You registered successfully!');
        } else {
          header('location: ../pages/login.html?error=Could not create an account at the moment');
        }
      }
    }
  }
}
