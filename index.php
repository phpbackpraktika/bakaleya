<?php
session_start();
include_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <title>Bakaleya</title>
</head>

<body>
  <?php

  if (isset($_SESSION['message']) && $_SESSION['message']) {
    printf('<b>%s</b>', $_SESSION['message']);
    unset($_SESSION['message']);
  }
  ?>

  <div class="container mt-5">
    <form action="upload.php" method="post" enctype="multipart/form-data">
      Выберите CSV файл:
      <input type="file" name="file" id="file">
      <input class="btn btn-primary" type="submit" value="Upload" name="submit">
    </form>
  </div>
  <div class="container mt-5">
    <form action="." method="GET">
      <label for="searchInput">Поиск по названию товара</label>
      <input type="text" name="query">
      <button type="submit">Поиск</button>
    </form>
 
     
        <?php
        include_once 'db.php';

        if (isset($_GET['query'])) {
          $searchQuery = $_GET['query'];

          $sql = "SELECT article, name, qty
            FROM products
            WHERE name LIKE '%$searchQuery%'
            ORDER BY qty DESC
            LIMIT 5";

          $result = $conn->query($sql);


          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<p>Артикул: " . $row["article"] . "</p>";
              echo "<p>Название: " . $row["name"] . "</p>";
              echo "<p>Количество: " . $row["qty"] . "</p>" . "<br>";
            }
          } else {
            echo "Нет результата";
          }
        } else {
          echo "";
        }

        ?>
    
    
  </div>

  <div class="container mt-5">
    <form action="." method="GET">
      <label for="searchInput">Поиск по категориям и брендам</label>
      <input type="text" name="q">
      <button type="submit">Поиск</button>
    </form>
    <?php

    include_once 'db.php';

    if (isset($_GET['q'])) {
      $search_query = $_GET['q'];

      // SQL запросы для поиска по категориям и брендам 
      $sql_name_brand = "SELECT * 
                       FROM products 
                       WHERE MATCH (name, brand_name) AGAINST ('$search_query' IN NATURAL LANGUAGE MODE)";
      $sql_name_category = "SELECT * 
                          FROM products 
                          WHERE MATCH (name, category_name) AGAINST ('$search_query' IN NATURAL LANGUAGE MODE)";

      function executeSearch($conn, $query, $message)
      {
        $result = $conn->query($query);
        echo "<h3>$message</h3>";
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<p>Product ID: " . $row["id"] . "</p>";
            echo "<p>Name: " . $row["name"] . "</p>";
            echo "<p>Brand: " . $row["brand_name"] . "</p>";
            echo "<p>Category: " . $row["category_name"] . "</p>";
            echo "<hr>";
          }
        } else {
          echo "<p>Нет данных</p>";
        }
      }

      executeSearch($conn, $sql_name_brand, "Поиск по бренду");
      executeSearch($conn, $sql_name_category, "Поиск по категории");
    }
    ?>
  </div>

  <div class="container mt-5">
    <h2>Products</h2>
    <table class="table">
      <thead>
        <tr>
          <th>Article</th>
          <th>Name</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Brand</th>
          <th>Category</th>
        </tr>
      </thead>
      <tbody>
        <?php

        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          // Вывод данных 
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["article"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["price"] . "</td>";
            echo "<td>" . $row["qty"] . "</td>";
            echo "<td>" . $row["brand_name"] . "</td>";
            echo "<td>" . $row["category_name"] . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='6'>Ошибка загрузки таблицы</td></tr>";
        }

        ?>
      </tbody>
    </table>
  </div>
</body>

</html>