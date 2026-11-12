<?php
declare(strict_types=1);

session_start();

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitizeText(mixed $value): string
{
    if (!is_string($value)) {
        return '';
    }

    return trim(strip_tags($value));
}

$csrfToken = $_SESSION['clase5_csrf'] ?? '';
if (!is_string($csrfToken) || $csrfToken === '') {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['clase5_csrf'] = $csrfToken;
}

$contact = [
    'name' => '',
    'email' => '',
    'message' => '',
];
$contactErrors = [];
$contactSuccess = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $submittedToken = sanitizeText($_POST['csrf_token'] ?? null);
    if (!hash_equals($csrfToken, $submittedToken)) {
        $contactErrors[] = 'La sesión del formulario venció. Actualizá la página e intentá nuevamente.';
    }

    $contact['name'] = sanitizeText($_POST['name'] ?? null);
    $contact['email'] = sanitizeText($_POST['email'] ?? null);
    $contact['message'] = sanitizeText($_POST['message'] ?? null);

    if ($contact['name'] === '') {
        $contactErrors[] = 'Ingresá tu nombre.';
    } elseif (preg_match('/^.{1,120}$/us', $contact['name']) !== 1) {
        $contactErrors[] = 'El nombre debe tener como máximo 120 caracteres.';
    }

    if ($contact['email'] === '') {
        $contactErrors[] = 'Ingresá tu correo electrónico.';
    } elseif (
        strlen($contact['email']) > 254
        || filter_var($contact['email'], FILTER_VALIDATE_EMAIL) === false
    ) {
        $contactErrors[] = 'Ingresá un correo electrónico válido.';
    }

    if ($contact['message'] === '') {
        $contactErrors[] = 'Escribí tu mensaje.';
    } elseif (preg_match('/^.{10,2000}$/us', $contact['message']) !== 1) {
        $contactErrors[] = 'El mensaje debe tener entre 10 y 2000 caracteres.';
    }

    if ($contactErrors === []) {
        $_SESSION['clase5_last_contact'] = $contact;
        $contactSuccess = 'Formulario procesado correctamente. Los datos se conservaron en esta sesión.';
        $contact = ['name' => '', 'email' => '', 'message' => ''];
    }
}

$query = sanitizeText($_GET['q'] ?? null);
$searchError = '';
if (preg_match('/^.{0,80}$/us', $query) !== 1) {
    $searchError = 'La búsqueda debe tener como máximo 80 caracteres.';
    $query = '';
}

$news = [
    [
        'title' => 'Gimnasia y Tiro se prepara para su próximo partido',
        'summary' => 'El plantel entrena en Salta con la mirada puesta en el próximo compromiso.',
        'category' => 'Actualidad',
    ],
    [
        'title' => 'El Gigante del Norte espera a su gente',
        'summary' => 'Los hinchas acompañarán al Albo en una nueva fecha como local.',
        'category' => 'Club',
    ],
    [
        'title' => 'Una historia que representa a Salta',
        'summary' => 'Repasamos algunos momentos destacados de Gimnasia y Tiro.',
        'category' => 'Historia',
    ],
];

$filteredNews = array_values(array_filter(
    $news,
    static function (array $item) use ($query): bool {
        return $query === ''
            || stripos($item['title'], $query) !== false
            || stripos($item['summary'], $query) !== false
            || stripos($item['category'], $query) !== false;
    }
));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formularios POST y GET con validación y protección contra XSS.">
    <title>Clase 5 | Formularios seguros - Gimnasia y Tiro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="../index.html" aria-label="Volver al sitio de Gimnasia y Tiro">
            <span class="brand-mark">G</span>
            <span>Gimnasia y Tiro <small>Clase 5 · Formularios seguros</small></span>
        </a>
        <a class="back-link" href="../index.html">Volver al sitio</a>
    </header>

    <main>
        <section class="intro">
            <h1>Formularios</h1>
        </section>

        <div class="content-grid">
            <section class="panel" aria-labelledby="contact-title">
                <div class="panel-heading">
                    <span class="method-badge post-badge">POST</span>
                    <div>
                        <h2 id="contact-title">Contacto</h2>
                        <p>Completá los datos para procesar tu mensaje.</p>
                    </div>
                </div>

                <?php if ($contactSuccess !== ''): ?>
                    <div class="notice notice-success" role="status"><?= escapeHtml($contactSuccess) ?></div>
                <?php endif; ?>

                <?php if ($contactErrors !== []): ?>
                    <div class="notice notice-error" role="alert">
                        <strong>Revisá estos datos:</strong>
                        <ul>
                            <?php foreach ($contactErrors as $error): ?>
                                <li><?= escapeHtml($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php" class="form-stack">
                    <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">

                    <label for="name">Nombre y apellido</label>
                    <input id="name" name="name" type="text" maxlength="120" autocomplete="name" value="<?= escapeHtml($contact['name']) ?>" required>

                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" maxlength="254" autocomplete="email" value="<?= escapeHtml($contact['email']) ?>" required>

                    <label for="message">Mensaje</label>
                    <textarea id="message" name="message" rows="5" minlength="10" maxlength="2000" required><?= escapeHtml($contact['message']) ?></textarea>
                    <p class="field-help">Entre 10 y 2000 caracteres.</p>

                    <button class="button button-primary" type="submit">Procesar mensaje</button>
                </form>
                <p class="privacy-note">La confirmación es de esta demostración local; el formulario no envía correos.</p>
            </section>

            <section class="panel" aria-labelledby="search-title">
                <div class="panel-heading">
                    <span class="method-badge get-badge">GET</span>
                    <div>
                        <h2 id="search-title">Buscar novedades</h2>
                        <p>Filtrá las noticias por palabra o categoría.</p>
                    </div>
                </div>

                <?php if ($searchError !== ''): ?>
                    <div class="notice notice-error" role="alert"><?= escapeHtml($searchError) ?></div>
                <?php endif; ?>

                <form method="get" action="index.php" class="search-form">
                    <label for="q">Palabra clave</label>
                    <div class="search-row">
                        <input id="q" name="q" type="search" maxlength="80" value="<?= escapeHtml($query) ?>" placeholder="Ej.: estadio" aria-describedby="search-help">
                        <button class="button button-primary" type="submit">Buscar</button>
                    </div>
                    <p id="search-help" class="field-help">Probá con «Albo», «club» o «historia».</p>
                </form>

                <div class="results" aria-live="polite">
                    <h3><?= $query === '' ? 'Novedades' : 'Resultados para “' . escapeHtml($query) . '”' ?></h3>
                    <?php if ($filteredNews === []): ?>
                        <p class="empty-state">No encontramos novedades con ese término. Probá otra búsqueda.</p>
                    <?php else: ?>
                        <?php foreach ($filteredNews as $item): ?>
                            <article class="result-card">
                                <span><?= escapeHtml($item['category']) ?></span>
                                <h4><?= escapeHtml($item['title']) ?></h4>
                                <p><?= escapeHtml($item['summary']) ?></p>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer class="site-footer">
        <p>Gimnasia y Tiro · Clase 5 · PHP, POST, GET y salida segura</p>
    </footer>
</body>
</html>
