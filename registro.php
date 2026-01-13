<?php require 'header.php'; ?>

<section>
    <h1>Registro de Usuario</h1>

    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color:red'>" . htmlspecialchars($_GET['error']) . "</p>";
    }
    if (isset($_GET['msg'])) {
        echo "<p style='color:green'>" . htmlspecialchars($_GET['msg']) . "</p>";
    }
    ?>

    <form action="proceso_registro.php" method="post">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="contrasena" required><br><br>

        <button type="submit" class="button">Registrarme</button>
    </form>
</section>

<?php require 'footer.php'; ?>