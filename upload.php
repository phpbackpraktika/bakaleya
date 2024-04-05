<?php

include_once 'db.php';

// Путь к папке, куда вы хотите загрузить CSV файлы
$uploadDirectory = './bakaleya';


if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die("Ошибка загрузки" . $_FILES['file']['error']);
}

// Получаем имя и временный путь файла
$fileName = $_FILES['file']['name'];
$tmpFilePath = $_FILES['file']['tmp_name'];

// Перемещаем файл папку
$targetFilePath = $uploadDirectory . $fileName;

if (!move_uploaded_file($tmpFilePath, $targetFilePath)) {
    die("Ошибка загрузки файла");
}

// Открываем загруженный CSV файл для чтения
$file = fopen($targetFilePath, 'r');
if (!$file) {
    die("Ошибка открытии файла");
}

$query = "INSERT INTO products (article, name, price, qty, brand_name, category_name) 
          VALUES (?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE 
          name = VALUES(name), 
          price = VALUES(price), 
          qty = VALUES(qty), 
          brand_name = VALUES(brand_name), 
          category_name = VALUES(category_name)";
$stmt = $conn->prepare($query);

// Пропускаем заголовки 
fgets($file);

// Читаем CSV файл
while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
    if (count($data) == 6) {
        $article = $conn->real_escape_string($data[0]);
        $name = $conn->real_escape_string($data[1]);
        $price = intval($data[2]);
        $qty = intval($data[3]);
        $brand_name = $conn->real_escape_string($data[4]);
        $category_name = $conn->real_escape_string($data[5]);

        $stmt->bind_param("ssiiss", $article, $name, $price, $qty, $brand_name, $category_name);
        $stmt->execute();
    } else {
        echo "Ошибка" . implode(",", $data) . "<br>";
    }
}


fclose($file);
$stmt->close();


echo "Загрузка завершена.";
?>
