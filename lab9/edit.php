<?php
if (!defined('APP_STARTED')) {
    exit('Прямой доступ запрещён.');
}

$noticeText = '';
$noticeClass = '';
$formData = null;

try {
    $db = connectDb();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_name'] ?? '') === 'edit_contact') {
        $currentId = normalizeId($_POST['id'] ?? 0);
        $formData = [
            'surname' => trim((string)($_POST['surname'] ?? '')),
            'name' => trim((string)($_POST['name'] ?? '')),
            'patronymic' => trim((string)($_POST['patronymic'] ?? '')),
            'gender' => normalizeGender((string)($_POST['gender'] ?? 'Мужской')),
            'birth_date' => trim((string)($_POST['birth_date'] ?? '')),
            'phone' => trim((string)($_POST['phone'] ?? '')),
            'address' => trim((string)($_POST['address'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'comment' => trim((string)($_POST['comment'] ?? '')),
        ];

        $errors = [];

        if ($currentId <= 0) {
            $errors[] = 'Не выбрана запись для редактирования.';
        }

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
            $stmt = $db->prepare(
                'UPDATE friends SET '
                . 'surname = ?, '
                . 'name = ?, '
                . 'patronymic = ?, '
                . 'gender = ?, '
                . 'birth_date = NULLIF(?, \'\'), '
                . 'phone = ?, '
                . 'address = ?, '
                . 'email = ?, '
                . 'comment = ? '
                . 'WHERE id = ? LIMIT 1'
            );

            if (!$stmt) {
                throw new Exception('Не удалось подготовить SQL-запрос на изменение записи.');
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
                'sssssssssi',
                $surname,
                $name,
                $patronymic,
                $gender,
                $birthDate,
                $phone,
                $address,
                $email,
                $comment,
                $currentId
            );

            if (!$stmt->execute()) {
                throw new Exception('Не удалось изменить запись в базе данных.');
            }

            $noticeClass = 'notice--success';
            $noticeText = 'Запись успешно изменена.';
            $formData = null;
            $_GET['id'] = $currentId;
        }
    }

    $listResult = $db->query('SELECT id, surname, name, patronymic FROM friends ORDER BY surname ASC, name ASC, patronymic ASC, id ASC');

    if (!$listResult) {
        throw new Exception('Не удалось получить список записей для редактирования.');
    }

    $contacts = [];
    while ($row = $listResult->fetch_assoc()) {
        $contacts[] = $row;
    }

    if (!$contacts) {
        ?>
        <section class="card">
          <h2>Редактирование записи</h2>
          <?php if ($noticeText !== ''): ?>
            <div class="notice <?= h($noticeClass) ?>"><?= h($noticeText) ?></div>
          <?php endif; ?>
          <div class="empty-text">В базе данных нет записей для редактирования.</div>
        </section>
        <?php
        return;
    }

    $currentId = normalizeId($_GET['id'] ?? ($_POST['id'] ?? 0));
    if ($currentId <= 0) {
        $currentId = (int)$contacts[0]['id'];
    }

    $contactIds = array_map(static function ($item) {
        return (int)$item['id'];
    }, $contacts);

    if (!in_array($currentId, $contactIds, true)) {
        $currentId = (int)$contacts[0]['id'];
    }

    $stmt = $db->prepare('SELECT id, surname, name, patronymic, gender, birth_date, phone, address, email, comment FROM friends WHERE id = ? LIMIT 1');

    if (!$stmt) {
        throw new Exception('Не удалось подготовить SQL-запрос на получение текущей записи.');
    }

    $stmt->bind_param('i', $currentId);
    $stmt->execute();
    $currentResult = $stmt->get_result();
    $currentRow = $currentResult ? $currentResult->fetch_assoc() : null;

    if (!$currentRow) {
        throw new Exception('Не удалось получить выбранную запись.');
    }

    if (is_array($formData)) {
        $currentRow = array_merge($currentRow, $formData, ['id' => $currentId]);
    }
    ?>

    <section class="card">
      <h2>Выбор записи для редактирования</h2>
      <div class="note">Текущая запись выделена красной рамкой.</div>

      <div class="inline-list">
        <?php foreach ($contacts as $contact): ?>
          <?php if ((int)$contact['id'] === $currentId): ?>
            <span class="inline-list__current"><?= h(shortName($contact)) ?></span>
          <?php else: ?>
            <a class="inline-list__link" href="index.php?p=edit&amp;id=<?= (int)$contact['id'] ?>"><?= h(shortName($contact)) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card">
      <h2>Форма редактирования</h2>

      <?php if ($noticeText !== ''): ?>
        <div class="notice <?= h($noticeClass) ?>"><?= h($noticeText) ?></div>
      <?php endif; ?>

      <form class="form" action="index.php?p=edit&amp;id=<?= (int)$currentId ?>" method="post">
        <input type="hidden" name="form_name" value="edit_contact">
        <input type="hidden" name="id" value="<?= (int)$currentId ?>">

        <div class="form-grid">
          <div class="form__field">
            <label class="form__label" for="edit_surname">Фамилия</label>
            <input class="form__input" type="text" name="surname" id="edit_surname" value="<?= h($currentRow['surname']) ?>" required>
          </div>

          <div class="form__field">
            <label class="form__label" for="edit_name">Имя</label>
            <input class="form__input" type="text" name="name" id="edit_name" value="<?= h($currentRow['name']) ?>" required>
          </div>

          <div class="form__field">
            <label class="form__label" for="edit_patronymic">Отчество</label>
            <input class="form__input" type="text" name="patronymic" id="edit_patronymic" value="<?= h($currentRow['patronymic']) ?>">
          </div>

          <div class="form__field">
            <label class="form__label" for="edit_gender">Пол</label>
            <select class="form__select" name="gender" id="edit_gender">
              <option value="Мужской" <?= $currentRow['gender'] === 'Мужской' ? 'selected' : '' ?>>Мужской</option>
              <option value="Женский" <?= $currentRow['gender'] === 'Женский' ? 'selected' : '' ?>>Женский</option>
            </select>
          </div>

          <div class="form__field">
            <label class="form__label" for="edit_birth_date">Дата рождения</label>
            <input class="form__input" type="date" name="birth_date" id="edit_birth_date" value="<?= h($currentRow['birth_date']) ?>">
          </div>

          <div class="form__field">
            <label class="form__label" for="edit_phone">Телефон</label>
            <input class="form__input" type="text" name="phone" id="edit_phone" value="<?= h($currentRow['phone']) ?>">
          </div>

          <div class="form__field form__field--full">
            <label class="form__label" for="edit_address">Адрес</label>
            <input class="form__input" type="text" name="address" id="edit_address" value="<?= h($currentRow['address']) ?>">
          </div>

          <div class="form__field">
            <label class="form__label" for="edit_email">E-mail</label>
            <input class="form__input" type="email" name="email" id="edit_email" value="<?= h($currentRow['email']) ?>">
          </div>

          <div class="form__field form__field--full">
            <label class="form__label" for="edit_comment">Комментарий</label>
            <textarea class="form__textarea form__textarea--small" name="comment" id="edit_comment"><?= h($currentRow['comment']) ?></textarea>
          </div>
        </div>

        <div class="form__actions">
          <button class="button" type="submit">Изменить запись</button>
        </div>
      </form>
    </section>

    <?php
} catch (Throwable $e) {
    ?>
    <section class="card">
      <h2>Редактирование записи</h2>
      <div class="notice notice--error"><?= h($e->getMessage()) ?></div>
    </section>
    <?php
}
