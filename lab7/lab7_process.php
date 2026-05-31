<?php

date_default_timezone_set('Europe/Moscow');

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function parseNumber(string $value)
{
    $normalized = str_replace(' ', '', trim($value));
    $normalized = str_replace(',', '.', $normalized);

    if ($normalized === '' || !is_numeric($normalized)) {
        return null;
    }

    return $normalized + 0;
}

function formatNumber($value): string
{
    if (!is_numeric($value)) {
        return (string)$value;
    }

    if ((float)$value == (int)$value) {
        return (string)(int)$value;
    }

    $formatted = number_format((float)$value, 6, '.', '');
    return rtrim(rtrim($formatted, '0'), '.');
}

function formatArray(array $array): string
{
    $parts = [];
    foreach ($array as $item) {
        $parts[] = formatNumber($item);
    }
    return '[' . implode(', ', $parts) . ']';
}

function addStep(array &$steps, int &$iteration, array $array): void
{
    $iteration++;
    $steps[] = [
        'number' => $iteration,
        'array' => $array
    ];
}

function selectionSort(array $array, array &$steps, int &$iteration): array
{
    $count = count($array);

    for ($i = 0; $i < $count - 1; $i++) {
        $minIndex = $i;

        for ($j = $i + 1; $j < $count; $j++) {
            if ($array[$j] < $array[$minIndex]) {
                $minIndex = $j;
            }
            addStep($steps, $iteration, $array);
        }

        if ($minIndex !== $i) {
            $temp = $array[$i];
            $array[$i] = $array[$minIndex];
            $array[$minIndex] = $temp;
            addStep($steps, $iteration, $array);
        }
    }

    return $array;
}

function bubbleSort(array $array, array &$steps, int &$iteration): array
{
    $count = count($array);

    for ($i = 0; $i < $count - 1; $i++) {
        for ($j = 0; $j < $count - 1 - $i; $j++) {
            if ($array[$j] > $array[$j + 1]) {
                $temp = $array[$j];
                $array[$j] = $array[$j + 1];
                $array[$j + 1] = $temp;
                addStep($steps, $iteration, $array);
            } else {
                addStep($steps, $iteration, $array);
            }
        }
    }

    return $array;
}

function shellSort(array $array, array &$steps, int &$iteration): array
{
    $count = count($array);
    $gap = intdiv($count, 2);

    while ($gap > 0) {
        for ($i = $gap; $i < $count; $i++) {
            $current = $array[$i];
            $j = $i;

            while ($j >= $gap && $array[$j - $gap] > $current) {
                $array[$j] = $array[$j - $gap];
                $j -= $gap;
                addStep($steps, $iteration, $array);
            }

            $array[$j] = $current;
            addStep($steps, $iteration, $array);
        }

        $gap = intdiv($gap, 2);
    }

    return $array;
}

function gnomeSort(array $array, array &$steps, int &$iteration): array
{
    $count = count($array);
    $index = 1;

    while ($index < $count) {
        if ($index === 0 || $array[$index - 1] <= $array[$index]) {
            addStep($steps, $iteration, $array);
            $index++;
        } else {
            $temp = $array[$index];
            $array[$index] = $array[$index - 1];
            $array[$index - 1] = $temp;
            addStep($steps, $iteration, $array);
            $index--;
        }
    }

    return $array;
}

function quickSortRecursive(array &$array, int $left, int $right, array &$steps, int &$iteration): void
{
    $i = $left;
    $j = $right;
    $pivot = $array[intdiv($left + $right, 2)];

    while ($i <= $j) {
        while ($array[$i] < $pivot) {
            $i++;
            addStep($steps, $iteration, $array);
        }

        while ($array[$j] > $pivot) {
            $j--;
            addStep($steps, $iteration, $array);
        }

        if ($i <= $j) {
            $temp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $temp;
            addStep($steps, $iteration, $array);
            $i++;
            $j--;
        }
    }

    if ($left < $j) {
        quickSortRecursive($array, $left, $j, $steps, $iteration);
    }

    if ($i < $right) {
        quickSortRecursive($array, $i, $right, $steps, $iteration);
    }
}

function quickSortWithSteps(array $array, array &$steps, int &$iteration): array
{
    if (count($array) > 1) {
        quickSortRecursive($array, 0, count($array) - 1, $steps, $iteration);
    }
    return $array;
}

function builtinSort(array $array, array &$steps, int &$iteration, string &$note): array
{
    addStep($steps, $iteration, $array);
    sort($array, SORT_NUMERIC);
    addStep($steps, $iteration, $array);
    $note = 'Внутренние итерации встроенной функции sort() недоступны в пользовательском PHP-коде, поэтому показаны только исходное и итоговое состояния массива.';
    return $array;
}

$algorithms = [
    'selection' => 'Сортировка выбором',
    'bubble' => 'Пузырьковый алгоритм',
    'shell' => 'Алгоритм Шелла',
    'gnome' => 'Алгоритм садового гнома',
    'quick' => 'Быстрая сортировка',
    'builtin' => 'Встроенная функция PHP sort()',
];

$selectedAlgorithm = $_POST['algorithm'] ?? 'selection';
if (!array_key_exists($selectedAlgorithm, $algorithms)) {
    $selectedAlgorithm = 'selection';
}

$rawElements = isset($_POST['element']) && is_array($_POST['element']) ? $_POST['element'] : [];
$rawElements = array_map(static function ($item) {
    return trim((string)$item);
}, $rawElements);

$displayInput = $rawElements;
$validationMessage = '';
$validationOk = false;
$errors = [];
$numbers = [];
$steps = [];
$iterationCount = 0;
$timeSpent = null;
$sortedArray = [];
$finalMessage = '';
$builtinNote = '';

$hasAnyFilledValue = false;
foreach ($rawElements as $value) {
    if ($value !== '') {
        $hasAnyFilledValue = true;
        break;
    }
}

if (!$hasAnyFilledValue) {
    $validationMessage = 'Предупреждение: входные данные отсутствуют, сортировка не выполнена.';
} else {
    foreach ($rawElements as $index => $value) {
        if ($value === '') {
            $errors[] = 'Элемент с индексом ' . $index . ' не заполнен.';
            continue;
        }

        $parsed = parseNumber($value);
        if ($parsed === null) {
            $errors[] = 'Элемент с индексом ' . $index . ' содержит нечисловое значение: ' . $value;
            continue;
        }

        $numbers[] = $parsed;
    }

    if ($errors) {
        $validationMessage = 'Предупреждение: найдены некорректные входные данные, сортировка не выполнена.';
    } else {
        $validationOk = true;
        $validationMessage = 'Проверка пройдена: все элементы массива являются числами.';

        $start = microtime(true);
        switch ($selectedAlgorithm) {
            case 'selection':
                $sortedArray = selectionSort($numbers, $steps, $iterationCount);
                break;
            case 'bubble':
                $sortedArray = bubbleSort($numbers, $steps, $iterationCount);
                break;
            case 'shell':
                $sortedArray = shellSort($numbers, $steps, $iterationCount);
                break;
            case 'gnome':
                $sortedArray = gnomeSort($numbers, $steps, $iterationCount);
                break;
            case 'quick':
                $sortedArray = quickSortWithSteps($numbers, $steps, $iterationCount);
                break;
            case 'builtin':
                $sortedArray = builtinSort($numbers, $steps, $iterationCount, $builtinNote);
                break;
            default:
                $sortedArray = selectionSort($numbers, $steps, $iterationCount);
        }
        $timeSpent = microtime(true) - $start;
        $finalMessage = 'Сортировка завершена, проведено ' . $iterationCount . ' итерац' . (($iterationCount % 100 === 1) ? 'ия' : ((in_array($iterationCount % 10, [2,3,4], true) && !in_array($iterationCount % 100, [12,13,14], true)) ? 'ии' : 'ий')) . '. Сортировка заняла ' . number_format($timeSpent, 6, '.', '') . ' секунд.';
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Результаты сортировки — ЛР №7</title>
  <link rel="stylesheet" href="lab7_styles.css">
</head>
<body>
<header class="header">
  <div class="container header__inner">
    <div class="header__left">
      <img class="logo" src="logo.png" alt="Логотип университета">
      <div class="header__meta">
        <div class="header__line">ФИО: <span class="muted">Левченко Дмитрий Игоревич</span></div>
        <div class="header__line">Группа: <span class="muted">241-351</span></div>
        <div class="header__line">ЛР №7</div>
      </div>
    </div>
  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">
      <div class="page-head">
        <div>
          <h1>Отчет о сортировке массива</h1>
          <p class="lead">Ниже показаны выбранный алгоритм, входные данные, результат проверки и все зафиксированные итерации сортировки.</p>
        </div>
        <a class="button button--secondary" href="lab7_form.php">Вернуться к форме</a>
      </div>

      <div class="report-grid">
        <div class="info-box">
          <div class="info-box__label">Алгоритм</div>
          <div class="info-box__value"><?= h($algorithms[$selectedAlgorithm]) ?></div>
        </div>
        <div class="info-box">
          <div class="info-box__label">Входные данные</div>
          <div class="info-box__value">
            <?php if ($displayInput): ?>
              <div class="chips">
                <?php foreach ($displayInput as $item): ?>
                  <span class="chip"><?= h($item === '' ? '∅' : $item) ?></span>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <span class="muted-dark">Нет переданных элементов</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="info-box">
          <div class="info-box__label">Проверка валидности</div>
          <div class="info-box__value">
            <span class="status <?= $validationOk ? 'status--ok' : 'status--error' ?>"><?= h($validationMessage) ?></span>
          </div>
        </div>
        <div class="info-box">
          <div class="info-box__label">Результат сортировки</div>
          <div class="info-box__value">
            <?php if ($validationOk): ?>
              <strong><?= h(formatArray($sortedArray)) ?></strong>
            <?php else: ?>
              <span class="muted-dark">Сортировка не выполнялась</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php if ($errors): ?>
        <div class="warning-block">
          <h2>Найденные проблемы</h2>
          <ul class="warning-list">
            <?php foreach ($errors as $error): ?>
              <li><?= h($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($validationOk): ?>
        <?php if ($builtinNote !== ''): ?>
          <div class="note-block"><?= h($builtinNote) ?></div>
        <?php endif; ?>

        <div class="summary-block">
          <h2>Итог</h2>
          <p><?= h($finalMessage) ?></p>
        </div>

        <div class="steps-block">
          <h2>Ход сортировки</h2>
          <div class="table-wrap">
            <table class="steps-table">
              <thead>
                <tr>
                  <th>№ итерации</th>
                  <th>Текущее состояние массива</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($steps as $step): ?>
                  <tr>
                    <td><?= h($step['number']) ?></td>
                    <td><code><?= h(formatArray($step['array'])) ?></code></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php else: ?>
        <div class="warning-block">
          <h2>Обработка остановлена</h2>
          <p>Алгоритм не запускался, потому что входной массив отсутствует или содержит некорректные значения.</p>
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>Время формирования отчета</div>
    <div><?= date('d.m.Y H:i:s') ?></div>
  </div>
</footer>
</body>
</html>
