<?php
  $title = "City Guide — Идеи прогулок";
  $current = "page2";

  $img1 = (date('s') % 2 === 0) ? "fotos/foto2.jpg" : "fotos/foto1.jpg";
  $img2 = (date('s') % 2 === 0) ? "fotos/foto5.jpg" : "fotos/foto6.jpg";

  $row = [
    "Маршрут",
    "Центр → парк → кофе",
    "Простой план на 2–3 часа"
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
      <h1>Идеи прогулок</h1>

      <h2>Маршрут на вечер</h2>
      <p>
        Выбери одну центральную точку, затем пройди пешком в сторону парка или набережной.
        В конце сделай остановку в кафе или зайди в пекарню — это хороший финал прогулки.
      </p>
      <p>
        Чтобы прогулка не превратилась в гонку, оставь запас времени. Лучше пройти меньше,
        но спокойно: 6–8 тысяч шагов обычно хватает, чтобы “перезагрузиться”.
      </p>

      <h2>Мини-памятка</h2>
      <table class="table">
        <?php echo "<tr><th>План</th><th>Как выглядит</th><th>Сколько времени</th></tr>"; ?>
        <tr>
          <td><?php echo $row[0]; ?></td>
          <td><?php echo $row[1]; ?></td>
          <td><?php echo $row[2]; ?></td>
        </tr>
      </table>

      <h2>Фотографии</h2>
      <p class="small">При обновлении страницы изображения могут подставляться иначе.</p>
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