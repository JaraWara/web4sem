<?php
if (!defined('APP_STARTED')) {
    exit('Прямой доступ запрещён.');
}

function renderMenu(string $activePage, string $activeSort): string
{
    $activeSort = normalizeSort($activeSort);

    ob_start();
    ?>
    <section class="card">
      <h2>Меню сайта</h2>

      <nav class="menu">
        <a class="menu__item <?= $activePage === 'view' ? 'is-active' : '' ?>" href="index.php?p=view&amp;sort=<?= h($activeSort) ?>">Просмотр</a>
        <a class="menu__item <?= $activePage === 'add' ? 'is-active' : '' ?>" href="index.php?p=add">Добавление записи</a>
        <a class="menu__item <?= $activePage === 'edit' ? 'is-active' : '' ?>" href="index.php?p=edit">Редактирование записи</a>
        <a class="menu__item <?= $activePage === 'delete' ? 'is-active' : '' ?>" href="index.php?p=delete">Удаление записи</a>
      </nav>

      <?php if ($activePage === 'view'): ?>
        <div class="submenu-title">Вид сортировки</div>
        <div class="submenu">
          <a class="submenu__item <?= $activeSort === 'byid' ? 'is-active' : '' ?>" href="index.php?p=view&amp;sort=byid">По добавлению</a>
          <a class="submenu__item <?= $activeSort === 'bysurname' ? 'is-active' : '' ?>" href="index.php?p=view&amp;sort=bysurname">По фамилии</a>
          <a class="submenu__item <?= $activeSort === 'bybirth' ? 'is-active' : '' ?>" href="index.php?p=view&amp;sort=bybirth">По дате рождения</a>
        </div>
      <?php endif; ?>
    </section>
    <?php

    return ob_get_clean();
}
