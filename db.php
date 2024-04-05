<?php
// Подключение к базе данных MySQL
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "bakaleya"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подлючения к Базе Данных: " . $conn->connect_error);
}
