<!DOCTYPE html>
<html lang="ru">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <title>Форма подачи заявки</title>
   <script>
       $(document).ready(function() {
           function validateForm() {
               let isValid = true;
               $("#request-form input, #request-form textarea").each(function() {
                   if ($(this).val() === "") {
                       isValid = false;
                   }
               });
               if (!$("#email")[0].checkValidity() || !$("#pin")[0].checkValidity()) {
                   isValid = false;
               }
               $("#submit-button").prop("disabled", !isValid);
           }
           
           $("#request-form input, #request-form textarea").on("input", validateForm);
           validateForm();
       });
   </script>
</head>
<body>
<div class="container">
   <h2 class="mt-5">Форма подачи заявки</h2>
   <form id="request-form" method="POST" action="index.php">
      <div class="form-group">
         <label for="subject">Тема:</label>
         <input type="text" class="form-control" id="subject" name="subject" required maxlength="255">
      </div>
      <div class="form-group">
         <label for="text">Текст:</label>
         <textarea class="form-control" id="text" name="text" required maxlength="4096"></textarea>
      </div>
      <div class="form-group">
         <label for="priority">Приоритет:</label>
         <select class="form-control" id="priority" name="priority">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3" selected>3</option>
            <option value="4">4</option>
            <option value="5">5</option>
         </select>
      </div>
      <div class="form-group">
         <label for="email">Электронная почта:</label>
         <input type="email" class="form-control" id="email" name="email" required maxlength="255">
      </div>
      <div class="form-group">
         <label for="pin">PIN:</label>
         <input type="text" class="form-control" id="pin" name="pin" required pattern="\d{4}">
      </div>
      <button type="submit" class="btn btn-primary" id="submit-button" disabled>Отправить</button>
   </form>
   <div class="mt-5">
      <h4>Ваши предыдущие заявки:</h4>
      <ul>
      <?php
      $db = new SQLite3('requests.db');
      foreach ($_COOKIE as $key => $value) {
          if (preg_match('/^request_(\d+)$/', $key, $matches)) {
              $requestId = $matches[1];
              $query = $db->query("SELECT * FROM requests WHERE id = $requestId");
              $request = $query->fetchArray(SQLITE3_ASSOC);
              if ($request && md5($request['pin']) === $value) {
                  echo "<li><a href='?id=$requestId'>" . htmlspecialchars($request['subject']) . "</a></li>";
              }
          }
      }
      ?>
      </ul>
   </div>
</div>
</body>
</html>