<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

if (isset($_GET['reset_tab']) && $_GET['reset_tab'] === '1') {
    $_SESSION['history'] = array();

    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = array();
}

$currentExpression = '';
$currentResult = '';
$currentIsError = false;
$showResult = false;

function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatNumber($number)
{
    if (abs($number - round($number)) < 0.0000000001) {
        return (string)round($number);
    }

    $text = sprintf('%.10f', $number);
    $text = rtrim($text, '0');
    $text = rtrim($text, '.');

    return $text;
}

function bracketsAreValid($expression)
{
    $balance = 0;
    $length = strlen($expression);

    for ($i = 0; $i < $length; $i++) {
        if ($expression[$i] === '(') {
            $balance++;
        } elseif ($expression[$i] === ')') {
            $balance--;
            if ($balance < 0) {
                return false;
            }
        }
    }

    return $balance === 0;
}

function parseExpressionValue($expression, &$position)
{
    $value = parseTermValue($expression, $position);

    while ($position < strlen($expression)) {
        $char = $expression[$position];

        if ($char === '+') {
            $position++;
            $value += parseTermValue($expression, $position);
        } elseif ($char === '-') {
            $position++;
            $value -= parseTermValue($expression, $position);
        } else {
            break;
        }
    }

    return $value;
}

function parseTermValue($expression, &$position)
{
    $value = parseFactorValue($expression, $position);

    while ($position < strlen($expression)) {
        $char = $expression[$position];

        if ($char === '*') {
            $position++;
            $value *= parseFactorValue($expression, $position);
        } elseif ($char === '/') {
            $position++;
            $divider = parseFactorValue($expression, $position);

            if (abs($divider) < 0.0000000001) {
                throw new Exception('Деление на ноль.');
            }

            $value /= $divider;
        } else {
            break;
        }
    }

    return $value;
}

function parseFactorValue($expression, &$position)
{
    if ($position >= strlen($expression)) {
        throw new Exception('Ошибка в записи выражения.');
    }

    if ($expression[$position] === '+') {
        $position++;
        return parseFactorValue($expression, $position);
    }

    if ($expression[$position] === '-') {
        $position++;
        return -parseFactorValue($expression, $position);
    }

    if ($expression[$position] === '(') {
        $position++;
        $value = parseExpressionValue($expression, $position);

        if ($position >= strlen($expression) || $expression[$position] !== ')') {
            throw new Exception('Неправильная расстановка скобок.');
        }

        $position++;
        return $value;
    }

    return parseNumberValue($expression, $position);
}

function parseNumberValue($expression, &$position)
{
    $start = $position;
    $hasDigits = false;
    $dots = 0;
    $length = strlen($expression);

    while ($position < $length) {
        $char = $expression[$position];

        if ($char >= '0' && $char <= '9') {
            $hasDigits = true;
            $position++;
        } elseif ($char === '.') {
            $dots++;
            if ($dots > 1) {
                throw new Exception('Неправильная форма числа.');
            }
            $position++;
        } else {
            break;
        }
    }

    if (!$hasDigits) {
        throw new Exception('Ожидалось число.');
    }

    $numberText = substr($expression, $start, $position - $start);

    if ($numberText === '.' || $numberText === '') {
        throw new Exception('Неправильная форма числа.');
    }

    return (float)$numberText;
}

function calculateExpression($expression)
{
    $expression = str_replace(' ', '', $expression);

    if ($expression === '') {
        throw new Exception('Выражение не задано.');
    }

    if (!preg_match('/^[0-9+\-*\/().]+$/', $expression)) {
        throw new Exception('Недопустимые символы в выражении.');
    }

    if (!bracketsAreValid($expression)) {
        throw new Exception('Неправильная расстановка скобок.');
    }

    $position = 0;
    $result = parseExpressionValue($expression, $position);

    if ($position !== strlen($expression)) {
        throw new Exception('Ошибка в записи выражения.');
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['val'])) {
    $currentExpression = trim($_POST['val']);
    $showResult = true;

    try {
        $result = calculateExpression($currentExpression);
        $currentResult = formatNumber($result);
        $currentIsError = false;
    } catch (Exception $e) {
        $currentResult = $e->getMessage();
        $currentIsError = true;
    }

    $_SESSION['history'][] = array(
        'expression' => ($currentExpression === '' ? '[пустое выражение]' : $currentExpression),
        'result' => $currentResult,
        'is_error' => $currentIsError
    );
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Арифметический калькулятор</title>
    <link rel="stylesheet" href="styles.css">
</head>

<script>
(function () {
    if (!sessionStorage.getItem('calc_tab_opened')) {
        sessionStorage.setItem('calc_tab_opened', '1');

        if (window.location.pathname.indexOf('index.php') !== -1) {
            window.location.replace('index.php?reset_tab=1');
        } else {
            window.location.replace('?reset_tab=1');
        }
    }
})();
</script>

<body>
    <header class="header">
        <div class="container">
            <h1 class="header__title">Арифметический калькулятор</h1>
            <div class="header__subtitle">Лабораторная работа №10</div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <section class="card">
                <h2>Ввод выражения</h2>
                <p class="card__text">
                    Поддерживаются числа, знаки +, -, *, / и круглые скобки.
                </p>

                <form method="post" action="">
                    <div class="form__row">
                        <input
                            class="form__input"
                            type="text"
                            name="val"
                            placeholder="Например: (2+3)*4-5/2"
                            value="<?php echo h($currentExpression); ?>"
                        >
                        <button class="button" type="submit">Вычислить</button>
                    </div>
                </form>
            </section>

            <?php if ($showResult): ?>
                <section class="card">
                    <h2>Текущий результат</h2>
                    <div class="result <?php echo $currentIsError ? 'result--error' : 'result--success'; ?>">
                        <div class="result__expression">
                            <span class="label">Выражение:</span>
                            <?php echo h($currentExpression === '' ? '[пустое выражение]' : $currentExpression); ?>
                        </div>
                        <div class="result__value">
                            <span class="label"><?php echo $currentIsError ? 'Ошибка:' : 'Результат:'; ?></span>
                            <?php echo h($currentResult); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="card">
                <h2>История вычислений</h2>

                <?php if (count($_SESSION['history']) === 0): ?>
                    <div class="empty">История пока пуста.</div>
                <?php else: ?>
                    <div class="history">
                        <?php foreach ($_SESSION['history'] as $item): ?>
                            <div class="history__item <?php echo $item['is_error'] ? 'history__item--error' : ''; ?>">
                                <div class="history__left"><?php echo h($item['expression']); ?></div>
                                <div class="history__right"><?php echo h($item['result']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>