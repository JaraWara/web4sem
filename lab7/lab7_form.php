<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Сортировка массива — ЛР №7</title>
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
        <div class="header__line">ЛР №7 — сортировка массива</div>
      </div>
    </div>
  </div>
</header>

<main class="main">
  <div class="container">
    <section class="card">
      <h1>Ввод массива для сортировки</h1>

      <form class="form" method="post" action="lab7_process.php" target="_blank">
        <div class="form__section">
          <label class="label" for="algorithm">Алгоритм сортировки</label>
          <select class="control" name="algorithm" id="algorithm">
            <option value="selection">Сортировка выбором</option>
            <option value="bubble">Пузырьковый алгоритм</option>
            <option value="shell">Алгоритм Шелла</option>
            <option value="gnome">Алгоритм садового гнома</option>
            <option value="quick">Быстрая сортировка</option>
            <option value="builtin">Встроенная функция PHP sort()</option>
          </select>
        </div>

        <div class="form__section">
          <div class="form__section-head">
            <div>
              <div class="label label--static">Элементы массива</div>
              <div class="hint">Можно вводить целые и дробные числа. Допустимы точка и запятая.</div>
            </div>
            <div class="counter">Количество полей: <span id="arr-length-view">1</span></div>
          </div>

          <table class="input-table" id="elements-table">
            <thead>
              <tr>
                <th>№</th>
                <th>Значение элемента массива</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="input-table__index">0</td>
                <td><input class="control" type="text" name="element[]" placeholder="Введите число"></td>
              </tr>
            </tbody>
          </table>
          <input type="hidden" name="arrLength" id="arrLength" value="1">
        </div>

        <div class="actions">
          <button class="button button--secondary" type="button" onclick="addElementRow()">Добавить еще один элемент</button>
          <button class="button" type="submit">Сортировать массив</button>
        </div>
      </form>
    </section>
  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>Страница ввода данных</div>
    <div id="footer-time">Дата и время загрузки формы</div>
  </div>
</footer>

<script>
function updateLength() {
  var tbody = document.querySelector('#elements-table tbody');
  var rows = tbody.querySelectorAll('tr');
  document.getElementById('arrLength').value = rows.length;
  document.getElementById('arr-length-view').textContent = rows.length;

  rows.forEach(function(row, index) {
    var indexCell = row.querySelector('.input-table__index');
    if (indexCell) {
      indexCell.textContent = index;
    }
  });
}

function addElementRow() {
  var tbody = document.querySelector('#elements-table tbody');
  var row = document.createElement('tr');
  row.innerHTML = '<td class="input-table__index"></td>' +
                  '<td><input class="control" type="text" name="element[]" placeholder="Введите число"></td>';
  tbody.appendChild(row);
  updateLength();

  var input = row.querySelector('input');
  if (input) {
    input.focus();
  }
}

(function setFooterTime() {
  var now = new Date();
  var day = String(now.getDate()).padStart(2, '0');
  var month = String(now.getMonth() + 1).padStart(2, '0');
  var year = now.getFullYear();
  var hours = String(now.getHours()).padStart(2, '0');
  var minutes = String(now.getMinutes()).padStart(2, '0');
  var seconds = String(now.getSeconds()).padStart(2, '0');
  document.getElementById('footer-time').textContent = 'Дата и время: ' + day + '.' + month + '.' + year + ' ' + hours + ':' + minutes + ':' + seconds;
})();

updateLength();
</script>
</body>
</html>
