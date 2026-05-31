<?php
if (!defined('APP_STARTED')) {
    exit('Прямой доступ запрещён.');
}

$noticeText = '';
$noticeClass = '';
$formData = [
    'surname' => '',
    'name' => '',
    'patronymic' => '',
    'gender' => 'Мужской',
    'birth_date' => '',
    'phone' => '',
    'address' => '',
    'email' => '',
    'comment' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_name'] ?? '') === 'add_contact') {
    foreach ($formData as $key => $value) {
        if (isset($_POST[$key])) {
            $formData[$key] = trim((string)$_POST[$key]);
        }
    }

    $formData['gender'] = normalizeGender($formData['gender']);
    $errors = [];

    if ($formData['surname'] === '') {
        $errors[] = 'Введите фамилию.';
    }

    if ($formData['name'] === '') {
        $errors[] = 'Введите имя.';
    }

    if ($formData['birth_date'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $formData['birth_date'])) {
        $errors[] = 'Дата рождения должна быть в формате ГГГГ-ММ-ДД.';
    }

    if ($formData['email'] !== '' && !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный e-mail.';
    }

    if ($errors) {
        $noticeClass = 'notice--error';
        $noticeText = implode(' ', $errors);
    } else {
        try {
            $db = connectDb();
            $stmt = $db->prepare(
                'INSERT INTO friends '
                . '(surname, name, patronymic, gender, birth_date, phone, address, email, comment) '
                . 'VALUES (?, ?, ?, ?, NULLIF(?, \'\'), ?, ?, ?, ?)'
            );

            if (!$stmt) {
                throw new Exception('Не удалось подготовить SQL-запрос на добавление записи.');
            }

            $surname = $formData['surname'];
            $name = $formData['name'];
            $patronymic = $formData['patronymic'];
            $gender = $formData['gender'];
            $birthDate = $formData['birth_date'];
            $phone = $formData['phone'];
            $address = $formData['address'];
            $email = $formData['email'];
            $comment = $formData['comment'];

            $stmt->bind_param(
                'sssssssss',
                $surname,
                $name,
                $patronymic,
                $gender,
                $birthDate,
                $phone,
                $address,
                $email,
                $comment
            );

            if (!$stmt->execute()) {
                throw new Exception('Не удалось добавить запись в базу данных.');
            }

            $noticeClass = 'notice--success';
            $noticeText = 'Запись успешно добавлена.';

            $formData = [
                'surname' => '',
                'name' => '',
                'patronymic' => '',
                'gender' => 'Мужской',
                'birth_date' => '',
                'phone' => '',
                'address' => '',
                'email' => '',
                'comment' => '',
            ];
        } catch (Throwable $e) {
            $noticeClass = 'notice--error';
            $noticeText = $e->getMessage();
        }
    }
}
?>
<section class="card">
  <h2>Добавление новой записи</h2>

  <?php if ($noticeText !== ''): ?>
    <div class="notice <?= h($noticeClass) ?>"><?= h($noticeText) ?></div>
  <?php endif; ?>

  <form class="form" action="index.php?p=add" method="post">
    <input type="hidden" name="form_name" value="add_contact">

    <div class="form-grid">
      <div class="form__field">
        <label class="form__label" for="surname">Фамилия</label>
        <input class="form__input" type="text" name="surname" id="surname" value="<?= h($formData['surname']) ?>" required>
      </div>

      <div class="form__field">
        <label class="form__label" for="name">Имя</label>
        <input class="form__input" type="text" name="name" id="name" value="<?= h($formData['name']) ?>" required>
      </div>

      <div class="form__field">
        <label class="form__label" for="patronymic">Отчество</label>
        <input class="form__input" type="text" name="patronymic" id="patronymic" value="<?= h($formData['patronymic']) ?>">
      </div>

      <div class="form__field">
        <label class="form__label" for="gender">Пол</label>
        <select class="form__select" name="gender" id="gender">
          <option value="Мужской" <?= $formData['gender'] === 'Мужской' ? 'selected' : '' ?>>Мужской</option>
          <option value="Женский" <?= $formData['gender'] === 'Женский' ? 'selected' : '' ?>>Женский</option>
        </select>
      </div>

      <div class="form__field">
        <label class="form__label" for="birth_date">Дата рождения</label>
        <input class="form__input" type="date" name="birth_date" id="birth_date" value="<?= h($formData['birth_date']) ?>">
      </div>

      <div class="form__field">
        <label class="form__label" for="phone">Телефон</label>
        <input class="form__input" type="text" name="phone" id="phone" value="<?= h($formData['phone']) ?>">
      </div>

      <div class="form__field form__field--full">
        <label class="form__label" for="address">Адрес</label>
        <input class="form__input" type="text" name="address" id="address" value="<?= h($formData['address']) ?>">
      </div>

      <div class="form__field">
        <label class="form__label" for="email">E-mail</label>
        <input class="form__input" type="email" name="email" id="email" value="<?= h($formData['email']) ?>">
      </div>

      <div class="form__field form__field--full">
        <label class="form__label" for="comment">Комментарий</label>
        <textarea class="form__textarea form__textarea--small" name="comment" id="comment"><?= h($formData['comment']) ?></textarea>
      </div>
    </div>

    <div class="form__actions">
      <button class="button" type="submit">Добавить запись</button>
    </div>
  </form>
</section>
