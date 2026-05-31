<?php
if (!defined('APP_STARTED')) {
    exit('Прямой доступ запрещён.');
}

$noticeText = '';
$noticeClass = '';

try {
    $db = connectDb();
    $deleteId = normalizeId($_GET['id'] ?? 0);

    if ($deleteId > 0) {
        $selectStmt = $db->prepare('SELECT id, surname, name, patronymic FROM friends WHERE id = ? LIMIT 1');

        if (!$selectStmt) {
            throw new Exception('Не удалось подготовить SQL-запрос на выбор записи для удаления.');
        }

        $selectStmt->bind_param('i', $deleteId);
        $selectStmt->execute();
        $selectResult = $selectStmt->get_result();
        $deleteRow = $selectResult ? $selectResult->fetch_assoc() : null;

        if ($deleteRow) {
            $deleteTitle = shortName($deleteRow);
            $deleteStmt = $db->prepare('DELETE FROM friends WHERE id = ? LIMIT 1');

            if (!$deleteStmt) {
                throw new Exception('Не удалось подготовить SQL-запрос на удаление записи.');
            }

            $deleteStmt->bind_param('i', $deleteId);

            if (!$deleteStmt->execute()) {
                throw new Exception('Не удалось удалить запись из базы данных.');
            }

            $noticeClass = 'notice--success';
            $noticeText = 'Запись «' . $deleteTitle . '» удалена.';
        } else {
            $noticeClass = 'notice--error';
            $noticeText = 'Запись для удаления не найдена.';
        }
    }

    $listResult = $db->query('SELECT id, surname, name, patronymic FROM friends ORDER BY surname ASC, name ASC, patronymic ASC, id ASC');

    if (!$listResult) {
        throw new Exception('Не удалось получить список записей для удаления.');
    }
    ?>

    <section class="card">
      <h2>Удаление записи</h2>

      <?php if ($noticeText !== ''): ?>
        <div class="notice <?= h($noticeClass) ?>"><?= h($noticeText) ?></div>
      <?php endif; ?>

      <?php if ($listResult->num_rows === 0): ?>
        <div class="empty-text">В базе данных нет записей для удаления.</div>
      <?php else: ?>
        <div class="note">Нажми на нужную запись, чтобы удалить её из базы данных.</div>
        <div class="inline-list">
          <?php while ($row = $listResult->fetch_assoc()): ?>
            <a class="inline-list__link inline-list__link--danger" href="index.php?p=delete&amp;id=<?= (int)$row['id'] ?>"><?= h(shortName($row)) ?></a>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </section>

    <?php
} catch (Throwable $e) {
    ?>
    <section class="card">
      <h2>Удаление записи</h2>
      <div class="notice notice--error"><?= h($e->getMessage()) ?></div>
    </section>
    <?php
}
