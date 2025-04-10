// Animations et effets visuels
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes au défilement
    const animateOnScroll = () => {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            const cardTop = card.getBoundingClientRect().top;
            const triggerBottom = window.innerHeight * 0.8;
            
            if (cardTop < triggerBottom) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }
        });
    };

    // Initialisation des cartes avec une opacité de 0
    document.querySelectorAll('.card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease-out';
    });

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();

    // Initialisation des tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Gestionnaire pour les messages flash
    const flashMessage = document.querySelector('.alert');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.classList.add('fade');
            setTimeout(() => {
                flashMessage.remove();
            }, 300);
        }, 3000);
    }

    // Validation des formulaires
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Amélioration des alertes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (!alert.classList.contains('alert-permanent')) {
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        }
    });

    // Animation du menu de navigation
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Amélioration des tableaux
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
                this.style.transform = 'translateX(5px)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.transform = 'translateX(0)';
            });
        });
    });

    // Amélioration des boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });

    // Gestion des formulaires
    const formInputs = document.querySelectorAll('form input, form textarea');
    formInputs.forEach(input => {
        // Animation du focus
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});

// Fonction pour confirmer la suppression
function confirmDelete(event, message) {
    if (!confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?')) {
        event.preventDefault();
    }
}

// Fonction pour mettre à jour le statut d'un ticket
async function updateTicketStatus(ticketId, status) {
    try {
        const response = await fetch(`/tickets/${ticketId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        });
        
        if (!response.ok) throw new Error('Erreur lors de la mise à jour');
        
        const data = await response.json();
        if (data.success) {
            showToast('Statut mis à jour avec succès', 'success');
            // Mettre à jour l'interface utilisateur
            document.querySelector(`#ticket-${ticketId} .status`).textContent = status;
        }
    } catch (error) {
        showToast('Erreur lors de la mise à jour du statut', 'error');
        console.error('Erreur:', error);
    }
}

// Fonction pour charger les commentaires
async function loadComments(ticketId) {
    try {
        const response = await fetch(`/tickets/${ticketId}/comments`);
        const comments = await response.json();
        
        const commentsContainer = document.querySelector('#comments-container');
        commentsContainer.innerHTML = comments.map(comment => `
            <div class="comment">
                <strong>${escapeHtml(comment.username)}</strong>
                <small>${formatDate(comment.created_at)}</small>
                <p>${escapeHtml(comment.content)}</p>
            </div>
        `).join('');
    } catch (error) {
        console.error('Erreur lors du chargement des commentaires:', error);
    }
}

// Fonction pour échapper le HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Fonction pour créer une notification toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} show`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">${type === 'success' ? 'Succès' : 'Erreur'}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;
    
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Animation de chargement
function showSpinner() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-backdrop';
    spinner.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
    document.body.appendChild(spinner);
}

function hideSpinner() {
    const spinner = document.querySelector('.spinner-backdrop');
    if (spinner) spinner.remove();
}

// Style pour le spinner
const style = document.createElement('style');
style.textContent = `
    .spinner-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
`;
document.head.appendChild(style);

// Formatage de la date
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Fonctions utilitaires pour les tickets
const ticketUtils = {
    // Mise à jour du statut d'un ticket
    updateStatus: function(ticketId, newStatus) {
        fetch(`/tickets/update-status/${ticketId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mise à jour visuelle du statut
                const statusBadge = document.querySelector(`#ticket-${ticketId} .status-badge`);
                if (statusBadge) {
                    statusBadge.className = `badge status-${newStatus}`;
                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                }
            }
        });
    },

    // Formatage de la date
    formatDate: function(dateString) {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    }
};
