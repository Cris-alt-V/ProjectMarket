<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estado de la conexión</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; }
        .status { padding: 1rem; border-radius: 6px; display: inline-block; }
        .success { background: #e6ffed; color: #0b6b2f; border: 1px solid #b7f0c6; }
        .failed { background: #ffe6e6; color: #8b0000; border: 1px solid #f0b7b7; }
    </style>
</head>
<body>
    <h1>Comprobación de base de datos</h1>

    <?php if($status === 'success'): ?>
        <div class="status success">Conexión exitosa</div>
    <?php else: ?>
        <div class="status failed">Conexión fallida</div>
    <?php endif; ?>

    <p><?php echo e($message); ?></p>

</body>
</html>
<?php /**PATH C:\Users\castr\OneDrive\Desktop\laravel\xddd\ProjectMarket\market\resources\views/dbstatus.blade.php ENDPATH**/ ?>