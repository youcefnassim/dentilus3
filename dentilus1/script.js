// Script pour gérer l'interface du logiciel dentaire - Version améliorée

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des fonctionnalités
    initializeApp();
    
    function initializeApp() {
        setupNavigation();
        setupAnimations();
        setupRealTimeUpdates();
        setupPatientInteractions();
        setupTooltips();
        setupMobileMenu();
        setupDarkMode();
        setupNotifications();
        setupFormValidation();
        setupSearchAndFilters();
        setupModals();
        setupTabs();
        setupPagination();
        setupKeyboardShortcuts();
    }
    
    // Gestion de la navigation
    function setupNavigation() {
        const menuItems = document.querySelectorAll('.menu a');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Retirer la classe active de tous les éléments
                menuItems.forEach(item => item.classList.remove('active'));
                
                // Ajouter la classe active à l'élément cliqué
                this.classList.add('active');
                
                // Simuler le chargement du contenu
                simulateContentLoading(this.querySelector('span').textContent);
                
                // Fermer le menu mobile si ouvert
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            });
        });
        
        // Marquer l'élément actif selon la page courante
        markActiveMenuItem();
    }
    
    function markActiveMenuItem() {
        const currentPage = window.location.pathname.split('/').pop() || 'Accueil.html';
        const menuItems = document.querySelectorAll('.menu a');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === currentPage) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
    
    // Configuration des animations
    function setupAnimations() {
        // Animation des cartes au chargement
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });
        
        // Animation des cartes patients
        const patientCards = document.querySelectorAll('.patient-card');
        patientCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.15}s`;
            card.classList.add('fade-in');
        });
        
        // Animation des éléments au scroll
        setupScrollAnimations();
    }
    
    function setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);
        
        // Observer tous les éléments avec la classe 'animate-on-scroll'
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }
    
    // Mise à jour en temps réel
    function setupRealTimeUpdates() {
        // Mettre à jour l'heure d'attente des patients toutes les minutes
        setInterval(updateWaitingTimes, 60000);
        
        // Simulation de données en temps réel
        simulateRealTimeData();
        
        // Mise à jour de l'heure actuelle
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
    }
    
    function updateCurrentTime() {
        const timeElements = document.querySelectorAll('.current-time');
        const now = new Date();
        const timeString = now.toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        timeElements.forEach(element => {
            element.textContent = timeString;
        });
    }
    
    function updateWaitingTimes() {
        const timeElements = document.querySelectorAll('.patient-card p:first-child');
        timeElements.forEach(element => {
            const text = element.textContent;
            const timeMatch = text.match(/(\d+)\s*min/);
            if (timeMatch) {
                const currentTime = parseInt(timeMatch[1]);
                element.textContent = text.replace(
                    `${currentTime} min`, 
                    `${currentTime + 1} min`
                );
            }
        });
    }
    
    function simulateRealTimeData() {
        setInterval(() => {
            // Mettre à jour le nombre de patients en salle d'attente
            const waitingCount = document.querySelectorAll('.patient-card').length;
            const waitingRoomTitle = document.querySelector('.waiting-room h3');
            if (waitingRoomTitle) {
                waitingRoomTitle.innerHTML = 
                    `<i class="fas fa-clock"></i> Salle d'attente (${waitingCount})`;
            }
                
            // Mettre à jour légèrement les statistiques
            const stats = document.querySelectorAll('.stat');
            stats.forEach(stat => {
                const currentValue = parseInt(stat.textContent.replace(/,|€|%/g, ''));
                if (!isNaN(currentValue)) {
                    const change = Math.floor(Math.random() * 3) - 1; // -1, 0, or 1
                    if (stat.textContent.includes('€')) {
                        stat.textContent = (currentValue + change) + '€';
                    } else if (stat.textContent.includes('%')) {
                        const newValue = Math.min(100, Math.max(0, currentValue + change));
                        stat.textContent = newValue + '%';
                    } else {
                        stat.textContent = currentValue + change;
                    }
                }
            });
        }, 30000);
    }
    
    // Interactions avec les patients
    function setupPatientInteractions() {
        const patientCards = document.querySelectorAll('.patient-card');
        
        patientCards.forEach(card => {
            card.addEventListener('click', function() {
                const patientName = this.querySelector('h4').textContent;
                showPatientDetails(patientName);
            });
            
            // Ajouter des boutons d'action
            addPatientActionButtons(card);
        });
    }
    
    function addPatientActionButtons(card) {
        const actionButtons = document.createElement('div');
        actionButtons.className = 'action-buttons';
        actionButtons.innerHTML = `
            <button class="btn-primary" onclick="callPatient('${card.querySelector('h4').textContent}')">
                <i class="fas fa-phone"></i> Appeler
            </button>
            <button class="btn-secondary" onclick="viewPatientDetails('${card.querySelector('h4').textContent}')">
                <i class="fas fa-eye"></i> Voir
            </button>
        `;
        card.appendChild(actionButtons);
    }
    
    // Menu mobile
    function setupMobileMenu() {
        // Créer le bouton de menu mobile
        const mobileToggle = document.createElement('button');
        mobileToggle.className = 'mobile-menu-toggle';
        mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
        mobileToggle.style.display = 'none';
        document.body.appendChild(mobileToggle);
        
        // Gérer l'ouverture/fermeture du menu
        mobileToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        });
        
        // Fermer le menu en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            
            if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
        
        // Afficher/masquer le bouton selon la taille d'écran
        function toggleMobileButton() {
            if (window.innerWidth <= 480) {
                mobileToggle.style.display = 'block';
            } else {
                mobileToggle.style.display = 'none';
                document.querySelector('.sidebar').classList.remove('open');
            }
        }
        
        window.addEventListener('resize', toggleMobileButton);
        toggleMobileButton();
    }
    
    // Mode sombre
    function setupDarkMode() {
        // Créer le bouton de mode sombre
        const darkModeToggle = document.createElement('button');
        darkModeToggle.className = 'dark-mode-toggle';
        darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        document.body.appendChild(darkModeToggle);
        
        // Gérer le basculement du mode sombre
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            
            // Changer l'icône
            this.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            
            // Sauvegarder la préférence
            localStorage.setItem('darkMode', isDark);
        });
        
        // Restaurer la préférence
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }
    }
    
    // Système de notifications
    function setupNotifications() {
        window.showNotification = function(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:white;margin-left:10px;cursor:pointer;">×</button>
            `;
            
            document.body.appendChild(notification);
            
            // Afficher la notification
            setTimeout(() => notification.classList.add('show'), 100);
            
            // Supprimer automatiquement
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, duration);
        };
    }
    
    function getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    // Validation des formulaires
    function setupFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                }
            });
        });
    }
    
    function validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                showFieldError(input, 'Ce champ est obligatoire');
                isValid = false;
            } else {
                clearFieldError(input);
            }
        });
        
        return isValid;
    }
    
    function showFieldError(field, message) {
        clearFieldError(field);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.textContent = message;
        error.style.color = 'var(--error-color)';
        error.style.fontSize = '12px';
        error.style.marginTop = '5px';
        
        field.parentNode.appendChild(error);
        field.style.borderColor = 'var(--error-color)';
    }
    
    function clearFieldError(field) {
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        field.style.borderColor = '';
    }
    
    // Recherche et filtres
    function setupSearchAndFilters() {
        const searchInputs = document.querySelectorAll('.search-box input');
        
        searchInputs.forEach(input => {
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const targetContainer = this.closest('.filter-section').nextElementSibling;
                
                if (targetContainer) {
                    filterElements(targetContainer, searchTerm);
                }
            });
        });
    }
    
    function filterElements(container, searchTerm) {
        const elements = container.querySelectorAll('.patient-card, .card');
        
        elements.forEach(element => {
            const text = element.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        });
    }
    
    // Gestion des modales
    function setupModals() {
        // Créer les modales dynamiquement
        window.openModal = function(title, content, size = 'medium') {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.innerHTML = `
                <div class="modal-content ${size}">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button class="modal-close" onclick="closeModal(this)">&times;</button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            modal.style.display = 'block';
            
            // Fermer en cliquant à l'extérieur
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal(modal.querySelector('.modal-close'));
                }
            });
        };
        
        window.closeModal = function(closeButton) {
            const modal = closeButton.closest('.modal');
            modal.remove();
        };
    }
    
    // Gestion des onglets
    function setupTabs() {
        const tabContainers = document.querySelectorAll('.tabs');
        
        tabContainers.forEach(container => {
            const tabs = container.querySelectorAll('.tab');
            const contents = container.parentNode.querySelectorAll('.tab-content');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function() {
                    // Retirer la classe active de tous les onglets et contenus
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // Ajouter la classe active à l'onglet et contenu sélectionnés
                    this.classList.add('active');
                    if (contents[index]) {
                        contents[index].classList.add('active');
                    }
                });
            });
        });
    }
    
    // Pagination
    function setupPagination() {
        const paginationContainers = document.querySelectorAll('.pagination');
        
        paginationContainers.forEach(container => {
            const buttons = container.querySelectorAll('button');
            
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    if (!this.disabled) {
                        // Retirer la classe active de tous les boutons
                        buttons.forEach(b => b.classList.remove('active'));
                        
                        // Ajouter la classe active au bouton cliqué
                        this.classList.add('active');
                        
                        // Ici on pourrait charger la page correspondante
                        console.log('Page sélectionnée:', this.textContent);
                    }
                });
            });
        });
    }
    
    // Raccourcis clavier
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Ctrl + K pour la recherche
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.search-box input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            
            // Échap pour fermer les modales
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal');
                if (openModal) {
                    openModal.remove();
                }
            }
            
            // Ctrl + D pour le mode sombre
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                document.querySelector('.dark-mode-toggle').click();
            }
        });
    }
    
    // Fonctions utilitaires
    function simulateContentLoading(sectionName) {
        console.log(`Chargement de la section: ${sectionName}`);
        showNotification(`Chargement de ${sectionName}...`, 'info', 2000);
    }
    
    function showPatientDetails(patientName) {
        const content = `
            <div class="patient-details">
                <h4>Détails du patient: ${patientName}</h4>
                <p><strong>Âge:</strong> 35 ans</p>
                <p><strong>Dernière visite:</strong> 15/04/2025</p>
                <p><strong>Prochain RDV:</strong> 20/05/2025</p>
                <p><strong>Notes:</strong> Patient régulier, aucun problème particulier.</p>
            </div>
        `;
        openModal('Détails Patient', content);
    }
    
    // Fonctions globales
    window.callPatient = function(patientName) {
        showNotification(`Appel de ${patientName}...`, 'info');
    };
    
    window.viewPatientDetails = function(patientName) {
        showPatientDetails(patientName);
    };
    
    window.addNewPatient = function() {
        const content = `
            <form class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date de naissance</label>
                        <input type="date" required>
                    </div>
                    <div class="form-group">
                        <label>Motif de consultation</label>
                        <select required>
                            <option value="">Sélectionner...</option>
                            <option value="control">Contrôle</option>
                            <option value="emergency">Urgence</option>
                            <option value="treatment">Traitement</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea placeholder="Notes additionnelles..."></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">Ajouter le patient</button>
                    <button type="button" class="btn-secondary" onclick="closeModal(this)">Annuler</button>
                </div>
            </form>
        `;
        openModal('Nouveau Patient', content, 'large');
    };
    
    window.editPatient = function(patientName) {
        showNotification(`Modification du patient ${patientName}...`, 'info');
    };
    
    window.deletePatient = function(patientName) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer ${patientName} ?`)) {
            showNotification(`Patient ${patientName} supprimé`, 'success');
        }
    };
    
    window.createNewPrescription = function() {
        const content = `
            <form class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient</label>
                        <select required>
                            <option value="">Sélectionner un patient...</option>
                            <option value="marie">Marie Lambert</option>
                            <option value="pierre">Pierre Dubois</option>
                            <option value="julie">Julie Moreau</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Médicaments</label>
                    <textarea placeholder="Liste des médicaments et posologie..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Instructions</label>
                    <textarea placeholder="Instructions particulières..."></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">Créer l'ordonnance</button>
                    <button type="button" class="btn-secondary" onclick="closeModal(this)">Annuler</button>
                </div>
            </form>
        `;
        openModal('Nouvelle Ordonnance', content, 'large');
    };
    
    window.editPrescription = function(patientName) {
        showNotification(`Modification de l'ordonnance de ${patientName}...`, 'info');
    };
    
    window.printPrescription = function(patientName) {
        showNotification(`Impression de l'ordonnance de ${patientName}...`, 'success');
    };
    
    window.deletePrescription = function(patientName) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'ordonnance de ${patientName} ?`)) {
            showNotification(`Ordonnance de ${patientName} supprimée`, 'success');
        }
    };
    
    window.useTemplate = function(templateType) {
        showNotification(`Utilisation du modèle ${templateType}...`, 'info');
    };
    
    window.addNewMedication = function() {
        const content = `
            <form class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom du médicament</label>
                        <input type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Dosage</label>
                        <input type="text" placeholder="ex: 500mg" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Quantité en stock</label>
                        <input type="number" required>
                    </div>
                    <div class="form-group">
                        <label>Seuil d'alerte</label>
                        <input type="number" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Fournisseur</label>
                    <input type="text" required>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">Ajouter le médicament</button>
                    <button type="button" class="btn-secondary" onclick="closeModal(this)">Annuler</button>
                </div>
            </form>
        `;
        openModal('Nouveau Médicament', content, 'large');
    };
    
    window.reorderMedication = function(medicationName) {
        showNotification(`Commande de ${medicationName} en cours...`, 'info');
    };
    
    window.editMedication = function(medicationName) {
        showNotification(`Modification de ${medicationName}...`, 'info');
    };
    
    window.createNewAppointment = function() {
        const content = `
            <form class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient</label>
                        <select required>
                            <option value="">Sélectionner un patient...</option>
                            <option value="marie">Marie Lambert</option>
                            <option value="pierre">Pierre Dubois</option>
                            <option value="julie">Julie Moreau</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Heure</label>
                        <input type="time" required>
                    </div>
                    <div class="form-group">
                        <label>Durée (minutes)</label>
                        <select required>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                            <option value="60">1 heure</option>
                            <option value="90">1h30</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Motif de consultation</label>
                    <select required>
                        <option value="">Sélectionner...</option>
                        <option value="control">Contrôle</option>
                        <option value="emergency">Urgence</option>
                        <option value="treatment">Traitement</option>
                        <option value="cleaning">Détartrage</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea placeholder="Notes additionnelles..."></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">Créer le RDV</button>
                    <button type="button" class="btn-secondary" onclick="closeModal(this)">Annuler</button>
                </div>
            </form>
        `;
        openModal('Nouveau Rendez-vous', content, 'large');
    };
    
    window.startAppointment = function(patientName) {
        showNotification(`Début de consultation pour ${patientName}...`, 'success');
    };
    
    window.editAppointment = function(patientName) {
        showNotification(`Modification du RDV de ${patientName}...`, 'info');
    };
    
    window.cancelAppointment = function(patientName) {
        if (confirm(`Êtes-vous sûr de vouloir annuler le RDV de ${patientName} ?`)) {
            showNotification(`RDV de ${patientName} annulé`, 'warning');
        }
    };
    
    window.exportStatistics = function() {
        showNotification('Export des statistiques en cours...', 'info');
        // Ici on pourrait générer un fichier PDF ou Excel
        setTimeout(() => {
            showNotification('Statistiques exportées avec succès !', 'success');
        }, 2000);
    };
    
    window.uploadNewPhoto = function() {
        const content = `
            <form class="form-container">
                <div class="form-group">
                    <label>Patient</label>
                    <select required>
                        <option value="">Sélectionner un patient...</option>
                        <option value="marie">Marie Lambert</option>
                        <option value="pierre">Pierre Dubois</option>
                        <option value="julie">Julie Moreau</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Type de photo</label>
                    <select required>
                        <option value="">Sélectionner...</option>
                        <option value="before">Avant traitement</option>
                        <option value="after">Après traitement</option>
                        <option value="progress">Progression</option>
                        <option value="xray">Radiographie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fichier photo</label>
                    <input type="file" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea placeholder="Description de la photo..."></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">Ajouter la photo</button>
                    <button type="button" class="btn-secondary" onclick="closeModal(this)">Annuler</button>
                </div>
            </form>
        `;
        openModal('Ajouter une Photo', content, 'large');
    };
    
    window.viewPhoto = function(photoName) {
        showNotification(`Affichage de ${photoName}...`, 'info');
    };
    
    window.downloadPhoto = function(photoName) {
        showNotification(`Téléchargement de ${photoName}...`, 'success');
    };
    
    window.deletePhoto = function(photoName) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer ${photoName} ?`)) {
            showNotification(`Photo ${photoName} supprimée`, 'success');
        }
    };
    
    window.createNewTreatment = function() {
        const content = `
            <form class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient</label>
                        <select required>
                            <option value="">Sélectionner un patient...</option>
                            <option value="marie">Marie Lambert</option>
                            <option value="pierre">Pierre Dubois</option>
                            <option value="julie">Julie Moreau</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Type de traitement</label>
                        <select required>
                            <option value="">Sélectionner...</option>
                            <option value="cleaning">Détartrage</option>
                            <option value="filling">Plombage</option>
                            <option value="orthodontics">Orthodontie</option>
                            <option value="whitening">Blanchiment</option>
                            <option value="implant">Implant</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date de début</label>
                        <input type="date" required>
                    </div>
                    <div class="form-group">
                        <label>Durée estimée (semaines)</label>
                        <input type="number" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description du traitement</label>
                    <textarea placeholder="Description détaillée du traitement..." required></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">Créer le traitement</button>
                    <button type="button" class="btn-secondary" onclick="closeModal(this)">Annuler</button>
                </div>
            </form>
        `;
        openModal('Nouveau Traitement', content, 'large');
    };
    
    window.updateTreatment = function(patientName) {
        showNotification(`Mise à jour du traitement de ${patientName}...`, 'info');
    };
    
    window.completeTreatment = function(patientName) {
        if (confirm(`Marquer le traitement de ${patientName} comme terminé ?`)) {
            showNotification(`Traitement de ${patientName} terminé`, 'success');
        }
    };
    
    // Initialiser les tooltips
    function setupTooltips() {
        const tooltipElements = document.querySelectorAll('.tooltip');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                // Les tooltips sont gérés par CSS
            });
        });
    }
    
    // Chargement des données spécifiques à chaque page
    loadPageSpecificData();
    
    function loadPageSpecificData() {
        const currentPage = window.location.pathname.split('/').pop() || 'Accueil.html';
        
        switch(currentPage) {
            case 'ordonnance.html':
                console.log('Chargement des données des ordonnances...');
                break;
            case 'médicaments.html':
                console.log('Chargement des données des médicaments...');
                break;
            case 'traitement.html':
                console.log('Chargement des données des traitements...');
                break;
            case 'travauxode.html':
                console.log('Chargement des données des travaux ODE...');
                break;
            case 'laboratoire.html':
                console.log('Chargement des données du laboratoire...');
                break;
            case 'Rdvs.html':
                console.log('Chargement des données des rendez-vous...');
                break;
            case 'galerie.html':
                console.log('Chargement des données de la galerie...');
                break;
            case 'complément.html':
                console.log('Chargement des données complémentaires...');
                break;
            case 'statistique.html':
                console.log('Chargement des données statistiques...');
                break;
            case 'paramètres.html':
                console.log('Chargement des paramètres...');
                break;
            case 'documentation.html':
                console.log('Chargement de la documentation...');
                break;
            default:
                console.log('Chargement des données du tableau de bord...');
        }
    }
});