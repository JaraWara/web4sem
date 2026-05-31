<?php
date_default_timezone_set('Europe/Moscow');

$html_type = isset($_GET['html_type']) ? $_GET['html_type'] : '';
$content = isset($_GET['content']) ? (int)$_GET['content'] : 0;

$current_layout = ($html_type === 'DIV') ? 'DIV' : 'TABLE';

function renderExpression($i, $j) {
    $result = $i * $j;

    echo '<a href="?content=' . $i . '">' . $i . '</a>';
    echo ' × ';
    echo '<a href="?content=' . $j . '">' . $j . '</a>';
    echo ' = ';

    if ($result > 1 && $result < 10) {
        echo '<a href="?content=' . $result . '">' . $result . '</a>';
    } else {
        echo $result;
    }
}

function outTableForm($content) {
    echo '<div class="table-card">';
    echo '<h2 class="table-card__title">';
    echo ($content == 0) ? 'Вся таблица умножения' : 'Таблица умножения на ' . $content;
    echo '</h2>';

    echo '<table class="multiplication-table">';
    echo '<tbody>';

    for ($i = 2; $i <= 9; $i++) {
        if ($content == 0 || $content == $i) {
            echo '<tr>';
            for ($j = 2; $j <= 9; $j++) {
                echo '<td>';
                renderExpression($i, $j);
                echo '</td>';
            }
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

function outDivForm($content) {
    echo '<div class="blocks-list">';

    for ($i = 2; $i <= 9; $i++) {
        if ($content == 0 || $content == $i) {
            echo '<div class="block-card">';
            echo '<h2 class="block-card__title">Таблица умножения на ' . $i . '</h2>';
            echo '<div class="block">';

            for ($j = 2; $j <= 9; $j++) {
                echo '<div class="block__row">';
                renderExpression($i, $j);
                echo '</div>';
            }

            echo '</div>';
            echo '</div>';
        }
    }

    echo '</div>';
}
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Таблица умножения — ЛР №5</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="header">
  <div class="container header__inner">
    <div class="header__left">
      <img class="logo" src="logo.png" alt="Логотип университета">
      <div class="header__meta">
        <div class="header__line">ФИО: <span class="muted">Левченко Дмитрий Игоревич</span></div>
        <div class="header__line">Группа: <span class="muted">241-351</span></div>
        <div class="header__line">ЛР №5</div>
      </div>
      <div class="sidebar__title">Тип верстки</div>
        <nav class="nav nav--sidebar">
            <a class="nav__link <?php echo ($html_type === 'TABLE') ? 'nav__link--active' : ''; ?>" href="?html_type=TABLE&content=<?php echo $content; ?>">Табличная верстка</a>
            <a class="nav__link <?php echo ($html_type === 'DIV') ? 'nav__link--active' : ''; ?>" href="?html_type=DIV&content=<?php echo $content; ?>">Блочная верстка</a>
          </nav>
      </div>
    </div>
  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">
      <h1>Таблица умножения</h1>

      <div class="layout">
        <aside class="sidebar">

          <div class="sidebar__section">
            <div class="sidebar__title">Меню</div>
            <div class="menu menu--sidebar">
              <a class="menu__link <?php echo ($content == 0) ? 'menu__link--active' : ''; ?>" href="?content=0<?php echo ($html_type !== '') ? '&html_type=' . $html_type : ''; ?>">Вся таблица умножения</a>
              <?php for ($i = 2; $i <= 9; $i++): ?>
                <a class="menu__link <?php echo ($content == $i) ? 'menu__link--active' : ''; ?>" href="?content=<?php echo $i; ?><?php echo ($html_type !== '') ? '&html_type=' . $html_type : ''; ?>">Таблица умножения на <?php echo $i; ?></a>
              <?php endfor; ?>
            </div>
          </div>
        </aside>

        <div class="content">
          <?php
          if ($current_layout === 'TABLE') {
              outTableForm($content);
          } else {
              outDivForm($content);
          }
          ?>
        </div>
      </div>

    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>
      Тип верстки:
      <?php
      if ($html_type === 'TABLE') {
          echo 'Табличная верстка';
      } elseif ($html_type === 'DIV') {
          echo 'Блочная верстка';
      } else {
          echo 'не выбрана';
      }
      ?>
    </div>
    <div>Дата и время: <?php echo date('d.m.Y H:i:s'); ?></div>
  </div>
</footer>

</body>
</html>