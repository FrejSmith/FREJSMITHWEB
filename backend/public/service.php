<?php
$services = [
    'site-internet' => [
        'name' => 'Création de Site Internet',
        'description' => 'Un site web sur mesure, unique et attractif. Transformez chaque clic en une étape vers le succès.',
        'long_desc' => 'Nous créons des sites web performants, modernes et convertissants. Avec nos solutions WordPress, Symfony et custom, votre présence en ligne sera optimale pour les moteurs de recherche et vos visiteurs.'
    ],
    'application-web' => [
        'name' => 'Application Web',
        'description' => 'Une application web alliant excellence et créativité. Transformez toutes vos idées en expériences exceptionnelles.',
        'long_desc' => 'Développement d\'applications web robustes et scalables, avec les dernières technologies. De la conception à la mise en production, nous assurons un accompagnement complet.'
    ],
    'seo' => [
        'name' => 'Référencement SEO',
        'description' => 'Un référencement naturel optimisé sur plusieurs mois pour atteindre les premières positions Google.',
        'long_desc' => 'Amélirez votre visibilité sur les moteurs de recherche avec une stratégie SEO personnalisée. Audit, optimisation on-page, netlinking et suivi des performances.'
    ],
    'ux-ui' => [
        'name' => 'UX/UI Design',
        'description' => 'Un design captivant, une interface intuitive et une expérience utilisateur qui séduit et convertit.',
        'long_desc' => 'Conception d\'interfaces modernes et ergonomiques. Nos designers créent des expériences utilisateur inoubliables qui augmentent vos conversions.'
    ],
    'graphisme' => [
        'name' => 'Graphisme',
        'description' => 'Un graphisme innovant et une identité qui capte l\'attention et favorise l\'engagement.',
        'long_desc' => 'Création d\'identités visuelles, logos, branding complet. Nous transformons votre vision en design mémorable et impactant.'
    ],
    'maintenance' => [
        'name' => 'Maintenance et Support',
        'description' => 'Une assistance professionnelle et adaptée 7j/7 pour maintenir votre site web à tout moment.',
        'long_desc' => 'Maintenance continue, mises à jour sécurité, support technique réactif. Nous assurons que votre site reste performant et sécurisé au quotidien.'
    ]
];

$service = isset($_GET['service']) ? $_GET['service'] : 'site-internet';
$service_data = $services[$service] ?? $services['site-internet'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service_data['name']); ?> - FrejSmithWeb</title>
    <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <header class="header">
        <div class="container header-content">
            <div class="logo-section">
                <div class="logo">
                    <img src="images/fsw.png" alt="FrejSmithWeb Logo">
                </div>
                <div>
                    <h1>FrejSmithWeb</h1>
                    <p class="subline">FrejSmithWeb - IA & Innovation New</p>
                </div>
            </div>
            <nav class="nav">
                <a href="index.html" data-i18n="nav_home">Accueil</a>
                <a href="agences.html" data-i18n="nav_agences">Agences</a>
                <div class="nav-dropdown">
                    <a href="#offres" data-i18n="nav_domaines">Domaines d'expertises</a>
                    <div class="dropdown-menu">
                        <a href="service.php?service=site-internet">Création de Site Internet</a>
                        <a href="service.php?service=application-web">Application Web</a>
                        <a href="service.php?service=seo">Référencement SEO</a>
                        <a href="service.php?service=ux-ui">UX/UI Design</a>
                        <a href="service.php?service=graphisme">Graphisme</a>
                        <a href="service.php?service=maintenance">Maintenance et Support</a>
                    </div>
                </div>
                <a href="index.html#realisations" data-i18n="nav_refer">Références</a>
                <a href="blog.html" data-i18n="nav_blog">Blog</a>
                <a href="contact.html" data-i18n="nav_contact">Contactez-nous</a>
                <div class="lang-switcher">
                    <select id="languageSelector" aria-label="Langue">
                        <option value="fr">FR</option>
                        <option value="en">EN</option>
                        <option value="es">ES</option>
                        <option value="de">DE</option>
                    </select>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero" style="padding: 60px 0;">
            <div class="container">
                <span class="hero-badge" style="margin-bottom:24px;" data-i18n="service_page_header">Service</span>
                <h2><?php echo htmlspecialchars($service_data['name']); ?></h2>
                <p><?php echo htmlspecialchars($service_data['description']); ?></p>
            </div>
        </section>

        <section class="expertise-section">
            <div class="container">
                <div class="section-intro">
                    <h3><?php echo htmlspecialchars($service_data['name']); ?></h3>
                    <p><?php echo htmlspecialchars($service_data['long_desc']); ?></p>
                </div>
                <div style="display: grid; grid-template-columns: 1fr; max-width: 600px;">
                    <img src="https://via.placeholder.com/600x400?text=<?php echo urlencode($service_data['name']); ?>" alt="<?php echo htmlspecialchars($service_data['name']); ?>" style="width:100%; border-radius:18px; margin-bottom:32px;">
                </div>
            </div>
        </section>

        <section class="offers-cta">
            <div class="container" style="text-align:center;">
                <div>
                    <span class="eyebrow" data-i18n="service_page_contact_title">Contactez-nous</span>
                    <h3 data-i18n="service_page_contact_title">Vous êtes intéressé ?</h3>
                    <p data-i18n="service_page_contact_text">Discutons ensemble de comment nous pouvons vous aider avec ce service.</p>
                </div>
            </div>
        </section>

        <section class="expertise-section" style="padding: 48px 0;">
            <div class="container">
                <div style="max-width: 700px; margin: 0 auto;">
                    <form action="contact.html" method="GET" class="contact-form">
                        <div style="margin-bottom: 24px;">
                            <label for="name" style="display:block; margin-bottom:8px; color:#d8b4fe; font-weight:500;" data-i18n="label_name">Nom *</label>
                            <input type="text" id="name" name="name" required style="width:100%; padding:12px; background:#0b0716; border:1px solid rgba(124, 58, 237, 0.2); border-radius:8px; color:#ede9ff; font-size:16px;">
                        </div>
                        <div style="margin-bottom: 24px;">
                            <label for="email" style="display:block; margin-bottom:8px; color:#d8b4fe; font-weight:500;" data-i18n="label_email">Email *</label>
                            <input type="email" id="email" name="email" required style="width:100%; padding:12px; background:#0b0716; border:1px solid rgba(124, 58, 237, 0.2); border-radius:8px; color:#ede9ff; font-size:16px;">
                        </div>
                        <div style="margin-bottom: 24px;">
                            <label for="phone" style="display:block; margin-bottom:8px; color:#d8b4fe; font-weight:500;" data-i18n="label_phone">Téléphone</label>
                            <input type="tel" id="phone" name="phone" style="width:100%; padding:12px; background:#0b0716; border:1px solid rgba(124, 58, 237, 0.2); border-radius:8px; color:#ede9ff; font-size:16px;">
                        </div>
                        <div style="margin-bottom: 24px;">
                            <label for="message" style="display:block; margin-bottom:8px; color:#d8b4fe; font-weight:500;" data-i18n="label_message">Message *</label>
                            <textarea id="message" name="message" rows="6" required style="width:100%; padding:12px; background:#0b0716; border:1px solid rgba(124, 58, 237, 0.2); border-radius:8px; color:#ede9ff; font-size:16; font-family:inherit; resize:vertical;"></textarea>
                        </div>
                        <p style="color:#c4b5fd; margin-bottom:24px; font-size:14px;">Nous vous répondrons rapidement au <strong>06 41 31 32 00</strong> ou par email.</p>
                        <button type="submit" class="btn btn-primary" style="width:100%; padding:16px;" data-i18n="btn_send">Envoyer votre demande</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="lang.js"></script>
    <footer class="footer">
        <div class="container footer-grid">
            <div>
                <h4>FrejSmithWeb</h4>
                <p>Agence digitale à Paris, Annecy, Toulouse et Léguevin depuis 2019.</p>
            </div>
            <div>
                <h5>Menu</h5>
                <a href="index.html">Accueil</a>
                <a href="index.html#offres">Nos offres</a>
                <a href="index.html#realisations">Références</a>
                <a href="blog.html">Blog</a>
                <a href="contact.html">Contactez-nous</a>
            </div>
            <div>
                <h5>Support</h5>
                <a href="contact.html">Contactez-nous</a>
                <a href="blog.html">Blog</a>
                <a href="#">Sitemap</a>
            </div>
            <div>
                <h5>Contact</h5>
                <p><strong>06 41 31 32 00</strong></p>
                <a href="contact.html">Nous écrire</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>FrejSmithWeb 2019 - 2026. Tous droits réservés.</p>
            <div class="footer-links">
                <a href="#">Mentions légales</a>
                <a href="#">CGV</a>
                <a href="#">CGU</a>
            </div>
        </div>
    </footer>
</body>
</html>
