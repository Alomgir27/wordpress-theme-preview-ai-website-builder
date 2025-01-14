/**
 * Theme Preview Image Handler
 * Handles image uploads and replacements
 */

class ThemePreviewImageHandler {
    constructor() {
        this.settings = window.themePreviewSettings || {};
        this.userId = this.getUserId();
        this.init();
        // Make the instance globally accessible
        window.themePreviewHandler = this;
    }

    init() {
        this.addImageActionButtons();
        this.observeContentChanges();
        this.recoverStoredImages();
        this.recoverStoredContent();
        this.createRightPanel();
    }

    getUserId() {
        let userId = localStorage.getItem('theme_preview_user_id');
        if (!userId) {
            userId = 'user_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            localStorage.setItem('theme_preview_user_id', userId);
        }
        return userId;
    }

    generateImageId(imageElement) {
        // Create a unique ID based on the image's src and other attributes
        const src = imageElement.src;
        const alt = imageElement.alt || '';
        const className = imageElement.className || '';
        const path = src.split('?')[0]; // Remove query parameters
        
        // Create a string that uniquely identifies this image
        const idString = `${path}_${alt}_${className}`;
        
        // Create a hash of the string
        let hash = 0;
        for (let i = 0; i < idString.length; i++) {
            const char = idString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32-bit integer
        }
        
        return `img_${Math.abs(hash)}`;
    }

    async handleImageUpload(file, imageElement) {
        try {
            // Validate settings
            if (!this.settings?.cloudName || !this.settings?.uploadPreset) {
                throw new Error('Cloudinary settings are missing');
            }

            // Show loading state
            this.showToast('Uploading image...', 'info');
            imageElement.style.opacity = '0.5';

            // Upload directly to Cloudinary
            const formData = new FormData();
            formData.append('file', file);
            formData.append('upload_preset', this.settings.uploadPreset);
            formData.append('folder', `theme_preview/${this.settings.themeName}`);

            const cloudinaryResponse = await fetch(
                `https://api.cloudinary.com/v1_1/${this.settings.cloudName}/image/upload`,
                {
                    method: 'POST',
                    body: formData
                }
            );

            if (!cloudinaryResponse.ok) {
                const errorText = await cloudinaryResponse.text();
                console.error('Cloudinary upload failed:', errorText);
                throw new Error('Failed to upload image');
            }

            const result = await cloudinaryResponse.json();

            // Store replacement data
            const imageId = this.generateImageId(imageElement);
            const replacementData = {
                originalSrc: imageElement.getAttribute('data-original-src') || imageElement.src,
                currentSrc: result.secure_url,
                publicId: result.public_id,
                timestamp: new Date().toISOString(),
                userId: this.userId
            };

            // Save to local storage
            localStorage.setItem(`replaced_image_${imageId}`, JSON.stringify(replacementData));

            // Update image
            if (!imageElement.hasAttribute('data-original-src')) {
                imageElement.setAttribute('data-original-src', imageElement.src);
            }
            imageElement.src = result.secure_url;
            imageElement.setAttribute('data-is-replaced', 'true');
            imageElement.setAttribute('data-image-id', imageId);

            // Show success message
            this.showToast('Image replaced successfully', 'success');
            imageElement.style.opacity = '1';

            return result;

        } catch (error) {
            console.error('Upload error:', error);
            this.showToast(error.message || 'Failed to upload image', 'error');
            imageElement.style.opacity = '1';
            throw error;
        }
    }

    addImageActionButtons() {
        document.querySelectorAll('img').forEach(img => {
            if (!this.shouldProcessImage(img)) return;
            this.addActionButton(img);
        });
    }

    shouldProcessImage(img) {
        return !img.hasAttribute('data-has-actions') && 
               !img.closest('#wpadminbar, .theme-preview-toast');
    }

    addActionButton(img) {
        img.setAttribute('data-has-actions', 'true');
        const wrapper = this.ensureWrapper(img);

        const actionsContainer = document.createElement('div');
        actionsContainer.className = 'image-actions-container';
        actionsContainer.style.cssText = `
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 999;
        `;

        const buttonStyle = `
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 4px;
            padding: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #1e1e1e;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
            white-space: nowrap;
        `;

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';

        const buttons = [
            {
                name: 'Replace Image',
                icon: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>',
                onClick: () => fileInput.click()
            },
            {
                name: 'Copy URL',
                icon: '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
                onClick: () => {
                    navigator.clipboard.writeText(img.src)
                        .then(() => this.showToast('Image URL copied to clipboard!'))
                        .catch(() => this.showToast('Failed to copy URL'));
                }
            },
            {
                name: 'Preview',
                icon: '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
                onClick: () => this.showImagePreview(img)
            },
            {
                name: 'Download',
                icon: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
                onClick: () => {
                    const link = document.createElement('a');
                    link.href = img.src;
                    link.download = img.src.split('/').pop() || 'image';
                    link.click();
                }
            }
        ];

        // Add "View Original" button if image is replaced
        if (img.hasAttribute('data-is-replaced')) {
            buttons.push({
                name: 'View Original',
                icon: '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
                onClick: () => this.showImageComparison(img)
            });
        }

        buttons.forEach(({name, icon, onClick}) => {
            const button = document.createElement('button');
            button.className = `image-action-button ${name.toLowerCase().replace(' ', '-')}-button`;
            button.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    ${icon}
                </svg>
                ${name}
            `;
            button.style.cssText = buttonStyle;
            button.addEventListener('click', onClick);
            actionsContainer.appendChild(button);
        });

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (file) {
                try {
                    await this.handleImageUpload(file, img);
                    // Refresh action buttons to show "View Original" if needed
                    this.addActionButton(img);
                } catch (error) {
                    console.error('Upload failed:', error);
                }
            }
        });

        actionsContainer.appendChild(fileInput);
        wrapper.appendChild(actionsContainer);

        wrapper.addEventListener('mouseenter', () => {
            actionsContainer.style.opacity = '1';
            actionsContainer.style.visibility = 'visible';
        });
        wrapper.addEventListener('mouseleave', () => {
            actionsContainer.style.opacity = '0';
            actionsContainer.style.visibility = 'hidden';
        });
    }

    showImageComparison(imageElement) {
        const originalSrc = imageElement.getAttribute('data-original-src');
        const currentSrc = imageElement.src;

        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        `;

        const container = document.createElement('div');
        container.style.cssText = `
            display: flex;
            gap: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 90vw;
            max-height: 90vh;
        `;

        const createImageContainer = (src, label, isOriginal = false) => {
            const div = document.createElement('div');
            div.style.cssText = `
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                cursor: ${isOriginal ? 'pointer' : 'default'};
                padding: 10px;
                border-radius: 8px;
                transition: background-color 0.2s;
            `;

            if (isOriginal) {
                div.title = 'Click to restore original image';
                div.addEventListener('mouseenter', () => {
                    div.style.backgroundColor = 'rgba(0, 0, 0, 0.05)';
                });
                div.addEventListener('mouseleave', () => {
                    div.style.backgroundColor = 'transparent';
                });
                div.addEventListener('click', () => {
                    this.resetImage(imageElement);
                    modal.remove();
                });
            }

            const img = document.createElement('img');
            img.src = src;
            img.style.cssText = `
                max-width: 400px;
                max-height: 70vh;
                object-fit: contain;
            `;

            const text = document.createElement('p');
            text.textContent = label + (isOriginal ? ' (Click to restore)' : '');
            text.style.cssText = `
                margin: 0;
                font-weight: bold;
                color: ${isOriginal ? '#2196F3' : 'inherit'};
            `;

            div.appendChild(img);
            div.appendChild(text);
            return div;
        };

        container.appendChild(createImageContainer(originalSrc, 'Original', true));
        container.appendChild(createImageContainer(currentSrc, 'Current'));

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        closeBtn.onclick = () => modal.remove();

        modal.appendChild(container);
        modal.appendChild(closeBtn);
        document.body.appendChild(modal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    }

    resetImage(imageElement) {
        const imageId = imageElement.getAttribute('data-image-id');
        const originalSrc = imageElement.getAttribute('data-original-src');
        
        // Remove from local storage
        localStorage.removeItem(`replaced_image_${imageId}`);
        
        // Reset image attributes
        imageElement.src = originalSrc;
        imageElement.removeAttribute('data-is-replaced');
        imageElement.removeAttribute('data-image-id');
        imageElement.removeAttribute('data-original-src');
        
        // Re-add action buttons without "View Original"
        this.addActionButton(imageElement);
        
        // Show success message
        this.showToast('Image restored to original', 'success');
    }

    ensureWrapper(img) {
        let wrapper = img.parentElement;
        if (!wrapper.style.position || wrapper.style.position === 'static') {
            wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.display = 'inline-block';
            img.parentNode.insertBefore(wrapper, img);
            wrapper.appendChild(img);
        }
        return wrapper;
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `theme-preview-toast ${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    recoverStoredImages() {
        document.querySelectorAll('img').forEach(img => {
            const imageId = this.generateImageId(img);
            const storedData = localStorage.getItem(`replaced_image_${imageId}`);
            
            if (storedData) {
                const data = JSON.parse(storedData);
                if (data.userId === this.userId) {
                    if (!img.hasAttribute('data-original-src')) {
                        img.setAttribute('data-original-src', data.originalSrc);
                    }
                    img.src = data.currentSrc;
                    img.setAttribute('data-is-replaced', 'true');
                    img.setAttribute('data-image-id', imageId);
                    // Re-add action buttons to include "View Original"
                    this.addActionButton(img);
                }
            }
        });
    }

    observeContentChanges() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeName === 'IMG') {
                            const imageId = this.generateImageId(node);
                            const storedData = localStorage.getItem(`replaced_image_${imageId}`);
                            if (storedData) {
                                const data = JSON.parse(storedData);
                                if (data.userId === this.userId) {
                                    node.setAttribute('data-original-src', data.originalSrc);
                                    node.src = data.currentSrc;
                                    node.setAttribute('data-is-replaced', 'true');
                                    node.setAttribute('data-image-id', imageId);
                                    this.addActionButton(node);
                                }
                            }
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // OpenAI content generation methods
    addLoadingState(element) {
        // Save original content
        const originalContent = element.innerHTML;
        element.setAttribute('data-original-content', originalContent);
        
        // Add loading class
        element.classList.add('theme-preview-loading');
        
        // Create and add loading spinner
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.style.cssText = `
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-left: 10px;
            vertical-align: middle;
        `;
        element.appendChild(spinner);
        
        // Return function to remove loading state
        return () => {
            element.classList.remove('theme-preview-loading');
            const loadingSpinner = element.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.remove();
            }
        };
    }

    async generateContent(business, element) {
        let removeLoader;
        if (element) {
            removeLoader = this.addLoadingState(element);
        }
        
        try {
            if (!this.settings?.openaiApiKey) {
                throw new Error('OpenAI API key is missing');
            }

            const systemPrompt = {
                role: "system",
                content: `You are an AI that creates the content for a WordPress webpage.
                    The webpage content should be exciting and showcase great confidence in your business. 
                    You are a specialist in your field.
                    The business for which the webpage is created is: ${business}.
                    Follow the user's flow to craft each piece of text for the website.
                    Ensure the content is cohesive, interrelated, and not isolated.
                    Prioritize quality above all. The text must be high-quality, not generic, and easy to understand. 
                    Keep sentences under 150 characters. Use a conversational tone with simple language, 
                    suitable for a third-grade student, and minimize academic jargon.`
            };

            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.settings.openaiApiKey}`
                },
                body: JSON.stringify({
                    model: "gpt-3.5-turbo",
                    messages: [systemPrompt],
                    temperature: 0.7
                })
            });

            if (!response.ok) {
                throw new Error('Failed to generate content');
            }

            const result = await response.json();
            const content = result.choices[0].message.content;
            
            if (element) {
                element.innerHTML = content;
            }
            
            return content;
        } catch (error) {
            console.error('Content generation error:', error);
            this.showToast('Failed to generate content', 'error');
            if (element) {
                // Restore original content on error
                element.innerHTML = element.getAttribute('data-original-content');
            }
            throw error;
        } finally {
            if (removeLoader) {
                removeLoader();
            }
        }
    }

    async generateTaglines(business, count = 10, element) {
        let removeLoader;
        if (element) {
            removeLoader = this.addLoadingState(element);
        }
        
        try {
            if (!this.settings?.openaiApiKey) {
                throw new Error('OpenAI API key is missing');
            }

            const systemPrompt = {
                role: "system",
                content: `You are a tool that generates taglines for websites. 
                    Your job is to create simple, short, SEO-friendly taglines. 
                    Each tagline should be unique and straightforward. 
                    The length of each tagline should be about 50 characters, 
                    and each should be one phrase. 
                    Return the results as a JSON array without any additional information.`
            };

            const userPrompt = {
                role: "user",
                content: `Create ${count} examples of taglines for the business: ${business}`
            };

            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.settings.openaiApiKey}`
                },
                body: JSON.stringify({
                    model: "gpt-3.5-turbo",
                    messages: [systemPrompt, userPrompt],
                    temperature: 0.8
                })
            });

            if (!response.ok) {
                throw new Error('Failed to generate taglines');
            }

            const result = await response.json();
            const taglines = JSON.parse(result.choices[0].message.content);
            
            if (element) {
                element.innerHTML = taglines.join('<br>');
            }
            
            return taglines;
        } catch (error) {
            console.error('Tagline generation error:', error);
            this.showToast('Failed to generate taglines', 'error');
            if (element) {
                // Restore original content on error
                element.innerHTML = element.getAttribute('data-original-content');
            }
            throw error;
        } finally {
            if (removeLoader) {
                removeLoader();
            }
        }
    }

    async generateSingleTagline(business) {
        try {
            const taglines = await this.generateTaglines(business, 1);
            return taglines[0];
        } catch (error) {
            console.error('Single tagline generation error:', error);
            this.showToast('Failed to generate tagline', 'error');
            throw error;
        }
    }

    showImagePreview(imageElement) {
        const modal = document.createElement('div');
        modal.className = 'image-preview-modal';
        
        const img = document.createElement('img');
        img.src = imageElement.src;
        img.alt = imageElement.alt;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        `;
        
        modal.appendChild(img);
        modal.appendChild(closeBtn);
        document.body.appendChild(modal);
        
        const closeModal = () => modal.remove();
        closeBtn.onclick = closeModal;
        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        };
        
        // Enable keyboard navigation
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
            }
        });
    }

    createRightPanel() {
        const panel = document.createElement('div');
        panel.className = 'theme-preview-right-panel';
        panel.style.cssText = `
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 9999;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            &:hover {
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            }
        `;
        
        const buttons = [
            {
                name: 'Copy Template',
                icon: '<path d="M20 9h-9a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
                onClick: () => this.copyEntireTemplate()
            },
            {
                name: 'Copy Blocks',
                icon: '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
                onClick: () => this.copyBlocks()
            },
            {
                name: 'Theme Info',
                icon: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="8"/>',
                onClick: () => this.showThemeInfo()
            },
            {
                name: 'Prompt',
                icon: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
                onClick: () => this.showPromptModal()
            },
            {
                name: 'Generate',
                icon: '<path d="M12 3v19M5 12h14M15 5l-3-3-3 3M15 21l-3 3-3-3"/>',
                onClick: () => this.showContentGenerator()
            }
        ];

        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'theme-preview-panel-toggle';
        toggleBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13 17l5-5-5-5M6 17l5-5-5-5"/>
            </svg>
        `;
        toggleBtn.style.cssText = `
            position: absolute;
            left: -36px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            border-radius: 10px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            &:hover {
                transform: translateY(-50%) scale(1.1);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            }
        `;

        buttons.forEach(({name, icon, onClick}) => {
            const button = document.createElement('button');
            button.className = 'theme-preview-panel-button';
            button.style.cssText = `
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                padding: 10px 12px;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                transition: all 0.2s;
                min-width: 140px;
                &:hover {
                    background: #f8f9fa;
                    transform: translateX(-5px);
                    box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.1);
                    border-color: #2196F3;
                }
            `;
            
            button.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    ${icon}
                </svg>
                <span style="font-size: 13px; font-weight: 500;">${name}</span>
            `;
            
            button.addEventListener('click', onClick);
            panel.appendChild(button);
        });

        panel.appendChild(toggleBtn);
        document.body.appendChild(panel);

        toggleBtn.addEventListener('click', () => {
            if (panel.classList.contains('collapsed')) {
                panel.classList.remove('collapsed');
                panel.style.transform = 'translateY(-50%)';
                toggleBtn.style.transform = 'translateY(-50%)';
            } else {
                panel.classList.add('collapsed');
                panel.style.transform = 'translateY(-50%) translateX(calc(100% + 40px))';
                toggleBtn.style.transform = 'translateY(-50%) rotate(180deg)';
            }
        });
    }

    copyEntireTemplate() {
        const content = document.documentElement.outerHTML;
        navigator.clipboard.writeText(content)
            .then(() => this.showToast('Template copied to clipboard!', 'success'))
            .catch(() => this.showToast('Failed to copy template', 'error'));
    }

    copyBlocks() {
        const blocks = document.querySelectorAll('.wp-block');
        const blocksHtml = Array.from(blocks).map(block => block.outerHTML).join('\n');
        navigator.clipboard.writeText(blocksHtml)
            .then(() => this.showToast('Blocks copied to clipboard!', 'success'))
            .catch(() => this.showToast('Failed to copy blocks', 'error'));
    }

    showThemeInfo() {
        const modal = document.createElement('div');
        modal.className = 'theme-preview-modal';
        modal.innerHTML = `
            <div class="theme-preview-modal-content">
                <h2>Theme Information</h2>
                <div class="theme-info-grid">
                    <div>Theme Name:</div><div>${this.settings.themeName || 'N/A'}</div>
                    <div>Version:</div><div>${this.settings.themeVersion || 'N/A'}</div>
                    <div>Author:</div><div>${this.settings.themeAuthor || 'N/A'}</div>
                    <div>Description:</div><div>${this.settings.themeDescription || 'N/A'}</div>
                </div>
                <button class="theme-preview-modal-close">×</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.theme-preview-modal-close').onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
    }

    showPromptModal(element = null, initialPrompt = '') {
        const modal = document.createElement('div');
        modal.className = 'theme-preview-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 20px;
        `;
        
        // Get the existing content if element is provided
        const existingContent = element ? element.textContent.trim() : '';
        
        // Declare promptSection at the beginning
        let promptSection = null;
        
        const modalContent = document.createElement('div');
        modalContent.className = 'theme-preview-modal-content';
        modalContent.style.cssText = `
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 500px;
            max-height: 400px;
            display: flex;
            flex-direction: column;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        `;

        const modalHeader = document.createElement('div');
        modalHeader.style.cssText = `
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        `;

        const title = document.createElement('h2');
        title.textContent = initialPrompt ? 'Generate Content' : 'Edit Content';
        title.style.cssText = `
            margin: 0;
            font-size: 1.25rem;
            color: #333;
            font-weight: 600;
        `;

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.2s;
            &:hover {
                background: #f5f5f5;
            }
        `;

        const editorArea = document.createElement('div');
        editorArea.style.cssText = `
            flex: 1;
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #ccc transparent;
            &::-webkit-scrollbar {
                width: 6px;
            }
            &::-webkit-scrollbar-track {
                background: transparent;
            }
            &::-webkit-scrollbar-thumb {
                background-color: #ccc;
                border-radius: 3px;
            }
        `;

        // Content section
        const contentSection = document.createElement('div');
        contentSection.style.cssText = `
            flex: ${initialPrompt ? '1' : '2'};
            display: flex;
            flex-direction: column;
        `;

        const contentLabel = document.createElement('label');
        contentLabel.textContent = 'Content';
        contentLabel.style.cssText = `
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
        `;

        const contentTextarea = document.createElement('textarea');
        contentTextarea.value = existingContent;
        contentTextarea.placeholder = 'Enter your content here...';
        contentTextarea.style.cssText = `
            width: 100%;
            height: ${initialPrompt ? '80px' : '120px'};
            min-height: ${initialPrompt ? '80px' : '120px'};
            max-height: ${initialPrompt ? '120px' : '200px'};
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px;
            font-size: 13px;
            line-height: 1.4;
            resize: none;
            outline: none;
            font-family: inherit;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #ccc transparent;
            &::-webkit-scrollbar {
                width: 6px;
            }
            &::-webkit-scrollbar-track {
                background: transparent;
            }
            &::-webkit-scrollbar-thumb {
                background-color: #ccc;
                border-radius: 3px;
            }
            &:focus {
                border-color: #2196F3;
                box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
            }
        `;

        // Prompt section
        if (initialPrompt) {
            promptSection = document.createElement('div');
            promptSection.style.cssText = `
                flex: 1;
                display: flex;
                flex-direction: column;
            `;

            const promptLabel = document.createElement('label');
            promptLabel.textContent = 'Prompt';
            promptLabel.style.cssText = `
                font-weight: 500;
                margin-bottom: 6px;
                color: #333;
                font-size: 14px;
            `;

            const promptTextarea = document.createElement('textarea');
            promptTextarea.value = initialPrompt;
            promptTextarea.placeholder = 'Enter your prompt here...';
            promptTextarea.style.cssText = `
                width: 100%;
                height: 60px;
                min-height: 60px;
                max-height: 100px;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 10px;
                font-size: 13px;
                line-height: 1.4;
                resize: none;
                outline: none;
                font-family: inherit;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: #ccc transparent;
                &::-webkit-scrollbar {
                    width: 6px;
                }
                &::-webkit-scrollbar-track {
                    background: transparent;
                }
                &::-webkit-scrollbar-thumb {
                    background-color: #ccc;
                    border-radius: 3px;
                }
                &:focus {
                    border-color: #2196F3;
                    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                }
            `;

            promptSection.appendChild(promptLabel);
            promptSection.appendChild(promptTextarea);
        }

        const buttonGroup = document.createElement('div');
        buttonGroup.style.cssText = `
            padding: 16px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        `;

        const createButton = (text, isPrimary = false) => {
            const btn = document.createElement('button');
            btn.textContent = text;
            btn.style.cssText = `
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                ${isPrimary ? `
                    background: #2196F3;
                    color: white;
                    border: none;
                    &:hover {
                        background: #1976D2;
                    }
                ` : `
                    background: white;
                    color: #333;
                    border: 1px solid #ddd;
                    &:hover {
                        background: #f5f5f5;
                    }
                `}
            `;
            return btn;
        };

        const primaryBtn = createButton(initialPrompt ? 'Generate' : 'Save', true);
        const cancelBtn = createButton('Cancel');

        // Add style for animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes modalSlideIn {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);

        // Assemble the modal
        modalHeader.appendChild(title);
        modalHeader.appendChild(closeBtn);
        
        contentSection.appendChild(contentLabel);
        contentSection.appendChild(contentTextarea);
        editorArea.appendChild(contentSection);
        
        if (promptSection) {
            editorArea.appendChild(promptSection);
        }
        
        buttonGroup.appendChild(cancelBtn);
        buttonGroup.appendChild(primaryBtn);

        modalContent.appendChild(modalHeader);
        modalContent.appendChild(editorArea);
        modalContent.appendChild(buttonGroup);
        modal.appendChild(modalContent);
        
        document.body.appendChild(modal);
        
        // Set cursor at the end of the content textarea
        contentTextarea.focus();
        contentTextarea.setSelectionRange(contentTextarea.value.length, contentTextarea.value.length);
        
        // Event handlers
        primaryBtn.onclick = async () => {
            if (initialPrompt) {
                // Generate mode
                const prompt = promptSection.querySelector('textarea').value.trim();
                const content = contentTextarea.value.trim();
                if (prompt && content) {
                    modal.remove();
                    style.remove();
                    await this.generateContent(prompt, element || document.querySelector('.entry-content'));
                    if (element) {
                        this.saveContentToStorage(element);
                    }
                }
            } else {
                // Edit mode
                const newContent = contentTextarea.value.trim();
                if (newContent && element) {
                    element.textContent = newContent;
                    this.saveContentToStorage(element);
                    this.showToast('Content saved successfully', 'success');
                }
                modal.remove();
                style.remove();
            }
        };
        
        const closeModal = () => {
            modal.remove();
            style.remove();
        };

        cancelBtn.onclick = closeModal;
        closeBtn.onclick = closeModal;
        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        };

        // Add keyboard shortcuts
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
            }
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                primaryBtn.click();
                document.removeEventListener('keydown', escHandler);
            }
        });
    }

    showContentGenerator() {
        const modal = document.createElement('div');
        modal.className = 'theme-preview-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        `;

        const content = document.createElement('div');
        content.className = 'theme-preview-modal-content';
        content.style.cssText = `
            background: white;
            border-radius: 16px;
            width: 400px;
            padding: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;

        content.innerHTML = `
            <h2 style="margin: 0 0 20px; font-size: 1.5rem; color: #333;">Generate Content</h2>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Business Name</label>
                <input type="text" placeholder="Enter business name" style="
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    &:focus {
                        outline: none;
                        border-color: #2196F3;
                        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                    }
                ">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Content Type</label>
                <select style="
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    background: white;
                    cursor: pointer;
                    &:focus {
                        outline: none;
                        border-color: #2196F3;
                        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                    }
                ">
                    <option value="full">Full Content</option>
                    <option value="tagline">Tagline Only</option>
                    <option value="description">Description Only</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button class="cancel-btn" style="
                    padding: 10px 20px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    background: white;
                    color: #333;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    &:hover {
                        background: #f5f5f5;
                    }
                ">Cancel</button>
                <button class="generate-btn" style="
                    padding: 10px 20px;
                    border: none;
                    border-radius: 8px;
                    background: #2196F3;
                    color: white;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    &:hover {
                        background: #1976D2;
                    }
                ">Generate</button>
            </div>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);

        const businessInput = content.querySelector('input');
        const typeSelect = content.querySelector('select');
        const generateBtn = content.querySelector('.generate-btn');
        const cancelBtn = content.querySelector('.cancel-btn');

        generateBtn.onclick = async () => {
            const business = businessInput.value.trim();
            const type = typeSelect.value;
            
            if (business) {
                modal.remove();
                const contentArea = document.querySelector('.entry-content');
                
                switch (type) {
                    case 'full':
                        await this.generateContent(business, contentArea);
                        break;
                    case 'tagline':
                        const tagline = await this.generateSingleTagline(business);
                        if (tagline) {
                            const taglineElement = document.querySelector('.site-description') || document.createElement('div');
                            taglineElement.textContent = tagline;
                        }
                        break;
                    case 'description':
                        await this.generateContent(business, contentArea, true);
                        break;
                }
            }
        };

        cancelBtn.onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
    }

    generateContentId(element) {
        // Create a unique ID based on the element's content and position
        const tagName = element.tagName.toLowerCase();
        const text = element.textContent.trim();
        const path = this.getElementPath(element);
        
        // Create a string that uniquely identifies this content
        const idString = `${path}_${tagName}_${text.substring(0, 50)}`; // Use first 50 chars for ID
        
        // Create a hash of the string
        let hash = 0;
        for (let i = 0; i < idString.length; i++) {
            const char = idString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        
        return `content_${Math.abs(hash)}`;
    }

    getElementPath(element) {
        const path = [];
        let current = element;
        
        while (current && current !== document.body) {
            let selector = current.tagName.toLowerCase();
            if (current.id) {
                selector += `#${current.id}`;
            } else {
                let nth = 1;
                let sibling = current.previousElementSibling;
                
                while (sibling) {
                    if (sibling.tagName === current.tagName) nth++;
                    sibling = sibling.previousElementSibling;
                }
                
                if (nth > 1) selector += `:nth-of-type(${nth})`;
            }
            
            path.unshift(selector);
            current = current.parentElement;
        }
        
        return path.join(' > ');
    }

    saveContentToStorage(element) {
        const contentId = this.generateContentId(element);
        const data = {
            content: element.textContent,
            timestamp: new Date().toISOString(),
            userId: this.userId,
            path: this.getElementPath(element)
        };
        localStorage.setItem(`edited_content_${contentId}`, JSON.stringify(data));
        
        // Add restore button if not already present
        if (!element.nextElementSibling?.classList.contains('restore-content-btn')) {
            const restoreBtn = document.createElement('button');
            restoreBtn.className = 'restore-content-btn';
            restoreBtn.textContent = 'Restore Original';
            restoreBtn.style.cssText = `
                margin-left: 10px;
                padding: 8px 16px;
                background: #f5f5f5;
                border: 1px solid #ddd;
                border-radius: 6px;
                cursor: pointer;
                font-size: 12px;
                transition: all 0.2s;
                min-width: 120px;
                &:hover {
                    background: #e0e0e0;
                }
            `;
            restoreBtn.onclick = () => {
                localStorage.removeItem(`edited_content_${contentId}`);
                element.textContent = element.getAttribute('data-original-content');
                restoreBtn.remove();
                this.showToast('Content restored to original', 'success');
            };
            element.parentNode.insertBefore(restoreBtn, element.nextSibling);
        }
    }

    recoverStoredContent() {
        document.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach(element => {
            const contentId = this.generateContentId(element);
            const storedData = localStorage.getItem(`edited_content_${contentId}`);
            
            if (storedData) {
                const data = JSON.parse(storedData);
                if (data.userId === this.userId) {
                    element.textContent = data.content;
                    element.setAttribute('data-is-edited', 'true');
                    element.setAttribute('data-content-id', contentId);
                }
            }
        });
    }

    showEditModal(element) {
        const modal = document.createElement('div');
        modal.className = 'theme-preview-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            backdrop-filter: blur(5px);
        `;

        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            border-radius: 12px;
            width: 300px;
            padding: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;

        const title = document.createElement('h2');
        title.textContent = 'Edit Content';
        title.style.cssText = `
            margin: 0 0 12px;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        `;

        const contentTextarea = document.createElement('textarea');
        contentTextarea.value = element.textContent.trim();
        contentTextarea.placeholder = 'Enter your content here...';
        contentTextarea.style.cssText = `
            width: 100%;
            height: 120px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 8px;
            font-size: 13px;
            line-height: 1.4;
            resize: none;
            outline: none;
            font-family: inherit;
            margin-bottom: 12px;
            overflow: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            &::-webkit-scrollbar {
                display: none;
            }
            &:focus {
                border-color: #2196F3;
                box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
            }
        `;

        const buttonGroup = document.createElement('div');
        buttonGroup.style.cssText = `
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        `;

        const saveBtn = document.createElement('button');
        saveBtn.textContent = 'Save';
        saveBtn.style.cssText = `
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            background: #2196F3;
            color: white;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            &:hover {
                background: #1976D2;
            }
        `;

        const cancelBtn = document.createElement('button');
        cancelBtn.textContent = 'Cancel';
        cancelBtn.style.cssText = `
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            color: #333;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            &:hover {
                background: #f5f5f5;
            }
        `;

        saveBtn.onclick = () => {
            const newContent = contentTextarea.value.trim();
            if (newContent) {
                element.textContent = newContent;
                this.saveContentToStorage(element);
                this.showToast('Content saved successfully', 'success');
                modal.remove();
            }
        };

        cancelBtn.onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };

        buttonGroup.appendChild(cancelBtn);
        buttonGroup.appendChild(saveBtn);
        content.appendChild(title);
        content.appendChild(contentTextarea);
        content.appendChild(buttonGroup);
        modal.appendChild(content);
        document.body.appendChild(modal);

        contentTextarea.focus();
        contentTextarea.setSelectionRange(contentTextarea.value.length, contentTextarea.value.length);
    }

    showGenerateModal(element, initialPrompt = '') {
        const modal = document.createElement('div');
        modal.className = 'theme-preview-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            backdrop-filter: blur(5px);
        `;

        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            border-radius: 12px;
            width: 300px;
            padding: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;

        const title = document.createElement('h2');
        title.textContent = 'Generate Content';
        title.style.cssText = `
            margin: 0 0 12px;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        `;

        // Prompt section
        const promptLabel = document.createElement('label');
        promptLabel.textContent = 'Prompt';
        promptLabel.style.cssText = `
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
        `;

        const promptTextarea = document.createElement('textarea');
        promptTextarea.value = initialPrompt;
        promptTextarea.placeholder = 'Enter your prompt here...';
        promptTextarea.style.cssText = `
            width: 100%;
            height: 60px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 8px;
            font-size: 13px;
            line-height: 1.4;
            resize: none;
            outline: none;
            font-family: inherit;
            margin-bottom: 12px;
            overflow: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            &::-webkit-scrollbar {
                display: none;
            }
            &:focus {
                border-color: #2196F3;
                box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
            }
        `;

        // Content section
        const contentLabel = document.createElement('label');
        contentLabel.textContent = 'Content';
        contentLabel.style.cssText = `
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
        `;

        const contentTextarea = document.createElement('textarea');
        contentTextarea.value = element ? element.textContent.trim() : '';
        contentTextarea.placeholder = 'Generated content will appear here...';
        contentTextarea.style.cssText = `
            width: 100%;
            height: 80px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 8px;
            font-size: 13px;
            line-height: 1.4;
            resize: none;
            outline: none;
            font-family: inherit;
            margin-bottom: 12px;
            overflow: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            &::-webkit-scrollbar {
                display: none;
            }
            &:focus {
                border-color: #2196F3;
                box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
            }
        `;

        const buttonGroup = document.createElement('div');
        buttonGroup.style.cssText = `
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        `;

        const generateBtn = document.createElement('button');
        generateBtn.textContent = 'Generate';
        generateBtn.style.cssText = `
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            background: #2196F3;
            color: white;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            &:hover {
                background: #1976D2;
            }
        `;

        const cancelBtn = document.createElement('button');
        cancelBtn.textContent = 'Cancel';
        cancelBtn.style.cssText = `
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            color: #333;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            &:hover {
                background: #f5f5f5;
            }
        `;

        generateBtn.onclick = async () => {
            const prompt = promptTextarea.value.trim();
            if (prompt) {
                modal.remove();
                await this.generateContent(prompt, element);
                this.saveContentToStorage(element);
            }
        };

        cancelBtn.onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };

        buttonGroup.appendChild(cancelBtn);
        buttonGroup.appendChild(generateBtn);
        content.appendChild(title);
        content.appendChild(promptLabel);
        content.appendChild(promptTextarea);
        content.appendChild(contentLabel);
        content.appendChild(contentTextarea);
        content.appendChild(buttonGroup);
        modal.appendChild(content);
        document.body.appendChild(modal);

        promptTextarea.focus();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new ThemePreviewImageHandler();
}); 