<?php require_once __DIR__ . '/header.php'; ?>

<div class="container-fluid p-0">

    <!-- HERO -->
    <section class="bg-dark text-white text-center py-5"
        style="background: linear-gradient(rgba(0,0,0,.6), rgba(0,0,0,.6)), url('img/empresa-banner.jpg') center/cover;">
        <div class="container">
            <h1 class="display-4 fw-bold">Nuestra Empresa</h1>
            <p class="lead">Comprometidos con la calidad y el mejor servicio</p>
        </div>
    </section>

    <!-- QUIENES SOMOS -->
    <section class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <img src="img/choza.jpg" class="img-fluid rounded shadow" alt="Empresa">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-3">¿Quiénes Somos?</h2>
                <p class="text-muted">
                    Somos una empresa dedicada a ofrecer productos de alta calidad,
                    brindando una experiencia única a nuestros clientes.
                    Nuestro compromiso es innovar constantemente y mejorar cada día.
                </p>
                <p class="text-muted">
                    Contamos con un equipo profesional que trabaja con pasión,
                    responsabilidad y compromiso.
                </p>
            </div>
        </div>
    </section>

    <!-- MISION Y VISION -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row g-4 text-center">

                <div class="col-md-6">
                    <div class="card border-0 shadow h-100 card-hover">
                        <div class="card-body p-4">
                            <h3 class="fw-bold text-primary mb-3">🎯 Misión</h3>
                            <p class="text-muted">
                                Brindar productos y servicios de calidad,
                                garantizando satisfacción y confianza
                                a nuestros clientes mediante innovación constante.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow h-100 card-hover">
                        <div class="card-body p-4">
                            <h3 class="fw-bold text-success mb-3">🚀 Visión</h3>
                            <p class="text-muted">
                                Ser una empresa líder en el mercado,
                                reconocida por su excelencia,
                                innovación y compromiso con la comunidad.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- VALORES -->
    <section class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Nuestros Valores</h2>
        </div>

        <div class="row text-center g-4">

            <div class="col-md-3">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4>🤝</h4>
                    <h5 class="fw-bold mt-2">Responsabilidad</h5>
                    <p class="text-muted small">Cumplimos nuestros compromisos con ética y transparencia.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4>💡</h4>
                    <h5 class="fw-bold mt-2">Innovación</h5>
                    <p class="text-muted small">Buscamos mejorar continuamente nuestros procesos.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4>⭐</h4>
                    <h5 class="fw-bold mt-2">Calidad</h5>
                    <p class="text-muted small">Ofrecemos productos y servicios de excelencia.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4>❤️</h4>
                    <h5 class="fw-bold mt-2">Compromiso</h5>
                    <p class="text-muted small">Trabajamos enfocados en la satisfacción del cliente.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- LOCALES -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nuestros Locales</h2>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="card border-0 shadow">
                        <img src="img/local1.jpg" class="card-img-top" style="height:220px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="fw-bold">Local Centro</h5>
                            <p class="text-muted small">
                                Av. Principal 123, Lima<br>
                                📞 987 654 321
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow">
                        <img src="img/local2.jpg" class="card-img-top" style="height:220px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="fw-bold">Local Norte</h5>
                            <p class="text-muted small">
                                Av. Norte 456, Lima<br>
                                📞 912 345 678
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow">
                        <img src="img/local3.jpg" class="card-img-top" style="height:220px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="fw-bold">Local Sur</h5>
                            <p class="text-muted small">
                                Av. Sur 789, Lima<br>
                                📞 923 456 789
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
