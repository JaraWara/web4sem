<?php
if (!defined('APP_STARTED')) {
    exit('Прямой доступ запрещён.');
}

function getSortSql(string $sort): string
{
    switch ($sort) {
        case 'bysurname':
            return 'ORDER BY surname ASC, name ASC, patronymic ASC, id ASC';

        case 'bybirth':
            return 'ORDER BY birth_date IS NULL, birth_date ASC, surname ASC, name ASC, patronymic ASC, id ASC';

        case 'byid':
        default:
            return 'ORDER BY created_at DESC, id DESC';
    }
}

function getSortLabel(string $sort): string
{
    switch ($sort) {
        case 'bysurname':
            return 'по фамилии';

        case 'bybirth':
            return 'по дате рождения';

        case 'byid':
        default:
            return 'по дате добавления';
    }
}

try {
    $db = connectDb();
    $sort = normalizeSort($_GET['sort'] ?? 'byid');
    $page = normalizeId($_GET['page'] ?? 1);

    if ($page < 1) {
        $page = 1;
    }

    $perPage = 10;
    $countResult = $db->query('SELECT COUNT(*) AS cnt FROM friends');

    if (!$countResult) {
        throw new Exception('Не удалось получить количество записей из таблицы friends.');
    }

    $countRow = $countResult->fetch_assoc();
    $totalRows = (int)($countRow['cnt'] ?? 0);
    $totalPages = max(1, (int)ceil($totalRows / $perPage));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    $sql = 'SELECT id, surname, name, patronymic, gender, birth_date, phone, address, email, comment '
        . 'FROM friends '
        . getSortSql($sort)
        . ' LIMIT ' . $offset . ', ' . $perPage;

    $result = $db->query($sql);

    if (!$result) {
        throw new Exception('Не удалось выполнить запрос на выборку записей.');
    }
    ?>

    <section class="card">
      <h2>Список контактов</h2>
      <div class="muted">Текущая сортировка: <?= h(getSortLabel($sort)) ?>. Всего записей: <?= $totalRows ?>.</div>

      <?php if ($totalRows === 0): ?>
        <div class="empty-text">В базе данных пока нет записей. Добавь первую запись через пункт «Добавление записи».</div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="result-table result-table--wide">
            <tr>
              <th>ID</th>
              <th>Фамилия</th>
              <th>Имя</th>
              <th>Отчество</th>
              <th>Пол</th>
              <th>Дата рождения</th>
              <th>Возраст</th>
              <th>Телефон</th>
              <th>Адрес</th>
              <th>E-mail</th>
              <th>Комментарий</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= h($row['surname']) ?></td>
                <td><?= h($row['name']) ?></td>
                <td><?= h($row['patronymic']) ?></td>
                <td><?= h($row['gender']) ?></td>
                <td><?= h(formatDate($row['birth_date'])) ?></td>
                <td><?= h(calculateAge($row['birth_date'])) ?></td>
                <td><?= h($row['phone']) ?></td>
                <td><?= h($row['address']) ?></td>
                <td><?= h($row['email']) ?></td>
                <td><?= nl2br(h($row['comment'])) ?></td>
              </tr>
            <?php endwhile; ?>
          </table>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <span class="pagination__label">Страницы:</span>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <?php if ($i === $page): ?>
                <span class="pagination__current"><?= $i ?></span>
              <?php else: ?>
                <a class="pagination__link" href="index.php?p=view&amp;sort=<?= h($sort) ?>&amp;page=<?= $i ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </section>

    <?php
} catch (Throwable $e) {
    ?>
    <section class="card">
      <h2>Список контактов</h2>
      <div class="notice notice--error"><?= h($e->getMessage()) ?></div>
    </section>
    <?php
}
