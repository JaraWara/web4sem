<?php

$store = isset($_GET['store']) ? (string)$_GET['store'] : '';
$count = isset($_GET['count']) ? (int)$_GET['count'] : 0;

if (isset($_GET['key'])) {
  $key = (string)$_GET['key'];


  $count++;

  if ($key === 'reset') {
    $store = '';
  } else {
    if (ctype_digit($key) && strlen($key) === 1) {
      $store .= $key;
    }
  }
}

?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Левченко Дмитрий Игоревич — 241-351 — ЛР №3</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="header">
  <div class="container header__inner">
    <div class="header__left">
      <img class="logo" src="logo.png" alt="Логотип университета">
      <div class="header__meta">
        <div class="header__line">ФИО: <span>Левченко Дмитрий Игоревич</span></div>
        <div class="header__line">Группа: <span>241-351</span></div>
        <div class="header__line">ЛР №3</div>
      </div>
    </div>

    <nav class="nav">
      <a class="nav__link nav__link--active" href="index.php">Главная</a>
    </nav>
  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">
      <h1>Цифровая панель</h1>

      <div class="result">
        <?php echo $store; ?>
      </div>

      <div class="pad">
        <?php
          function btn($label, $key, $store, $count) {
            $href = 'index.php?key=' . $key . '&store=' . $store . '&count=' . $count;

            echo '<a class="btn" href="'.$href.'">'.$label.'</a>';
          }

          foreach (['1','2','3','4','5','6','7','8','9','0'] as $d) {
            btn($d, $d, $store, $count);
          }
        ?>
      </div>

      <div class="resetRow">
        <?php
          $resetHref = 'index.php?key=reset&store=' . $store . '&count=' . $count;
        ?>
        <a class="btn btn--reset" href="<?php echo $resetHref; ?>">СБРОС</a>
      </div>

    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>Нажатий: <?php echo $count; ?></div>
  </div>
</footer>

</body>
</html>