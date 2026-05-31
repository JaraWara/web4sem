<?php

date_default_timezone_set('Europe/Moscow');

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function randomValue(): int
{
    return mt_rand(0, 100);
}

function parseNumber(?string $value): ?float
{
    if ($value === null) {
        return null;
    }

    $value = trim(str_replace(',', '.', $value));
    if ($value === '' || !is_numeric($value)) {
        return null;
    }

    return (float)$value;
}

function formatNumber($value): string
{
    if ($value === null || $value === '') {
        return '—';
    }

    if (!is_numeric($value)) {
        return (string)$value;
    }

    $formatted = number_format((float)$value, 2, '.', '');
    $formatted = rtrim(rtrim($formatted, '0'), '.');

    return $formatted === '' ? '0' : $formatted;
}

function solveTask(string $task, float $a, float $b, float $c): array
{
    switch ($task) {
        case 'triangle_area':
            if ($a <= 0 || $b <= 0 || $c <= 0 || $a + $b <= $c || $a + $c <= $b || $b + $c <= $a) {
                return [
                    'label' => 'Площадь треугольника',
                    'result' => null,
                    'error' => 'По введённым значениям нельзя построить треугольник.',
                ];
            }
            $p = ($a + $b + $c) / 2;
            $result = sqrt($p * ($p - $a) * ($p - $b) * ($p - $c));
            return [
                'label' => 'Площадь треугольника',
                'result' => round($result, 2),
                'error' => '',
            ];

        case 'triangle_perimeter':
            return [
                'label' => 'Периметр треугольника',
                'result' => round($a + $b + $c, 2),
                'error' => '',
            ];

        case 'parallelepiped_volume':
            return [
                'label' => 'Объём параллелепипеда',
                'result' => round($a * $b * $c, 2),
                'error' => '',
            ];

        case 'mean':
            return [
                'label' => 'Среднее арифметическое',
                'result' => round(($a + $b + $c) / 3, 2),
                'error' => '',
            ];

        case 'maximum':
            return [
                'label' => 'Максимум из трёх чисел',
                'result' => round(max($a, $b, $c), 2),
                'error' => '',
            ];

        case 'sum_squares':
            return [
                'label' => 'Сумма квадратов чисел',
                'result' => round($a * $a + $b * $b + $c * $c, 2),
                'error' => '',
            ];

        default:
            return [
                'label' => 'Среднее арифметическое',
                'result' => round(($a + $b + $c) / 3, 2),
                'error' => '',
            ];
    }
}

$tasks = [
    'triangle_area' => 'Площадь треугольника',
    'triangle_perimeter' => 'Периметр треугольника',
    'parallelepiped_volume' => 'Объём параллелепипеда',
    'mean' => 'Среднее арифметическое',
    'maximum' => 'Максимум из трёх чисел',
    'sum_squares' => 'Сумма квадратов чисел',
];

$viewModes = [
    'browser' => 'Версия для просмотра в браузере',
    'print' => 'Версия для печати',
];

$isSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['A'], $_POST['B'], $_POST['C']);

$fio = isset($_GET['fio']) ? trim($_GET['fio']) : '';
$group = isset($_GET['group']) ? trim($_GET['group']) : '';
$about = '';
$mail = '';
$task = 'mean';
$viewMode = 'browser';
$sendMail = false;
$userAnswerRaw = '';
$aRaw = (string)randomValue();
$bRaw = (string)randomValue();
$cRaw = (string)randomValue();

$taskLabel = '';
$programResult = null;
$programError = '';
$userAnswerDisplay = '';
$conclusion = '';
$emailMessage = '';
$emailNote = 'Локально на Open Server/XAMPP функция mail() обычно не отправляет письма без настроенного SMTP.';

if ($isSubmitted) {
    $fio = trim($_POST['FIO'] ?? '');
    $group = trim($_POST['GROUP'] ?? '');
    $about = trim($_POST['ABOUT'] ?? '');
    $mail = trim($_POST['MAIL'] ?? '');
    $task = $_POST['TASK'] ?? 'mean';
    $viewMode = $_POST['VIEW_MODE'] ?? 'browser';
    $sendMail = isset($_POST['send_mail']);
    $userAnswerRaw = trim($_POST['RESULT'] ?? '');

    $aRaw = trim($_POST['A'] ?? '');
    $bRaw = trim($_POST['B'] ?? '');
    $cRaw = trim($_POST['C'] ?? '');

    $a = parseNumber($aRaw);
    $b = parseNumber($bRaw);
    $c = parseNumber($cRaw);

    if ($a === null || $b === null || $c === null) {
        $taskLabel = $tasks[$task];
        $programError = 'Одно или несколько значений A, B, C введены некорректно.';
        $programResult = null;
    } else {
        $solution = solveTask($task, $a, $b, $c);
        $taskLabel = $solution['label'];
        $programResult = $solution['result'];
        $programError = $solution['error'];
    }

    if ($userAnswerRaw === '') {
        $userAnswerDisplay = 'Задача самостоятельно решена не была.';
    } else {
        $parsedAnswer = parseNumber($userAnswerRaw);
        $userAnswerDisplay = $parsedAnswer === null
            ? 'Введено некорректное значение.'
            : formatNumber($parsedAnswer);
    }

    if ($programError !== '') {
        $conclusion = 'Ошибка: тест не пройден';
    } else {
        $parsedAnswer = parseNumber($userAnswerRaw);
        if ($parsedAnswer === null) {
            $conclusion = 'Ошибка: тест не пройден';
        } else {
            $conclusion = abs($parsedAnswer - (float)$programResult) < 0.01
                ? 'Тест пройден'
                : 'Ошибка: тест не пройден';
        }
    }

    if ($sendMail) {
        if ($mail !== '') {
            $mailText = "ФИО: {$fio}\r\n";
            $mailText .= "Группа: {$group}\r\n";
            $mailText .= "Немного о себе: " . ($about !== '' ? $about : 'Не указано') . "\r\n";
            $mailText .= "Решаемая задача: {$taskLabel}\r\n";
            $mailText .= "Входные данные: A=" . $aRaw . ", B=" . $bRaw . ", C=" . $cRaw . "\r\n";
            $mailText .= "Предполагаемый результат: {$userAnswerDisplay}\r\n";
            $mailText .= "Вычисленный программой результат: " . ($programError !== '' ? $programError : formatNumber($programResult)) . "\r\n";
            $mailText .= "Вывод: {$conclusion}\r\n";

            @mail(
                $mail,
                'Результаты тестирования',
                $mailText,
                "From: auto@mail.ru\r\nContent-Type: text/plain; charset=UTF-8\r\n"
            );

            $emailMessage = 'Результаты теста были автоматически отправлены на e-mail ' . $mail . '.';
        } else {
            $emailMessage = 'Флажок отправки установлен, но e-mail не указан.';
        }
    }
}

$bodyClass = $isSubmitted && $viewMode === 'print' ? 'view-print' : 'view-browser';
$repeatLink = '?fio=' . urlencode($fio) . '&group=' . urlencode($group);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ЛР №6 — Обработка формы</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="<?= $bodyClass ?>">
<header class="header">
    <div class="container header__inner">
        <div class="header__left">
            <img class="logo" src="logo.png" alt="Логотип университета">
            <div class="header__meta">
                <div class="header__line">ФИО: <span class="muted"><?= $fio !== '' ? h($fio) : 'Впишите' ?></span></div>
                <div class="header__line">Группа: <span class="muted"><?= $group !== '' ? h($group) : 'Впишите' ?></span></div>
                <div class="header__line">ЛР №6</div>
            </div>
        </div>
        <div class="header__right">Математический тест</div>
    </div>
</header>

<main class="main">
    <div class="container">
        <?php if (!$isSubmitted): ?>
            <section class="card">
                <h1>Проверка математической задачи</h1>
                <p class="subtitle">Заполните форму, выберите задачу и введите свой предполагаемый ответ.</p>

                <form class="form" method="post" action="">
                    <div class="form__row">
                        <label class="form__label" for="fio">ФИО</label>
                        <input class="form__control" type="text" name="FIO" id="fio" value="<?= h($fio) ?>" required>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="group">Номер группы</label>
                        <input class="form__control" type="text" name="GROUP" id="group" value="<?= h($group) ?>" required>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="a">Значение A</label>
                        <input class="form__control" type="text" name="A" id="a" value="<?= h($aRaw) ?>" required>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="b">Значение B</label>
                        <input class="form__control" type="text" name="B" id="b" value="<?= h($bRaw) ?>" required>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="c">Значение C</label>
                        <input class="form__control" type="text" name="C" id="c" value="<?= h($cRaw) ?>" required>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="result">Ваш ответ</label>
                        <input class="form__control" type="text" name="RESULT" id="result" value="">
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="about">Немного о себе</label>
                        <textarea class="form__control form__control--textarea" name="ABOUT" id="about" rows="5"></textarea>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="task">Тип задачи</label>
                        <select class="form__control" name="TASK" id="task">
                            <?php foreach ($tasks as $key => $label): ?>
                                <option value="<?= h($key) ?>" <?= $task === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form__row form__row--checkbox">
                        <span class="form__label">Дополнительно</span>
                        <label class="checkbox">
                            <input type="checkbox" name="send_mail" id="send_mail" value="1" onclick="toggleMailField()">
                            <span>Отправить результат теста по e-mail</span>
                        </label>
                    </div>

                    <div class="form__row is-hidden" id="mail-row">
                        <label class="form__label" for="mail">Ваш e-mail</label>
                        <input class="form__control" type="email" name="MAIL" id="mail" value="">
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="view_mode">Режим вывода</label>
                        <select class="form__control" name="VIEW_MODE" id="view_mode">
                            <?php foreach ($viewModes as $key => $label): ?>
                                <option value="<?= h($key) ?>" <?= $viewMode === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form__actions">
                        <button class="button" type="submit">Проверить</button>
                    </div>
                </form>
            </section>
        <?php else: ?>
            <section class="card report <?= $viewMode === 'print' ? 'report--print' : 'report--browser' ?>">
                <h1>Результат проверки</h1>

                <div class="report__item"><strong>ФИО и группа студента:</strong> <?= h($fio) ?>, <?= h($group) ?></div>
                <div class="report__item"><strong>Сведения о студенте:</strong> <?= $about !== '' ? nl2br(h($about)) : 'Не указано' ?></div>
                <div class="report__item"><strong>Тип задачи:</strong> <?= h($taskLabel) ?></div>
                <div class="report__item"><strong>Входные данные:</strong> A = <?= h($aRaw) ?>, B = <?= h($bRaw) ?>, C = <?= h($cRaw) ?></div>
                <div class="report__item"><strong>Предполагаемый результат:</strong> <?= h($userAnswerDisplay) ?></div>
                <div class="report__item"><strong>Вычисленный программой результат:</strong>
                    <?= $programError !== '' ? h($programError) : h(formatNumber($programResult)) ?>
                </div>
                <div class="report__item"><strong>Вывод:</strong>
                    <span class="<?= $conclusion === 'Тест пройден' ? 'status status--ok' : 'status status--error' ?>">
                        <?= h($conclusion) ?>
                    </span>
                </div>

                <?php if ($emailMessage !== ''): ?>
                    <div class="notice"><?= h($emailMessage) ?></div>
                    <div class="notice notice--muted"><?= h($emailNote) ?></div>
                <?php endif; ?>

                <?php if ($viewMode === 'browser'): ?>
                    <div class="report__actions">
                        <a class="button button--link" href="<?= h($repeatLink) ?>">Повторить тест</a>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<footer class="footer">
    <div class="container footer__inner">
        <div>Режим: <?= $isSubmitted ? h($viewModes[$viewMode]) : 'Форма ввода' ?></div>
        <div>Дата и время: <?= date('d.m.Y H:i:s') ?></div>
    </div>
</footer>

<script>
function toggleMailField() {
    var checkbox = document.getElementById('send_mail');
    var mailRow = document.getElementById('mail-row');
    if (!checkbox || !mailRow) {
        return;
    }
    mailRow.classList.toggle('is-hidden', !checkbox.checked);
}

document.addEventListener('DOMContentLoaded', toggleMailField);
</script>
</body>
</html>
