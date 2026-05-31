<?php
header('Content-Type: text/html; charset=UTF-8');

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function utf8_chars(string $text): array
{
    preg_match_all('/./us', $text, $matches);
    return $matches[0];
}

function utf8_len(string $text): int
{
    return count(utf8_chars($text));
}

function utf8_lower(string $text): string
{
    $upper = [
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я',
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    ];

    $lower = [
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'
    ];

    return str_replace($upper, $lower, $text);
}

function getCharStats(string $text): array
{
    $chars = utf8_chars(utf8_lower($text));
    $result = [];

    foreach ($chars as $char) {
        if (isset($result[$char])) {
            $result[$char]++;
        } else {
            $result[$char] = 1;
        }
    }

    ksort($result);
    return $result;
}

function getWordStats(string $text): array
{
    preg_match_all('/[A-Za-zА-Яа-яЁё]+/u', utf8_lower($text), $matches);
    $words = $matches[0];
    $result = [];

    foreach ($words as $word) {
        if (isset($result[$word])) {
            $result[$word]++;
        } else {
            $result[$word] = 1;
        }
    }

    ksort($result);
    return $result;
}

$text = $_POST['data'] ?? '';
$text = trim($text);

$hasText = ($text !== '');

$charCount = 0;
$letterCount = 0;
$lowerCount = 0;
$upperCount = 0;
$punctCount = 0;
$digitCount = 0;
$wordCount = 0;
$charStats = [];
$wordStats = [];

if ($hasText) {
    $charCount = utf8_len($text);

    preg_match_all('/[A-Za-zА-Яа-яЁё]/u', $text, $letters);
    $letterCount = count($letters[0]);

    preg_match_all('/[a-zа-яё]/u', $text, $lowerLetters);
    $lowerCount = count($lowerLetters[0]);

    preg_match_all('/[A-ZА-ЯЁ]/u', $text, $upperLetters);
    $upperCount = count($upperLetters[0]);

    preg_match_all('/[.,!?:;"\'()\-\—\«\»]/u', $text, $punctuation);
    $punctCount = count($punctuation[0]);

    preg_match_all('/\d/u', $text, $digits);
    $digitCount = count($digits[0]);

    preg_match_all('/[A-Za-zА-Яа-яЁё]+/u', $text, $words);
    $wordCount = count($words[0]);

    $charStats = getCharStats($text);
    $wordStats = getWordStats($text);
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Результат анализа</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="header__inner">
        <div>
          <h1 class="header__title">Результат анализа текста</h1>
          <div class="header__meta">PHP-анализатор</div>
        </div>
      </div>
    </div>
  </header>

  <main class="main">
    <div class="container">
      <section class="card">
        <h2>Исходный текст</h2>

        <?php if ($hasText): ?>
          <div class="source-text"><?= nl2br(h($text)) ?></div>
        <?php else: ?>
          <div class="empty-text">Нет текста для анализа</div>
        <?php endif; ?>
      </section>

      <?php if ($hasText): ?>
        <section class="card">
          <h2>Информация о тексте</h2>

          <table class="result-table">
            <tr>
              <th>Параметр</th>
              <th>Значение</th>
            </tr>
            <tr>
              <td>Количество символов в тексте (включая пробелы)</td>
              <td><?= $charCount ?></td>
            </tr>
            <tr>
              <td>Количество букв</td>
              <td><?= $letterCount ?></td>
            </tr>
            <tr>
              <td>Количество строчных букв</td>
              <td><?= $lowerCount ?></td>
            </tr>
            <tr>
              <td>Количество заглавных букв</td>
              <td><?= $upperCount ?></td>
            </tr>
            <tr>
              <td>Количество знаков препинания</td>
              <td><?= $punctCount ?></td>
            </tr>
            <tr>
              <td>Количество цифр</td>
              <td><?= $digitCount ?></td>
            </tr>
            <tr>
              <td>Количество слов</td>
              <td><?= $wordCount ?></td>
            </tr>
          </table>
        </section>

        <section class="card">
          <h2>Количество вхождений каждого символа (без учёта регистра)</h2>

          <table class="result-table">
            <tr>
              <th>Символ</th>
              <th>Количество</th>
            </tr>
            <?php foreach ($charStats as $char => $count): ?>
              <tr>
                <td>
                  <?php
                  if ($char === ' ') {
                      echo '[пробел]';
                  } elseif ($char === "\n") {
                      echo '[перенос строки]';
                  } elseif ($char === "\t") {
                      echo '[табуляция]';
                  } else {
                      echo h($char);
                  }
                  ?>
                </td>
                <td><?= $count ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </section>

        <section class="card">
          <h2>Список всех слов и количество их вхождений</h2>

          <table class="result-table">
            <tr>
              <th>Слово</th>
              <th>Количество</th>
            </tr>
            <?php foreach ($wordStats as $word => $count): ?>
              <tr>
                <td><?= h($word) ?></td>
                <td><?= $count ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </section>
      <?php endif; ?>

      <section class="card card--actions">
        <a class="button button--link" href="index.html">Другой анализ</a>
      </section>
    </div>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer__inner">
        <div>Страница результата</div>
      </div>
    </div>
  </footer>
</body>
</html>