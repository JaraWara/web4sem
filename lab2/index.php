<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Левченко Дмитрий Игоревич — 241-351 — ЛР №2 — Вариант 4</title>
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
        <div class="header__line">ЛР №<span>2</span>, вариант 4</div>
      </div>
    </div>

    <nav class="nav">
      <a class="nav__link nav__link--active" href="index.php">Главная</a>
      <a class="nav__link" href="#result">Результат</a>
      <a class="nav__link" href="#stats">Статистика</a>
    </nav>
  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">
      <h1 id="result">Вычисление f(x) (вариант 4)</h1>

<?php
$x0    = 0;
$n     = 25;
$step  = 1;

$f_min = -1000;
$f_max =  1000;

$type = 'E';


$x = $x0;

$values = [];

function calc_f_variant4($x) {

  if ($x <= 10) {
    $den = 1 - $x / 5;
    if ($den == 0) {
      return "error"; 
    }
    return (5 - $x) / $den;
  }

  if ($x < 20) {
    return ($x * $x) / 4 + 7;
  }

  return 2 * $x - 21;
}

if ($type === 'B') {
  echo "<ul>";
} elseif ($type === 'C') {
  echo "<ol>";
} elseif ($type === 'D') {
  echo "<table class=\"table table--thin\">";
  echo "<tr><th>#</th><th>x</th><th>f(x)</th></tr>";
} elseif ($type === 'E') {
  echo "<div class=\"blocks\">";
}

for ($i = 1; $i <= $n; $i++) {
  $f = calc_f_variant4($x);

  $x_out = round($x, 3);

  if (is_numeric($f)) {
    $f_rounded = round((float)$f, 3);
    $f_out = number_format($f_rounded, 3, '.');
    $values[] = $f_rounded;
  } else {
    $f_out = "error";
  }

  if ($type === 'A') {
    echo "f($x_out)=$f_out";
    if ($i < $n) echo "<br>";
  }
  elseif ($type === 'B') {
    echo "<li>f($x_out)=$f_out</li>";
  }
  elseif ($type === 'C') {
    echo "<li>f($x_out)=$f_out</li>";
  }
  elseif ($type === 'D') {
    echo "<tr><td>$i</td><td>$x_out</td><td>$f_out</td></tr>";
  }
  elseif ($type === 'E') {
    echo "<div class=\"block\">f($x_out)=$f_out</div>";
  }

  if (is_numeric($f)) {
    if ($f >= $f_max || $f <= $f_min) {
      break;
    }
  }

  $x += $step;
}

if ($type === 'B') {
  echo "</ul>";
} elseif ($type === 'C') {
  echo "</ol>";
} elseif ($type === 'D') {
  echo "</table>";
} elseif ($type === 'E') {
  echo "</div>";
}

$sum = 0.0;
$min = null;
$max = null;

foreach ($values as $v) {
  $sum += $v;
  if ($min === null || $v < $min) $min = $v;
  if ($max === null || $v > $max) $max = $v;
}

$count_num = count($values);
$avg = ($count_num > 0) ? ($sum / $count_num) : null;

$sum_out = ($count_num > 0) ? number_format(round($sum, 3), 3, '.') : "error";
$min_out = ($count_num > 0) ? number_format(round($min, 3), 3, '.') : "error";
$max_out = ($count_num > 0) ? number_format(round($max, 3), 3, '.') : "error";
$avg_out = ($count_num > 0) ? number_format(round($avg, 3), 3, '.') : "error";
?>

      <h2 id="stats">Статистика по вычисленным значениям</h2>
      <table class="table table--thin">
        <tr><th>Параметр</th><th>Значение</th></tr>
        <tr><td>Сумма</td><td><?php echo $sum_out; ?></td></tr>
        <tr><td>Минимум</td><td><?php echo $min_out; ?></td></tr>
        <tr><td>Максимум</td><td><?php echo $max_out; ?></td></tr>
        <tr><td>Среднее</td><td><?php echo $avg_out; ?></td></tr>
      </table>

      <h2></h2>

    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>Тип верстки: <?php echo $type; ?></div>
  </div>
</footer>

</body>
</html>