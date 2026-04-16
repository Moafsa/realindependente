/**
 * Tenant Registration Form Handler
 * Handles multi-step form navigation and subdomain validation
 */

(function() {
    'use strict';

    let currentStep = 1;
    const totalSteps = 4;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        setupSubdomainValidation();
        setupPlanSelection();
        setupFormNavigation();
        updateSummary();
    });

    /**
     * Initialize form state
     */
    function initializeForm() {
        // Check if there's a plan in URL
        const urlParams = new URLSearchParams(window.location.search);
        const planId = urlParams.get('plan');
        if (planId) {
            // Auto-select plan and go to step 3
            setTimeout(() => {
                const planCard = document.querySelector(`[data-plan-id="${planId}"]`);
                if (planCard) {
                    selectPlan(planId);
                    goToStep(3);
                }
            }, 100);
        }

        // Show first step
        showStep(1);
    }

    /**
     * Setup form navigation between steps
     */
    function setupFormNavigation() {
        // Next step buttons
        document.querySelectorAll('.next-step').forEach(button => {
            button.addEventListener('click', function() {
                const nextStep = parseInt(this.getAttribute('data-next'));
                if (validateCurrentStep()) {
                    goToStep(nextStep);
                }
            });
        });

        // Previous step buttons
        document.querySelectorAll('.prev-step').forEach(button => {
            button.addEventListener('click', function() {
                const prevStep = parseInt(this.getAttribute('data-prev'));
                goToStep(prevStep);
            });
        });
    }

    /**
     * Navigate to a specific step
     */
    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;

        // Hide all steps
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show target step
        const targetStep = document.querySelector(`.step-content[data-step="${step}"]`);
        if (targetStep) {
            targetStep.classList.remove('hidden');
        }

        // Update progress indicators
        updateProgressIndicators(step);

        // Update current step
        currentStep = step;

        // Update summary if on last step
        if (step === 4) {
            updateSummary();
        }
    }

    /**
     * Show a specific step
     */
    function showStep(step) {
        goToStep(step);
    }

    /**
     * Update progress indicators
     */
    function updateProgressIndicators(activeStep) {
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            const step = index + 1;
            if (step <= activeStep) {
                indicator.classList.remove('bg-gray-300', 'text-gray-600');
                indicator.classList.add('bg-blue-600', 'text-white');
            } else {
                indicator.classList.remove('bg-blue-600', 'text-white');
                indicator.classList.add('bg-gray-300', 'text-gray-600');
            }
        });

        document.querySelectorAll('.step-line').forEach((line, index) => {
            const step = index + 1;
            if (step < activeStep) {
                line.classList.remove('bg-gray-300');
                line.classList.add('bg-blue-600');
            } else {
                line.classList.remove('bg-blue-600');
                line.classList.add('bg-gray-300');
            }
        });
    }

    /**
     * Validate current step before proceeding
     */
    function validateCurrentStep() {
        const currentStepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
        if (!currentStepContent) return false;

        const inputs = currentStepContent.querySelectorAll('input[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }

            // Special validation for password confirmation
            if (input.id === 'admin_password_confirmation') {
                const password = document.getElementById('admin_password');
                if (password && input.value !== password.value) {
                    isValid = false;
                    input.classList.add('border-red-500');
                    showFieldError(input, 'As senhas não coincidem');
                } else {
                    input.classList.remove('border-red-500');
                    hideFieldError(input);
                }
            }
        });

        // Step 3 validation: plan must be selected
        if (currentStep === 3) {
            const planId = document.getElementById('plan_id').value;
            if (!planId) {
                isValid = false;
                alert('Por favor, selecione um plano antes de continuar.');
            }
        }

        return isValid;
    }

    /**
     * Setup subdomain validation with AJAX
     */
    function setupSubdomainValidation() {
        const subdomainInput = document.getElementById('subdomain');
        if (!subdomainInput) return;

        let validationTimeout;

        subdomainInput.addEventListener('input', function() {
            const subdomain = this.value.trim().toLowerCase();
            const feedback = document.getElementById('subdomain-feedback');

            // Clear previous timeout
            clearTimeout(validationTimeout);

            // Basic validation
            if (!subdomain) {
                feedback.innerHTML = '';
                return;
            }

            // Check format
            if (!/^[a-z0-9-]+$/.test(subdomain)) {
                feedback.innerHTML = '<span class="text-red-600">Apenas letras minúsculas, números e hífens são permitidos</span>';
                subdomainInput.classList.add('border-red-500');
                return;
            }

            // Debounce AJAX call
            validationTimeout = setTimeout(() => {
                checkSubdomainAvailability(subdomain);
            }, 500);
        });
    }

    /**
     * Check subdomain availability via AJAX
     */
    function checkSubdomainAvailability(subdomain) {
        const subdomainInput = document.getElementById('subdomain');
        const feedback = document.getElementById('subdomain-feedback');

        // Show loading state
        feedback.innerHTML = '<span class="text-blue-600">Verificando disponibilidade...</span>';
        subdomainInput.classList.remove('border-red-500', 'border-green-500');

        // Make AJAX request
        fetch('/api/tenant/check-subdomain', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ subdomain: subdomain })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                feedback.innerHTML = '<span class="text-green-600">✓ Subdomínio disponível</span>';
                subdomainInput.classList.remove('border-red-500');
                subdomainInput.classList.add('border-green-500');
            } else {
                feedback.innerHTML = `<span class="text-red-600">✗ ${data.message || 'Subdomínio não disponível'}</span>`;
                subdomainInput.classList.remove('border-green-500');
                subdomainInput.classList.add('border-red-500');
            }
        })
        .catch(error => {
            console.error('Error checking subdomain:', error);
            feedback.innerHTML = '<span class="text-yellow-600">Erro ao verificar. Tente novamente.</span>';
        });
    }

    /**
     * Setup plan selection
     */
    function setupPlanSelection() {
        document.querySelectorAll('.plan-select-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const planId = this.getAttribute('data-plan-id');
                selectPlan(planId);
            });
        });

        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') {
                    const planId = this.getAttribute('data-plan-id');
                    selectPlan(planId);
                }
            });
        });
    }

    /**
     * Select a plan
     */
    function selectPlan(planId) {
        // Update hidden input
        document.getElementById('plan_id').value = planId;

        // Update visual selection
        document.querySelectorAll('.plan-card').forEach(card => {
            card.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500');
            card.classList.add('border-gray-200');
            
            const btn = card.querySelector('.plan-select-btn');
            if (btn) {
                btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            }
        });

        const selectedCard = document.querySelector(`[data-plan-id="${planId}"]`);
        if (selectedCard) {
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-blue-500', 'ring-2', 'ring-blue-500');
            
            const btn = selectedCard.querySelector('.plan-select-btn');
            if (btn) {
                btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                btn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            }
        }

        // Enable next button
        const nextButton = document.getElementById('next-to-payment');
        if (nextButton) {
            nextButton.disabled = false;
        }
    }

    /**
     * Update summary on step 4
     */
    function updateSummary() {
        const adminName = document.getElementById('admin_name')?.value || '-';
        const adminEmail = document.getElementById('admin_email')?.value || '-';
        const clubName = document.getElementById('club_name')?.value || '-';
        const subdomain = document.getElementById('subdomain')?.value || '-';
        const planId = document.getElementById('plan_id')?.value;

        document.getElementById('summary-admin-name').textContent = adminName;
        document.getElementById('summary-admin-email').textContent = adminEmail;
        document.getElementById('summary-club-name').textContent = clubName;
        document.getElementById('summary-subdomain').textContent = subdomain ? `${subdomain}.${getCentralDomain()}` : '-';

        if (planId) {
            const selectedCard = document.querySelector(`[data-plan-id="${planId}"]`);
            if (selectedCard) {
                const planName = selectedCard.querySelector('h3')?.textContent || '-';
                document.getElementById('summary-plan').textContent = planName;
            }
        }
    }

    /**
     * Get central domain from config
     */
    function getCentralDomain() {
        // This should match the domain from config/tenancy.php
        return 'meuclube.app'; // Default, should be dynamic
    }

    /**
     * Show field error message
     */
    function showFieldError(input, message) {
        let errorElement = input.parentElement.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'mt-1 text-sm text-red-600 field-error';
            input.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    /**
     * Hide field error message
     */
    function hideFieldError(input) {
        const errorElement = input.parentElement.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Watch for changes to update summary
    ['admin_name', 'admin_email', 'club_name', 'subdomain'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                if (currentStep === 4) {
                    updateSummary();
                }
            });
        }
    });
})();

