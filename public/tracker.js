/**
 * Système de tracking et sécurité avancé FrejSmithWeb
 * Détecte les menaces, trace les visiteurs et enregistre les événements de sécurité
 */

(function() {
    'use strict';

    const TRACKER_CONFIG = {
        apiEndpoint: '/api.php',
        trackingInterval: 30000, // 30 secondes
        reportInterval: 60000,   // 60 secondes
        maxLocalEvents: 50
    };

    class SecurityTracker {
        constructor() {
            this.sessionId = this.generateSessionId();
            this.fingerprint = '';
            this.localEvents = [];
            this.lastReportTime = Date.now();
            this.suspiciousActivities = {
                automationDetected: false,
                rapidClicks: 0,
                devToolsOpen: false,
                unusualNavigation: false,
                evalAttempt: false,
                scriptInjection: false
            };
            
            this.init();
        }

        init() {
            // Générer l'empreinte du navigateur
            this.fingerprint = this.getBrowserFingerprint();
            
            // Configuration des événements de sécurité
            this.setupSecurityListeners();
            
            // Lancer le suivi des visiteurs
            this.startTracking();
            
            // Lancer la détection de menaces
            this.startThreatDetection();
            
            // Gérer les erreurs JavaScript
            this.setupErrorHandler();
            
            // Envoyer les données de tracking périodiquement
            this.startReporting();
            
            console.log('[SecurityTracker] Initialisé avec session:', this.sessionId.substring(0, 8) + '...');
        }

        /**
         * Générer un ID de session unique
         */
        generateSessionId() {
            return 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        /**
         * Générer l'empreinte digitale du navigateur
         */
        getBrowserFingerprint() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.textBaseline = 'alphabetic';
            ctx.fillStyle = '#f60';
            ctx.fillRect(125, 1, 62, 20);
            ctx.fillStyle = '#069';
            ctx.fillText('FrejSmith' + new Date().getTime(), 2, 15);
            ctx.fillStyle = 'rgba(102, 204, 0, 0.7)';
            ctx.fillText('FrejSmith' + new Date().getTime(), 4, 17);

            const fingerprint = [
                navigator.userAgent,
                navigator.language,
                screen.width + 'x' + screen.height,
                screen.colorDepth,
                new Date().getTimezoneOffset(),
                typeof localStorage !== 'undefined',
                typeof sessionStorage !== 'undefined',
                navigator.hardwareConcurrency || 'unknown',
                navigator.deviceMemory || 'unknown',
                navigator.maxTouchPoints || 0,
                canvas.toDataURL(),
                navigator.plugins.length
            ].join('|');

            return this.simpleHash(fingerprint);
        }

        /**
         * Hash simple pour créer une empreinte
         */
        simpleHash(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                const char = str.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash;
            }
            return 'fp_' + Math.abs(hash).toString(36);
        }

        /**
         * Configuration des écouteurs de sécurité
         */
        setupSecurityListeners() {
            // Détecter l'ouverture des outils de développement
            this.detectDevTools();
            
            // Monitorer les clics rapides
            document.addEventListener('click', (e) => {
                if (!this.lastClickTime) this.lastClickTime = Date.now();
                const timeSinceLastClick = Date.now() - this.lastClickTime;
                if (timeSinceLastClick < 100 && timeSinceLastClick > 0) {
                    this.suspiciousActivities.rapidClicks++;
                }
                this.lastClickTime = Date.now();
            });

            // Monitorer les modifications du DOM
            if (window.MutationObserver) {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'childList') {
                            mutation.addedNodes.forEach((node) => {
                                if (node.nodeType === 1 && node.tagName === 'SCRIPT') {
                                    if (!node.src) {
                                        this.suspiciousActivities.scriptInjection = true;
                                        this.logEvent('script_injection', 'critical', 'Script inline injecté détecté');
                                    }
                                }
                            });
                        }
                    });
                });

                observer.observe(document, {
                    childList: true,
                    subtree: true,
                    attributes: false
                });
            }

            // Prévenir les tentatives eval
            const originalEval = window.eval;
            window.eval = (...args) => {
                this.suspiciousActivities.evalAttempt = true;
                this.logEvent('eval_attempt', 'critical', 'Tentative d\'eval détectée');
                throw new Error('eval() est désactivé pour des raisons de sécurité');
            };

            // Monitorer la navigation inhabituellement rapide
            this.navigationStartTime = Date.now();
            window.addEventListener('beforeunload', () => {
                const timeOnPage = Date.now() - this.navigationStartTime;
                if (timeOnPage < 1000) {
                    this.suspiciousActivities.unusualNavigation = true;
                }
            });
        }

        /**
         * Détecter l'ouverture des outils de développement
         */
        detectDevTools() {
            let devtools = { open: false, orientation: null };
            
            const threshold = 160;
            setInterval(() => {
                if (window.outerHeight - window.innerHeight > threshold || 
                    window.outerWidth - window.innerWidth > threshold) {
                    if (!devtools.open) {
                        devtools.open = true;
                        this.suspiciousActivities.devToolsOpen = true;
                        this.logEvent('dev_tools_open', 'medium', 'Outils de développement ouverts');
                    }
                } else {
                    devtools.open = false;
                }
            }, 500);

            // Détecter par console
            const check = () => {
                const before = new Date().getTime();
                debugger;
                const after = new Date().getTime();
                if (after - before > 100) {
                    this.suspiciousActivities.devToolsOpen = true;
                }
            };

            if (document.body) {
                setTimeout(check, 0);
            }
        }

        /**
         * Démarrer le suivi des visiteurs
         */
        startTracking() {
            const trackVisitor = () => {
                const data = {
                    page: window.location.pathname,
                    session_id: this.sessionId,
                    fingerprint: this.fingerprint,
                    security_data: {
                        userAgent: navigator.userAgent,
                        referrer: document.referrer,
                        url: window.location.href,
                        timestamp: Date.now(),
                        suspicious: this.suspiciousActivities,
                        screenResolution: screen.width + 'x' + screen.height,
                        timezone: new Date().getTimezoneOffset(),
                        language: navigator.language,
                        plugins: navigator.plugins.length
                    }
                };

                this.sendData('track-visitor', data);
            };

            // Premier tracking immédiatement
            trackVisitor();

            // Puis périodiquement
            setInterval(trackVisitor, TRACKER_CONFIG.trackingInterval);
        }

        /**
         * Démarrer la détection de menaces
         */
        startThreatDetection() {
            const detectThreat = () => {
                const threats = [];

                // Vérifier les comportements suspects
                if (this.suspiciousActivities.automationDetected) {
                    threats.push('automation');
                }
                if (this.suspiciousActivities.rapidClicks > 5) {
                    threats.push('rapid_clicks');
                }
                if (this.suspiciousActivities.devToolsOpen) {
                    threats.push('dev_tools');
                }
                if (this.suspiciousActivities.evalAttempt) {
                    threats.push('eval_attempt');
                }
                if (this.suspiciousActivities.scriptInjection) {
                    threats.push('script_injection');
                }

                // Vérifier les URLs suspectes
                const suspiciousPatterns = [
                    /union.*select/i,
                    /select.*from/i,
                    /drop.*table/i,
                    /<script/i,
                    /javascript:/i,
                    /onerror=/i,
                    /onclick=/i
                ];

                const url = window.location.href;
                suspiciousPatterns.forEach(pattern => {
                    if (pattern.test(url)) {
                        threats.push('suspicious_url');
                    }
                });

                if (threats.length > 0) {
                    this.logEvent('threat_detected', 'high', 'Menace détectée: ' + threats.join(', '));
                }
            };

            setInterval(detectThreat, 5000);
        }

        /**
         * Configurer le gestionnaire d'erreurs global
         */
        setupErrorHandler() {
            window.addEventListener('error', (event) => {
                this.logJsError(event);
            });

            window.addEventListener('unhandledrejection', (event) => {
                this.logJsError({
                    message: 'Unhandled Promise Rejection: ' + event.reason,
                    filename: 'Promise',
                    lineno: 0,
                    colno: 0
                });
            });
        }

        /**
         * Enregistrer une erreur JavaScript
         */
        logJsError(event) {
            const errorData = {
                error: {
                    message: event.message || 'Erreur inconnue',
                    filename: event.filename || '',
                    lineno: event.lineno || 0,
                    colno: event.colno || 0
                },
                userAgent: navigator.userAgent,
                url: window.location.href,
                sessionId: this.sessionId,
                fingerprint: this.fingerprint,
                timestamp: Date.now()
            };

            this.sendData('log-js-error', { security_log: JSON.stringify(errorData) });
        }

        /**
         * Enregistrer un événement de sécurité
         */
        logEvent(eventType, severity, description) {
            const eventData = {
                eventType: eventType,
                severity: severity,
                description: description,
                userAgent: navigator.userAgent,
                referrer: document.referrer,
                url: window.location.href,
                sessionId: this.sessionId,
                fingerprint: this.fingerprint,
                timestamp: Date.now(),
                suspicious: this.suspiciousActivities
            };

            this.localEvents.push(eventData);

            // Limiter la taille du cache local
            if (this.localEvents.length > TRACKER_CONFIG.maxLocalEvents) {
                this.localEvents.shift();
            }

            // Envoyer immédiatement les événements critiques
            if (severity === 'critical') {
                this.sendData('log-security-event', { security_log: JSON.stringify(eventData) });
            }
        }

        /**
         * Démarrer la transmission des rapports
         */
        startReporting() {
            setInterval(() => {
                if (this.localEvents.length > 0) {
                    // Envoyer les événements accumulés
                    this.localEvents.forEach(event => {
                        if (event.severity !== 'critical') {
                            this.sendData('log-security-event', { security_log: JSON.stringify(event) });
                        }
                    });
                    this.localEvents = [];
                }
            }, TRACKER_CONFIG.reportInterval);
        }

        /**
         * Envoyer les données au serveur
         */
        sendData(action, data) {
            const payload = { action, ...data };

            // Utiliser fetch avec gestion des erreurs
            fetch(TRACKER_CONFIG.apiEndpoint + '?action=' + action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data),
                credentials: 'include',
                timeout: 5000
            }).catch(error => {
                console.warn('[SecurityTracker] Erreur lors de l\'envoi des données:', error);
            });
        }

        /**
         * Détecter les comportements d'automatisation
         */
        detectAutomation() {
            // Vérifier les webdrivers et Puppeteer
            if (navigator.webdriver) {
                this.suspiciousActivities.automationDetected = true;
                return true;
            }

            // Vérifier les outils Selenium
            if (window.__nightmareDebugFn || window.__nightmare) {
                this.suspiciousActivities.automationDetected = true;
                return true;
            }

            // Vérifier Phantom
            if (window.callPhantom || window._phantom) {
                this.suspiciousActivities.automationDetected = true;
                return true;
            }

            // Vérifier les caractéristiques headless
            const headlessDetection = [
                !window.chrome,
                window.chrome && !window.chrome.webstore,
                !document.fonts || document.fonts.size === 0
            ];

            if (headlessDetection.some(x => x)) {
                // Possible browser headless
            }

            return false;
        }
    }

    // Initialiser le tracker quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.securityTracker = new SecurityTracker();
        });
    } else {
        window.securityTracker = new SecurityTracker();
    }

    /**
     * API publique pour intégration manuelle
     */
    window.trackEvent = function(eventName, eventData) {
        if (window.securityTracker) {
            window.securityTracker.logEvent(eventName, 'medium', JSON.stringify(eventData));
        }
    };

    window.reportError = function(error) {
        if (window.securityTracker) {
            window.securityTracker.logJsError({
                message: error.message || String(error),
                filename: 'Custom Error',
                lineno: 0,
                colno: 0
            });
        }
    };

})();
