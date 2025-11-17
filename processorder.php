<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>The Starbuzz Bean Machine</title>
</head>
<body>

<h1>The Starbuzz Bean Machine</h1>

<?php
  // --- Procesar todos los datos enviados ---
  $formData = [];

  foreach ($_POST as $key => $value) {
    if (is_array($value)) {
      // Si el campo es un array (por ejemplo extras[]), convertimos cada valor a texto seguro
      $safeValue = implode(", ", array_map('htmlspecialchars', $value));
    } else {
      // Si es un solo valor, lo convertimos directamente
      $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    $formData[$key] = $safeValue;
  }

  // --- Variables útiles ---
  $name = $formData["name"] ?? '';

  // --- Mensaje principal ---
  echo "<p>Thanks, <strong>$name</strong>, for your order... ";
  echo "But we didn't get your choice of beans or whether they are whole or ground. ";
  echo "You might want to click the back button to go back and try again, otherwise we won't be able to make your Bean Machine order, and that would suck.</p>";

  // --- Procesar archivo CSS o imagen subida ---
  $archivoInfo = null;
  if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $fileName = $_FILES['archivo']['name'];
    $fileSize = $_FILES['archivo']['size'];
    $fileTmpName = $_FILES['archivo']['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $maxFileSize = 5 * 1024 * 1024;
    $allowedExtensions = ['css', 'gif', 'jpg', 'jpeg', 'png'];
    
    if ($fileSize > $maxFileSize) {
      $archivoInfo = "Error: El archivo es demasiado grande (máximo 5MB)";
    } elseif (!in_array($fileExtension, $allowedExtensions)) {
      $archivoInfo = "Error: Solo se permiten archivos CSS o imágenes (css, gif, jpg, jpeg, png)";
    } else {
      if ($fileExtension === 'css') {
        $uploadPath = 'style.css';
        $mensaje = 'El archivo CSS ha sido cargado exitosamente y se aplicará a la página';
      } else {
        if (!is_dir('images')) {
          mkdir('images', 0755, true);
        }
        $uploadPath = 'images/' . basename($fileName);
        $mensaje = 'La imagen ha sido cargada exitosamente en la carpeta images/';
      }
      
      if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $archivoInfo = [
          'nombre' => htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'),
          'tamaño' => round($fileSize / 1024, 2) . ' KB',
          'ruta' => htmlspecialchars($uploadPath, ENT_QUOTES, 'UTF-8'),
          'mensaje' => $mensaje
        ];
      } else {
        $archivoInfo = "Error: No se pudo subir el archivo";
      }
    }
  }

  // --- Mostrar los datos recibidos ---
  echo "<p>Here's what we received from you so far:</p>";
  echo "<p>";

  foreach ($formData as $fieldName => $fieldValue) {
    $displayName = ucfirst($fieldName);
    echo "$displayName: $fieldValue<br>";
  }

  if ($archivoInfo) {
    echo "<br><strong>Archivo subido:</strong><br>";
    if (is_array($archivoInfo)) {
      echo "Nombre: " . $archivoInfo['nombre'] . "<br>";
      echo "Tamaño: " . $archivoInfo['tamaño'] . "<br>";
      echo "Guardado en: " . $archivoInfo['ruta'] . "<br>";
      echo $archivoInfo['mensaje'] . "<br>";
      echo "<a href='index.html'>Volver al formulario</a><br>";
    } else {
      echo $archivoInfo . "<br>";
    }
  }

  echo "</p>";
?>

</body>
</html>
