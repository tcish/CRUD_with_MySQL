<?php
  $host = "localhost";
  $user = "root";
  $pass = "";
  $dbName = "product_entry_form";

  try {
    $connection = new PDO("mysql:host={$host}; dbname={$dbName}", $user, $pass);
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $error) {
    echo ("There is nothing to connect with!! Please create database(product_entry_form) name and import sql file from directory!!" . $error->getMessage());
  }
?>