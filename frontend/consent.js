const resolveApiUrl = (path) => {
    const base = (window.API_BASE_URL || '').replace(/\/$/, '');
    const normalizedPath = path.startsWith('/') ? path : `/${path}`;
    return `${base}${normalizedPath}`;
};

document.addEventListener('DOMContentLoaded', () => {
    const storageKey = 'cookieConsentDecision';
    const preferenceViewedKey = 'cookieConsentPreferencesViewed';

    if (localStorage.getItem(storageKey)) {
        return;
    }

    const consentBanner = document.createElement('div');
    consentBanner.className = 'cookie-consent-banner';
    consentBanner.innerHTML = `
        <div class="consent-content">
            <div class="consent-brand">
                <span class="brand-dot"></span>
                <span>Site Web Montpellier</span>
            </div>
            <p>Nous utilisons des cookies essentiels pour offrir une expérience fluide. Choisissez votre préférence ou consultez les options avant de continuer.</p>
            <div class="consent-actions">
                <button type="button" class="btn-accept">Accepter</button>
                <button type="button" class="btn-refuse">Refuser</button>
                <button type="button" class="btn-preferences">Voir les préférences</button>
            </div>
            <div class="consent-details" style="display:none;"></div>
        </div>
    `;

    document.body.appendChild(consentBanner);

    const btnAccept = consentBanner.querySelector('.btn-accept');
    const btnRefuse = consentBanner.querySelector('.btn-refuse');
    const btnPreferences = consentBanner.querySelector('.btn-preferences');
    const detailsContainer = consentBanner.querySelector('.consent-details');

    const sendConsentChoice = (choice, details = '') => {
        fetch(resolveApiUrl('api.php?action=log-cookie-consent'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                choice,
                page_url: window.location.href,
                site_name: 'Site Web Montpellier',
                details
            }),
            credentials: 'include'
        }).catch((error) => {
            console.warn('[Consent]', error);
        });
    };

    const hideBanner = () => {
        consentBanner.style.opacity = '0';
        consentBanner.style.pointerEvents = 'none';
        window.setTimeout(() => {
            consentBanner.remove();
        }, 250);
    };

    btnAccept.addEventListener('click', () => {
        localStorage.setItem(storageKey, 'accept');
        sendConsentChoice('accept', 'L\'utilisateur a accepté les cookies.');
        hideBanner();
    });

    btnRefuse.addEventListener('click', () => {
        localStorage.setItem(storageKey, 'refuse');
        sendConsentChoice('refuse', 'L\'utilisateur a refusé les cookies.');
        hideBanner();
    });

    btnPreferences.addEventListener('click', () => {
        const viewed = localStorage.getItem(preferenceViewedKey);
        if (!viewed) {
            localStorage.setItem(preferenceViewedKey, 'true');
            sendConsentChoice('preferences', 'L\'utilisateur a consulté les préférences de cookies.');
        }

        detailsContainer.textContent = 'Préférences de cookies :\n- Essentiels : toujours activés\n- Analytiques : aident à améliorer le site\n- Marketing : utilisés pour afficher des offres personnalisées';
        detailsContainer.style.display = 'block';
        detailsContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});
