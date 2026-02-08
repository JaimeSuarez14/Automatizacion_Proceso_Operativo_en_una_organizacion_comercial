<?php require 'header.php'; ?>

<section>
    <div class="card mx-auto bg-body-tertiary p-3" style="width: 30rem;">
        <h1 class="text-center py-2"><i class="fa-regular fa-address-card me-2"></i>Registrar Usuario</h1>
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
        <p style="text-align:center;margin-top:15px;">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesion aquí</a>
        </p>
    </div>
</section>

<?php require 'footer.php'; ?>