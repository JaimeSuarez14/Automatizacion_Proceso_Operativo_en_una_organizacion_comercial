</main>

<footer class="site-footer" style="background:#003c5c; color:white; padding:40px 0; margin-top:40px;">
  <div class="container" style="max-width:1200px; margin:auto; display:flex; justify-content:space-between; flex-wrap:wrap;">

    <!-- Logo y lema -->
    <div style="flex:1; min-width:250px; text-align:center; margin-bottom:20px;">
      <img src="<?= BASE_URL ?>img/logo.png" alt="La Choza Náutica" style="width:120px; margin-bottom:10px;">
      <h2 style="color:#ffc400; margin:10px 0;">La Choza Náutica</h2>
      <p>Sabores del mar directo a tu mesa 🌊</p>
      <div>
        <a href="login.admin.php" style="color:white; text-decoration:none;">Administrar</a>
      </div>
    </div>

    <!-- Redes Sociales -->
    <div style="flex:1; min-width:200px; text-align:center; margin-bottom:20px;">
      <h3 style="color:#ffc400; margin-bottom:15px;">Síguenos</h3>

      <ul style="list-style:none; padding:0; line-height:2;">
        <li>
          <a href="https://www.facebook.com/lachozanauticaica/?locale=es_LA" target="_blank" style="color:white; text-decoration:none;">
            Facebook
          </a>
        </li>
        <li>
          <a href="https://www.instagram.com/lachozanauticaica/?hl=es" target="_blank" style="color:white; text-decoration:none;">
            Instagram
          </a>
        </li>
        <li>
          <a href="https://www.tiktok.com/@la.choza.nautica?is_from_webapp=1&sender_device=pc" target="_blank" style="color:white; text-decoration:none;">
            TikTok
          </a>
        </li>
      </ul>
    </div>


    <!-- Contacto -->
    <div style="flex:1; min-width:250px; text-align:center;">
      <h3 style="color:#ffc400; margin-bottom:15px;">Contáctanos</h3>
      <p>📍 Av. del Mar 1450, Lima - Perú</p>
      <p>📞 (01) 567-8900</p>
      <p>📧 reservas@lachozanautica.pe</p>
    </div>

  </div>

  <div style="text-align:center; padding-top:20px; margin-top:20px; border-top:1px solid rgba(255,255,255,0.2);">
    © <?php echo date('Y'); ?> La Choza Náutica | Todos los derechos reservados
  </div>
</footer>

<script>
  // Menú responsive
  document.querySelectorAll('.menu-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelector('.main-nav').classList.toggle('open');
    });
  });
</script>
<script  src="<?= BASE_URL ?>assets/dist/js/bootstrap.bundle.min.js"
  class="astro-vvvwv3sm"></script>


</body>

</html>