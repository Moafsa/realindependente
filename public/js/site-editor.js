/**
 * Site Editor with Real-time Preview
 */

(function() {
    'use strict';

    let previewTimeout = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeColorSync();
        updatePreview(); // Initial preview update
    });

    /**
     * Update preview in real-time
     */
    function updatePreview() {
        // Debounce preview updates
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(() => {
            applyPreviewStyles();
        }, 300);
    }

    /**
     * Apply styles to live preview
     */
    function applyPreviewStyles() {
        const primaryColor = document.getElementById('color_primary')?.value || '#2563eb';
        const secondaryColor = document.getElementById('color_secondary')?.value || '#16a34a';
        const siteName = document.getElementById('site_name')?.value || 'Nome do Clube';
        const siteDescription = document.getElementById('site_description')?.value || 'Descrição do clube';
        const bannerTitle = document.getElementById('banner_title')?.value || 'Bem-vindo ao Nosso Clube';
        const bannerSubtitle = document.getElementById('banner_subtitle')?.value || 'Descrição do clube';
        const contactPhone = document.getElementById('contact_phone')?.value || '(00) 0000-0000';
        const contactEmail = document.getElementById('contact_email')?.value || 'contato@clube.com';
        const contactAddress = document.getElementById('contact_address')?.value || 'Endereço do clube';

        // Apply CSS variables
        const preview = document.getElementById('live-preview');
        if (preview) {
            preview.style.setProperty('--preview-primary', primaryColor);
            preview.style.setProperty('--preview-secondary', secondaryColor);
        }

        // Update preview hero background
        const previewHero = document.getElementById('preview-hero');
        if (previewHero) {
            previewHero.style.background = `linear-gradient(to right, ${primaryColor}, ${secondaryColor})`;
        }

        // Update text content
        const previewSiteName = document.getElementById('preview-site-name');
        if (previewSiteName) {
            previewSiteName.textContent = siteName;
            previewSiteName.style.color = primaryColor;
        }

        const previewDescription = document.getElementById('preview-description');
        if (previewDescription) {
            previewDescription.textContent = siteDescription;
        }

        const previewBannerTitle = document.getElementById('preview-banner-title');
        if (previewBannerTitle) {
            previewBannerTitle.textContent = bannerTitle;
        }

        const previewBannerSubtitle = document.getElementById('preview-banner-subtitle');
        if (previewBannerSubtitle) {
            previewBannerSubtitle.textContent = bannerSubtitle;
        }

        const previewPhone = document.getElementById('preview-phone');
        if (previewPhone) {
            previewPhone.textContent = `📞 ${contactPhone}`;
        }

        const previewEmail = document.getElementById('preview-email');
        if (previewEmail) {
            previewEmail.textContent = `✉️ ${contactEmail}`;
        }

        const previewAddress = document.getElementById('preview-address');
        if (previewAddress) {
            previewAddress.textContent = `📍 ${contactAddress}`;
        }

        // Update stat cards colors
        const statCards = preview?.querySelectorAll('[style*="background-color"]');
        statCards?.forEach((card, index) => {
            const color = index === 0 ? primaryColor : secondaryColor;
            card.style.backgroundColor = color + '1A'; // 10% opacity
            const number = card.querySelector('.text-2xl');
            if (number) {
                number.style.color = color;
            }
        });
    }

    /**
     * Handle image upload and preview
     */
    window.handleImageUpload = function(input, previewId) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-full w-full object-contain rounded">`;
                preview.classList.remove('bg-gray-100', 'text-gray-400');
            }
            updatePreview();
        };
        reader.readAsDataURL(file);
    };

    /**
     * Save settings
     */
    window.saveSettings = function() {
        const form = document.getElementById('settings-form');
        const formFields = document.getElementById('form-fields');
        
        // Clear previous fields
        formFields.innerHTML = '';

        // Collect all settings
        const settings = {};
        document.querySelectorAll('[name^="settings["]').forEach(input => {
            const key = input.name.match(/settings\[(.+)\]/)[1];
            
            if (input.type === 'file') {
                if (input.files.length > 0) {
                    // File will be handled by form submission
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.name = input.name;
                    fileInput.files = input.files;
                    formFields.appendChild(fileInput);
                }
            } else if (input.type === 'checkbox') {
                settings[key] = input.checked ? '1' : '0';
            } else {
                settings[key] = input.value;
            }
        });

        // Add text settings as hidden inputs
        Object.keys(settings).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `settings[${key}]`;
            input.value = settings[key];
            formFields.appendChild(input);
        });

        // Submit form
        form.submit();
    };

    /**
     * Refresh preview
     */
    window.refreshPreview = function() {
        updatePreview();
    };

    /**
     * Initialize color input sync
     */
    function initializeColorSync() {
        const colorInputs = document.querySelectorAll('input[type="color"]');
        colorInputs.forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;
            if (textInput && textInput.tagName === 'INPUT') {
                // Sync color to text
                colorInput.addEventListener('input', function() {
                    textInput.value = this.value;
                    updatePreview();
                });

                // Sync text to color
                textInput.addEventListener('input', function() {
                    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                        colorInput.value = this.value;
                        updatePreview();
                    }
                });
            }
        });
    }

    // Initial preview update
    setTimeout(() => {
        updatePreview();
    }, 100);
})();

