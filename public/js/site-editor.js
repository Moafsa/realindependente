/**
 * Site Editor with Real-time Preview
 */

(function() {
    'use strict';

    let previewTimeout = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview(); // Initial preview update
    });

    /**
     * Update preview in real-time
     */
    window.updatePreview = function() {
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
        
        // Detect active tab
        const activeTabBtn = document.querySelector('.tab-btn.text-blue-600');
        const activeTab = activeTabBtn ? activeTabBtn.id.replace('tab-', '') : 'home';

        let bannerTitle = '';
        let bannerSubtitle = '';
        let bannerPreviewId = '';

        // Mapping fields based on active tab
        switch(activeTab) {
            case 'home':
                bannerTitle = document.getElementById('hero_title')?.value || 'Bem-vindo';
                bannerSubtitle = document.getElementById('hero_subtitle')?.value || 'Descrição';
                bannerPreviewId = 'banner-preview';
                break;
            case 'sobre':
                bannerTitle = document.getElementById('about_title')?.value || 'Sobre Nós';
                bannerSubtitle = document.getElementById('about_subtitle')?.value || 'Nossa História';
                bannerPreviewId = 'banner-preview'; // About often shares hero banner or has site_hero_image
                break;
            case 'atletas':
                bannerTitle = document.getElementById('athletes_title')?.value || 'Atletas';
                bannerSubtitle = document.getElementById('athletes_subtitle')?.value || 'Nossos Talentos';
                bannerPreviewId = 'athletes-banner-preview';
                break;
            case 'equipes':
                bannerTitle = document.getElementById('teams_title')?.value || 'Equipes';
                bannerSubtitle = document.getElementById('teams_subtitle')?.value || 'Nossas Categorias';
                bannerPreviewId = 'teams-banner-preview';
                break;
            case 'loja':
                bannerTitle = document.getElementById('store_title')?.value || 'Loja';
                bannerSubtitle = document.getElementById('store_subtitle')?.value || 'Produtos Oficiais';
                bannerPreviewId = 'store-banner-preview';
                break;
            default:
                bannerTitle = document.getElementById('hero_title')?.value || 'Clube';
                bannerSubtitle = document.getElementById('hero_subtitle')?.value || '';
        }

        // Apply CSS variables
        const preview = document.getElementById('live-preview');
        if (preview) {
            preview.style.setProperty('--preview-primary', primaryColor);
            preview.style.setProperty('--preview-secondary', secondaryColor);
        }

        // Update preview hero background
        const previewHero = document.getElementById('preview-hero');
        if (previewHero) {
            let bannerSrc = null;
            const bannerPreview = document.getElementById(bannerPreviewId);
            
            if (bannerPreview) {
                if (bannerPreview.tagName === 'IMG') {
                    bannerSrc = bannerPreview.src;
                } else {
                    bannerSrc = bannerPreview.querySelector('img')?.src;
                }
            }
            
            if (bannerSrc && bannerSrc !== '' && !bannerSrc.includes('placeholder')) {
                previewHero.style.background = `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url(${bannerSrc})`;
                previewHero.style.backgroundSize = 'cover';
                previewHero.style.backgroundPosition = 'center';
            } else {
                previewHero.style.background = `linear-gradient(to right, ${primaryColor}, ${secondaryColor})`;
            }
        }

        // Update preview site name and logo
        const previewSiteName = document.getElementById('preview-site-name');
        if (previewSiteName) {
            previewSiteName.textContent = siteName;
            previewSiteName.style.color = primaryColor;
        }

        const previewLogoContainer = document.getElementById('preview-logo');
        if (previewLogoContainer) {
            const logoPreview = document.getElementById('logo-preview');
            let logoSrc = null;

            if (logoPreview) {
                if (logoPreview.tagName === 'IMG') {
                    logoSrc = logoPreview.src;
                } else {
                    logoSrc = logoPreview.querySelector('img')?.src;
                }
            }
            
            if (logoSrc && logoSrc !== '' && !logoSrc.includes('placeholder')) {
                previewLogoContainer.innerHTML = `<img src="${logoSrc}" class="h-full w-full object-contain">`;
                previewLogoContainer.classList.remove('bg-gray-200');
            }
        }

        const previewBannerTitle = document.getElementById('preview-banner-title');
        if (previewBannerTitle) {
            previewBannerTitle.textContent = bannerTitle;
        }

        const previewBannerSubtitle = document.getElementById('preview-banner-subtitle');
        if (previewBannerSubtitle) {
            previewBannerSubtitle.textContent = bannerSubtitle;
        }

        // Update stat cards colors
        const statCards = preview?.querySelectorAll('.grid > div');
        statCards?.forEach((card, index) => {
            const color = index % 2 === 0 ? primaryColor : secondaryColor;
            card.style.backgroundColor = color + '1A'; // 10% opacity
            card.style.borderColor = color + '33'; // 20% opacity
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
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                img.className = 'h-full w-full object-contain rounded';
                preview.innerHTML = '';
                preview.appendChild(img);
                preview.classList.remove('bg-gray-100', 'text-gray-400');
            }
            updatePreview();
        };
        reader.readAsDataURL(file);
    };

    /**
     * Refresh preview
     */
    window.refreshPreview = function() {
        updatePreview();
    };

    // Initial preview update
    setTimeout(() => {
        updatePreview();
    }, 100);
})();
