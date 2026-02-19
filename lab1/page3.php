<?php
  $title = "City Guide — Контакты";
  $current = "page3";

  $img1 = (date('s') % 2 === 0) ? "fotos/foto6.jpg" : "fotos/foto5.jpg";
  $img2 = (date('s') % 2 === 0) ? "fotos/foto4.jpg" : "fotos/foto3.jpg";

  $row = [
    "Email",
    "hello@cityguide.test",
    "Для предложений и замечаний"
  ];
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
  <div class="container header__inner">
    <div class="brand">City Guide</div>

    <nav class="nav">
      <a href="<?php echo 'index.php'; ?>" <?php
          $name = "Главная";
          $is_current = ($current === "index");
          $class = $is_current ? "menu__link menu__link--active" : "menu__link";
          echo 'class="'.$class.'">'.$name;
      ?></a>

      <a href="<?php echo 'page2.php'; ?>" <?php
          $name = "Идеи прогулок";
          $is_current = ($current === "page2");
          $class = $is_current ? "menu__link menu__link--active" : "menu__link";
          echo 'class="'.$class.'">'.$name;
      ?></a>

      <a href="<?php echo 'page3.php'; ?>" <?php
          $name = "Контакты";
          $is_current = ($current === "page3");
          $class = $is_current ? "menu__link menu__link--active" : "menu__link";
          echo 'class="'.$class.'">'.$name;
      ?></a>
    </nav>
  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">
      <h1>Контакты</h1>

      <h2>Написать нам</h2>
      <p>
        Если хочешь предложить интересное место для прогулки или исправить неточность,
        просто напиши. Сообщения читаются, а лучшие идеи добавляются в подборки.
      </p>

      <h2>Контактная таблица</h2>
      <table class="table">
        <?php echo "<tr><th>Канал</th><th>Адрес</th><th>Комментарий</th></tr>"; ?>
        <tr>
          <td><?php echo $row[0]; ?></td>
          <td><?php echo $row[1]; ?></td>
          <td><?php echo $row[2]; ?></td>
        </tr>
      </table>

      <h2>Фотографии</h2>
      <p class="small">Картинки подставляются автоматически.</p>
      <div class="gallery">
        <img src="<?php echo $img1; ?>" alt="Фото 1">
        <img src="<?php echo $img2; ?>" alt="Фото 2">
      </div>
    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>City Guide</div>
    <div class="footer__meta">Сформировано <?php echo date('d.m.Y H:i:s'); ?></div>
  </div>
</footer>

</body>
</html>