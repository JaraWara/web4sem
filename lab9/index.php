<?php
header('Content-Type: text/html; charset=UTF-8');

define('APP_STARTED', true);
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'dima666');
define('DB_NAME', 'friends');

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function connectDb(): mysqli
{
    static $db = null;

    if ($db instanceof mysqli) {
        return $db;
    }

    if (function_exists('mysqli_report') && defined('MYSQLI_REPORT_OFF')) {
        mysqli_report(MYSQLI_REPORT_OFF);
    }

    $db = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($db->connect_errno) {
        throw new Exception('Не удалось подключиться к базе данных: ' . $db->connect_error);
    }

    if (!$db->set_charset('utf8mb4')) {
        throw new Exception('Не удалось установить кодировку utf8mb4 для соединения с БД.');
    }

    return $db;
}

function normalizePage(string $page): string
{
    $allowed = ['view', 'add', 'edit', 'delete'];
    return in_array($page, $allowed, true) ? $page : 'view';
}

function normalizeSort(string $sort): string
{
    $allowed = ['byid', 'bysurname', 'bybirth'];
    return in_array($sort, $allowed, true) ? $sort : 'byid';
}

function normalizeId($value): int
{
    return (is_numeric($value) && (int)$value > 0) ? (int)$value : 0;
}

function normalizeGender(string $value): string
{
    return in_array($value, ['Мужской', 'Женский'], true) ? $value : 'Мужской';
}

function formatDate(?string $value): string
{
    if (!$value || $value === '0000-00-00') {
        return '—';
    }

    $date = date_create($value);
    return $date ? $date->format('d.m.Y') : '—';
}

function calculateAge(?string $value): string
{
    if (!$value || $value === '0000-00-00') {
        return '—';
    }

    $date = date_create($value);
    if (!$date) {
        return '—';
    }

    $now = new DateTime();
    return (string)$date->diff($now)->y;
}

function firstLetter(string $text): string
{
    $text = trim($text);

    if ($text === '') {
        return '';
    }

    if (preg_match('/./u', $text, $matches)) {
        return $matches[0];
    }

    return '';
}

function shortName(array $row): string
{
    $surname = trim((string)($row['surname'] ?? ''));
    $name = firstLetter((string)($row['name'] ?? ''));
    $patronymic = firstLetter((string)($row['patronymic'] ?? ''));

    $result = $surname;

    if ($name !== '') {
        $result .= ' ' . $name . '.';
    }

    if ($patronymic !== '') {
        $result .= $patronymic . '.';
    }

    return trim($result);
}

$pageKey = normalizePage($_GET['p'] ?? 'view');
$sortKey = normalizeSort($_GET['sort'] ?? 'byid');
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Телефонная книга — ЛР 9</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="header__inner">
        <div>
          <h1 class="header__title">Телефонная книга</h1>
          <div class="header__meta">Лабораторная работа 9</div>
        </div>
      </div>
    </div>
  </header>

  <main class="main">
    <div class="container">
      <?php
      require __DIR__ . '/menu.php';
      echo renderMenu($pageKey, $sortKey);

      switch ($pageKey) {
          case 'add':
              include __DIR__ . '/add.php';
              break;

          case 'edit':
              include __DIR__ . '/edit.php';
              break;

          case 'delete':
              include __DIR__ . '/delete.php';
              break;

          case 'view':
          default:
              include __DIR__ . '/viewer.php';
              break;
      }
      ?>
    </div>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer__inner">
      </div>
    </div>
  </footer>
</body>
</html>
