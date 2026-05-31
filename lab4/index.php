<?php

$cols = 2;

$structures = [
  'A1*A2*A3*A4#B1*B2*B3*B4#C1*C2*C3*C4',
  '1*2*3*4#5*6*7*8#9*10*11*12',
  'Яблоко*Груша*Слива*Персик#Клубника*Малина* *Черника',      
  'Москва*СПб*Казань#Сочи*#Тула*Омск',                      
  'C1*C2#D1*D2*D3*D4*D5',                                    
  '***#X1*X2*X3*X4',                                         
  'P1*P2*P3*P4#',                                            
  '#Q1*Q2*Q3*Q4',                                             
  'R1*R2*R3*R4#S1*S2*S3*S4#T1*T2*T3*T4#U1*U2*U3*U4',
  '',
  '#'                                                          
];

function makeTR(string $rowStr, int $cols): string
{
    $rowStr = trim($rowStr);

    if ($rowStr === '') {
        return '';
    }

    $cells = explode('*', $rowStr);

    $hasAnyCell = false;
    foreach ($cells as $c) {
        if (trim($c) !== '') {
            $hasAnyCell = true;
            break;
        }
    }
    if (!$hasAnyCell) {
        return '';
    }

    if (count($cells) < $cols) {
        $cells = array_pad($cells, $cols, '');
    } elseif (count($cells) > $cols) {
        $cells = array_slice($cells, 0, $cols);
    }

    $html = '<tr>';
    for ($i = 0; $i < $cols; $i++) {
        $val = (string)$cells[$i];
        $html .= '<td>' . $val . '</td>';
    }
    $html .= '</tr>';

    return $html;
}

function outTable(string $structure, int $cols, int $index): void
{
    echo '<h2>Таблица №' . $index . '</h2>';

    if (trim($structure) === '') {
        echo '<div class="msg">В таблице нет строк</div>';
        return;
    }

    $rows = explode('#', $structure);

    if (count($rows) === 0) {
        echo '<div class="msg">В таблице нет строк</div>';
        return;
    }

    $tbody = '';
    foreach ($rows as $r) {
        $tr = makeTR($r, $cols);
        if ($tr !== '') {
            $tbody .= $tr;
        }
    }

    if ($tbody === '') {
        echo '<div class="msg">В таблице нет строк с ячейками</div>';
        return;
    }

    echo '<table class="table table--thin">';
    echo $tbody;
    echo '</table>';
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Левченко Дмитрий Игоревич — 241-351 — ЛР №4</title>
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
        <div class="header__line">ЛР №4</div>
      </div>
    </div>

  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">

      <?php if ($cols <= 0): ?>
        <div class="msg msg--bad">Неправильное число колонок</div>
      <?php else: ?>
        <?php
          for ($i = 0; $i < count($structures); $i++) {
              outTable($structures[$i], $cols, $i + 1);
          }
        ?>
      <?php endif; ?>

    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>Колонок: <?php echo $cols; ?></div>
  </div>
</footer>

</body>
</html>