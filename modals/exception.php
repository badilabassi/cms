<!DOCTYPE html>
<html lang="en">
<head>
  
<title>System Error</title>
<meta charset="utf-8" />
<meta name="robots" content="noindex, nofollow" />

<style>

* {
  padding: 0;
  margin: 0;
}

body {
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 14px;
  line-height: 20px;
  color: #222;
  margin: 50px;
}

a {
  color: #777;
  text-decoration: none;
}

h1 {
  margin-bottom: 20px;
}

h2 {
  border-bottom: 1px solid #ddd;
  padding-bottom: 20px;
  text-transform: uppercase;
  font-weight: normal;
}

.message {
  background: red;
  color: #fff;
  padding: 10px;
  margin-bottom: 40px;
  color: #fff;
  border-radius: 3px;
}

.trace {
  list-style: none;
  border-bottom: 1px solid #ddd;
  padding: 20px 0
}

.trace ul {
  margin-left: 20px;
}

.trace th {
  width: 120px;
}

.trace table {
  width: 100%;
  table-layout: fixed;
}


</style>

</head>
<body>

  <h1>System Error</h1>
  <p class="message"><strong><?php echo htmlspecialchars($exception->getMessage()) ?></strong></p>

  <?php if(c::get('debug')): ?>

  <h2>Trace</h2>

  <ul>
  <?php foreach($exception->getTrace() as $t): ?>
  <li class="trace">
    <table>
      <tr>
        <th>File:</th>
        <td><?php echo htmlspecialchars($t['file']) ?></td>
      </tr>
      <tr>
        <th>Line:</th>
        <td><?php echo htmlspecialchars($t['line']) ?></td>
      </tr>
      <tr>
        <th>Function:</th>
        <td><?php echo htmlspecialchars($t['function']) ?></td>
      </tr>
      <tr>
        <th>Arguments:</th>
        <td>
          <ul>
            <?php foreach($t['args'] as $arg): ?>
            <li><?php echo htmlspecialchars($arg) ?></li>
            <?php endforeach ?>
          </ul>
        </td>
      </tr>
    </table>
  </li>
  <?php endforeach ?>
  </ul>

  <?php endif ?>

</body>
</html>