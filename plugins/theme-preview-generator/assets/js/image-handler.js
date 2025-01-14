/**
 * Theme Preview Image Handler
 * Handles image uploads and replacements
 */

class ThemePreviewImageHandler {
    constructor() {
        this.settings = window.themePreviewSettings || {};
        this.userId = this.getUserId();
        this.contentTracker = this.initializeContentTracker();
        this.init();
        // Make the instance globally accessible
        window.themePreviewHandler = this;
    }

    initializeContentTracker() {
        const storedTracker = localStorage.getItem('theme_preview_content_tracker');
        return storedTracker ? JSON.parse(storedTracker) : {
            contents: {},
            lastUpdate: Date.now(),
            version: 1
        };
    }

    updateContentTracker(contentId, data) {
        this.contentTracker.contents[contentId] = {
            ...data,
            lastUpdate: Date.now()
        };
        localStorage.setItem('theme_preview_content_tracker', JSON.stringify(this.contentTracker));
    }

    generateContentId(element) {
        const tagName = element.tagName.toLowerCase();
        const text = element.textContent.trim();
        const path = this.getElementPath(element);
        const timestamp = Date.now();
        
        // Include more unique identifiers
        const idString = `${path}_${tagName}_${text.substring(0, 50)}_${timestamp}_${this.userId}`; 
        
        let hash = 0;
        for (let i = 0; i < idString.length; i++) {
            const char = idString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        
        const contentId = `content_${Math.abs(hash)}`;
        
        // Store the mapping between element path and content ID
        this.updateContentTracker(contentId, {
            path: path,
            tagName: tagName,
            timestamp: timestamp,
            userId: this.userId,
            type: 'generated'
        });
        
        return contentId;
    }

    storeGeneratedContent(element, content, prompt = '') {
        if (!element) return null;
        
        const contentId = this.generateContentId(element);
        const data = {
            content: content,
            prompt: prompt,
            timestamp: Date.now(),
            userId: this.userId,
            path: this.getElementPath(element),
            version: 1,
            type: 'generated'
        };
        
        // Store in both localStorage and contentTracker
        localStorage.setItem(`generated_content_${contentId}`, JSON.stringify(data));
        this.updateContentTracker(contentId, data);
        
        // Mark the element
        element.setAttribute('data-content-id', contentId);
        element.setAttribute('data-is-generated', 'true');
        element.setAttribute('data-content-version', '1');
        
        // Add visual indicators
        this.addVersionBadge(element, data.version);
        
        return contentId;
    }

    applyGeneratedContent(element, data, contentId) {
        element.textContent = data.content;
        element.setAttribute('data-content-id', contentId);
        element.setAttribute('data-is-generated', 'true');
        element.setAttribute('data-content-version', data.version.toString());
        this.addVersionBadge(element, data.version);
    }

    applyEditedContent(element, data, contentId) {
        element.textContent = data.content;
        element.setAttribute('data-content-id', contentId);
        element.setAttribute('data-is-edited', 'true');
        if (data.version) {
            element.setAttribute('data-content-version', data.version.toString());
            this.addVersionBadge(element, data.version);
        }
    }

    addVersionBadge(element, version) {
        if (!element) return;
        
        let badge = element.querySelector('.content-version-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'content-version-badge';
            badge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                background: #10B981;
                color: white;
                border-radius: 12px;
                padding: 2px 8px;
                font-size: 11px;
                font-weight: 500;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                z-index: 1;
            `;
            
            const wrapper = this.ensureWrapper(element);
            if (wrapper) {
                wrapper.style.position = 'relative';
                wrapper.appendChild(badge);
            }
        }
        if (badge) {
            badge.textContent = `v${version}`;
        }
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
    }

    recoverStoredContent() {
        // Get all tracked content
        const tracker = this.contentTracker;
        
        // Process each tracked content
        Object.entries(tracker.contents).forEach(([contentId, data]) => {
            if (data.userId !== this.userId) return;
            
            // Find the element using the stored path
            const element = this.findElementByPath(data.path);
            if (!element) return;
            
            // Skip UI elements
            if (element.closest('.theme-preview-modal, .theme-preview-right-panel, .theme-preview-toast')) {
                return;
            }

            // Recover generated content
            const generatedData = localStorage.getItem(`generated_content_${contentId}`);
            if (generatedData) {
                const parsedData = JSON.parse(generatedData);
                if (parsedData.userId === this.userId) {
                    this.applyGeneratedContent(element, parsedData, contentId);
                }
            }

            // Recover edited content
            const editedData = localStorage.getItem(`edited_content_${contentId}`);
            if (editedData) {
                const parsedData = JSON.parse(editedData);
                if (parsedData.userId === this.userId) {
                    this.applyEditedContent(element, parsedData, contentId);
                }
            }
        });
    }

    findElementByPath(path) {
        try {
            return document.querySelector(path);
        } catch (e) {
            // If the selector is invalid, try a more robust approach
            const parts = path.split(' > ');
            let current = document;
            
            for (const part of parts) {
                const [tag, nth] = part.split(':nth-of-type(');
                if (nth) {
                    const index = parseInt(nth) - 1;
                    const elements = Array.from(current.getElementsByTagName(tag));
                    current = elements[index] || null;
                } else {
                    current = current.querySelector(part);
                }
                if (!current) break;
            }
            
            return current;
        }
    }

    clearContentHistory() {
        const tracker = this.contentTracker;
        Object.keys(tracker.contents).forEach(contentId => {
            localStorage.removeItem(`generated_content_${contentId}`);
            localStorage.removeItem(`edited_content_${contentId}`);
        });
        this.contentTracker.contents = {};
        localStorage.setItem('theme_preview_content_tracker', JSON.stringify(this.contentTracker));
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
                const errorMsg = 'Cloudinary settings are missing. Please add your Cloud Name and Upload Preset in WordPress Admin > Theme Preview > Settings.';
                this.showToast(errorMsg, 'error');
                console.error('Cloudinary settings missing:', {
                    cloudName: this.settings?.cloudName ? 'set' : 'missing',
                    uploadPreset: this.settings?.uploadPreset ? 'set' : 'missing'
                });
                throw new Error(errorMsg);
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
                throw new Error('Failed to upload image to Cloudinary. Please check your credentials and try again.');
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

        // Check if image is edited
        const imageId = this.generateImageId(img);
        const storedData = localStorage.getItem(`replaced_image_${imageId}`);
        const isEdited = img.hasAttribute('data-is-replaced') || storedData;

        const buttons = [
            {
                name: 'Replace',
                icon: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>',
                onClick: () => fileInput.click()
            },
            {
                name: 'Preview',
                icon: '<path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                onClick: () => this.showImagePreview(img)
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

        // Add View Original button if image is edited
        if (isEdited) {
            buttons.splice(4, 0, {
                name: 'View Original',
                icon: '<path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                onClick: () => this.showOriginalImageModal(img)
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

    showImagePreview(imageElement) {
        const modal = document.createElement('div');
        modal.className = 'image-preview-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        `;
        
        const content = document.createElement('div');
        content.className = 'image-preview-content';
        content.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            overflow: auto;
        `;
        
        const img = document.createElement('img');
        img.src = imageElement.src;
        img.alt = imageElement.alt;
        img.style.cssText = `
            max-width: 100%;
            max-height: calc(90vh - 40px);
            object-fit: contain;
        `;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        `;
        
        content.appendChild(img);
        content.appendChild(closeBtn);
        modal.appendChild(content);
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

    resetImage(imageElement) {
        const imageId = imageElement.getAttribute('data-image-id');
        const storedData = localStorage.getItem(`replaced_image_${imageId}`);
        
        if (!storedData) return;
        
        const data = JSON.parse(storedData);
        
        // Reset image to original source
        imageElement.src = data.originalSrc;
        
        // Remove stored data and attributes
        localStorage.removeItem(`replaced_image_${imageId}`);
        imageElement.removeAttribute('data-is-replaced');
        imageElement.removeAttribute('data-image-id');
        imageElement.removeAttribute('data-original-src');
        
        // Re-add action buttons without the Original button
        this.addActionButton(imageElement);
    }

    ensureWrapper(element) {
        if (!element) return null;
        
        let wrapper = element.parentElement;
        if (!wrapper) return null;
        
        if (!wrapper.style.position || wrapper.style.position === 'static') {
            wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.display = 'inline-block';
            element.parentNode.insertBefore(wrapper, element);
            wrapper.appendChild(element);
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
        if (!element) return () => {}; // Return empty function if element is null
        
        // Save original content and styles
        const originalContent = element.innerHTML;
        const originalPosition = element.style.position;
        const originalBackground = element.style.background;
        
        // Add loading class
        element.classList.add('theme-preview-loading');
        
        // Create inline loading spinner
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.innerHTML = `
            <div class="spinner-container">
                <div class="spinner-ring"></div>
                <div class="spinner-ring-inner"></div>
                <div class="spinner-pulse"></div>
            </div>
        `;
        spinner.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            width: 100%;
            background: transparent;
            border-radius: 8px;
            min-height: ${element.offsetHeight}px;
        `;

        // Add styles for the spinning animations
        const style = document.createElement('style');
        style.textContent = `
            .spinner-container {
                position: relative;
                width: 32px;
                height: 32px;
            }
            .spinner-ring {
                position: absolute;
                width: 32px;
                height: 32px;
                border: 3px solid rgba(33, 150, 243, 0.1);
                border-top: 3px solid #2196F3;
                border-radius: 50%;
                animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            }
            .spinner-ring-inner {
                position: absolute;
                width: 20px;
                height: 20px;
                border: 3px solid transparent;
                border-top: 3px solid #1976D2;
                border-radius: 50%;
                top: 6px;
                left: 6px;
                animation: spin-reverse 0.75s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            }
            .spinner-pulse {
                position: absolute;
                width: 8px;
                height: 8px;
                background: #2196F3;
                border-radius: 50%;
                top: 12px;
                left: 12px;
                animation: pulse 1s ease-in-out infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            @keyframes spin-reverse {
                0% { transform: rotate(360deg); }
                100% { transform: rotate(0deg); }
            }
            @keyframes pulse {
                0% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.2); opacity: 0.8; }
                100% { transform: scale(1); opacity: 1; }
            }
            .theme-preview-loading {
                position: relative;
                transition: all 0.3s ease;
            }
        `;
        document.head.appendChild(style);
        
        // Clear the element's content and add the spinner
        element.innerHTML = '';
        element.appendChild(spinner);
        
        // Return function to remove loading state
        return () => {
            element.classList.remove('theme-preview-loading');
            element.style.position = originalPosition;
            element.style.background = originalBackground;
            const loadingSpinner = element.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.remove();
            }
            style.remove();
        };
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

    async generatePageContent(business, prompt) {
        // Show initial loading state with absolute positioning
        const initialLoader = document.createElement('div');
        initialLoader.className = 'initial-loading-spinner';
        initialLoader.innerHTML = `
            <div class="spinner-container">
                <div class="spinner-ring"></div>
                <div class="spinner-ring-inner"></div>
                <div class="spinner-pulse"></div>
                <span style="font-size: 14px; color: #333; margin-top: 8px;">Generating...</span>
            </div>
        `;
        initialLoader.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            backdrop-filter: blur(5px);
        `;

        const spinnerStyles = document.createElement('style');
        spinnerStyles.textContent = `
            .spinner-container {
                position: relative;
                width: 80px;
                height: 80px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .spinner-ring {
                position: absolute;
                width: 60px;
                height: 60px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #2196F3;
                border-radius: 50%;
                animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            }
            .spinner-ring-inner {
                position: absolute;
                width: 40px;
                height: 40px;
                border: 3px solid transparent;
                border-top: 3px solid #1976D2;
                border-radius: 50%;
                animation: spin-reverse 0.75s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            }
            .spinner-pulse {
                position: absolute;
                width: 15px;
                height: 15px;
                background: #2196F3;
                border-radius: 50%;
                animation: pulse 1s ease-in-out infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            @keyframes spin-reverse {
                0% { transform: rotate(360deg); }
                100% { transform: rotate(0deg); }
            }
            @keyframes pulse {
                0% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.2); opacity: 0.8; }
                100% { transform: scale(1); opacity: 1; }
            }
        `;
        document.head.appendChild(spinnerStyles);
        document.body.appendChild(initialLoader);

        try {
            // Get stored prompt
            const storedPrompt = localStorage.getItem('theme_preview_prompt');
            if (!storedPrompt) {
                throw new Error('No prompt found. Please set a prompt in the Generate panel.');
            }

            // Collect all text content from the page
            const contentElements = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, .wp-block-heading, .wp-block-paragraph'));
            const contentMap = new Map();

            // Create a structured content map
            contentElements.forEach(element => {
                const tag = element.tagName.toLowerCase();
                const text = element.textContent.trim();
                if (text) {
                    if (!contentMap.has(tag)) {
                        contentMap.set(tag, []);
                    }
                    contentMap.get(tag).push({
                        element: element,
                        text: text,
                        path: this.getElementPath(element)
                    });
                }
            });

            // Create a structured prompt and get the response
            const contentStructure = Array.from(contentMap.entries()).map(([tag, items]) => ({
                tag: tag,
                items: items.map(item => ({
                    text: item.text,
                    type: tag
                }))
            }));

            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.settings.openaiApiKey}`
                },
                body: JSON.stringify({
                    model: "gpt-4o",
                    messages: [
                        {
                            role: "system",
                            content: `You are an AI that creates website content. Generate content for a ${business} website. Use this specific prompt for guidance: ${storedPrompt}. Maintain the exact same structure and formatting. Return ONLY a valid JSON array with the exact same structure as provided, without any markdown formatting or code blocks.`
                        },
                        {
                            role: "user",
                            content: `Original content structure: ${JSON.stringify(contentStructure)}\n\nGenerate new content following the prompt: ${storedPrompt}`
                        }
                    ],
                    temperature: 0.7,
                    max_tokens: 2000
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error?.message || 'Failed to generate content');
            }

            const result = await response.json();
            console.log('OpenAI Response:', result);
            const content = result.choices[0].message.content;
            console.log('Generated Content:', content);
            
            // Parse the content as JSON
            let newContent;
            try {
                newContent = JSON.parse(content);
            } catch (error) {
                throw new Error('Failed to parse generated content');
            }

            // Remove initial loader immediately after getting the response
            initialLoader.remove();
            spinnerStyles.remove();

            // Apply the new content while maintaining structure
            for (const section of newContent) {
                const elements = contentMap.get(section.tag) || [];
                for (let i = 0; i < section.items.length; i++) {
                    const item = section.items[i];
                    const elementData = elements[i];
                    if (elementData) {
                        const element = elementData.element;
                        // Add individual loading state
                        const removeLoader = this.addLoadingState(element);
                        // Show loading state for a brief moment
                        await new Promise(resolve => setTimeout(resolve, 500));
                        element.textContent = item.text;
                        const contentId = this.storeGeneratedContent(element, item.text, business);
                        if (!contentId) {
                            console.warn('Failed to store generated content');
                        }
                        removeLoader();
                    }
                }
            }

            this.showToast('Content generated successfully', 'success');

        } catch (error) {
            console.error('Page content generation error:', error);
            this.showToast(error.message || 'Failed to generate page content', 'error');
            throw error;
        } finally {
            // Ensure loaders are removed in case of error
            if (document.body.contains(initialLoader)) {
                initialLoader.remove();
                spinnerStyles.remove();
            }
        }
    }

    async generateContent(business, element) {
        let removeLoader;
        
        try {
            // Use the passed business parameter first, then fallback to stored value
            const businessName = business || localStorage.getItem('theme_preview_business');
            if (!businessName) {
                throw new Error('Please set a business name first in the Generate panel');
            }

            if (!this.settings?.openaiApiKey) {
                const errorMsg = 'OpenAI API key is missing. Please add it in WordPress Admin > Theme Preview > Settings.';
                this.showToast(errorMsg, 'error');
                throw new Error(errorMsg);
            }

            let originalContent = '';
            let contentLength = 0;

            if (element) {
                originalContent = element.textContent?.trim() || '';
                contentLength = originalContent.length;
            }

            const systemPrompt = {
                role: "system",
                content: `You are an AI that creates the content for a WordPress webpage. 
                The business for which the webpage is created is: ${businessName}.
                Follow the user's flow to craft each piece of text for the website.
                Ensure the content is cohesive, interrelated, and not isolated.
                Prioritize quality above all. The text must be high-quality. Use a conversational tone with simple language, 
                suitable for a third-grade student, and minimize academic jargon.
                IMPORTANT: Generate content that matches the following constraints:
                - Maintain approximately ${contentLength} characters
                - Maintain similar structure and formatting as the original text`
            };

            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.settings.openaiApiKey}`
                },
                body: JSON.stringify({
                    model: "gpt-4o",
                    messages: [
                        systemPrompt,
                        {
                            role: "user",
                            content: `Original content structure:\n${originalContent}\n\nGenerate new content maintaining similar length and structure.`
                        }
                    ],
                    temperature: 0.7,
                    max_tokens: Math.max(500, contentLength * 2)
                })
            });

            if (!response.ok) {
                const error = await response.json();
                const errorMsg = error.error?.message || 'Failed to generate content';
                this.showToast(`OpenAI Error: ${errorMsg}`, 'error');
                throw new Error(errorMsg);
            }

            const result = await response.json();
            console.log('OpenAI Response:', result);
            const content = result.choices[0].message.content;
            console.log('Generated Content:', content);
            
            // Parse the content as JSON
            let newContent;
            try {
                newContent = content;
            } catch (error) {
                throw new Error('Failed to parse generated content');
            }

            if (element) {
                element.innerHTML = newContent;
                this.showToast('Content generated successfully', 'success');
                const contentId = this.storeGeneratedContent(element, newContent, businessName);
                if (!contentId) {
                    console.warn('Failed to store generated content');
                }
            }
            
            return newContent;
        } catch (error) {
            console.error('Content generation error:', error);
            this.showToast(error.message || 'Failed to generate content', 'error');
            if (element) {
                element.innerHTML = element.getAttribute('data-original-content') || element.innerHTML;
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
                    model: "gpt-4o",
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
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        `;
        
        const content = document.createElement('div');
        content.className = 'image-preview-content';
        content.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            overflow: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        `;
        
        const img = document.createElement('img');
        img.src = imageElement.src;
        img.alt = 'Original Image';
        img.style.cssText = `
            max-width: 100%;
            max-height: calc(90vh - 120px);
            object-fit: contain;
        `;

        const restoreButton = document.createElement('button');
        restoreButton.textContent = 'Restore Original Image';
        restoreButton.style.cssText = `
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            &:hover {
                background: #1976D2;
            }
        `;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        `;

        restoreButton.addEventListener('click', () => {
            this.resetImage(imageElement);
            modal.remove();
        });
        
        content.appendChild(img);
        content.appendChild(restoreButton);
        content.appendChild(closeBtn);
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        const closeModal = () => modal.remove();
        closeBtn.onclick = closeModal;
        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        };
        
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
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 9999;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            min-width: 150px;
            &:hover {
                box-shadow: 0 12px 40px rgba(31, 38, 135, 0.25);
            }
        `;
        
        const buttons = [
            {
                name: 'Generate All',
                icon: '<path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/>',
                onClick: () => this.showContentGenerator(),
                color: '#10B981',
                highlight: true
            },
            {
                name: 'Copy Template',
                icon: '<path d="M20 9h-9a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
                onClick: () => this.copyEntireTemplate(),
                color: '#6366F1'
            },
            {
                name: 'Copy Blocks',
                icon: '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
                onClick: () => this.copyBlocks(),
                color: '#8B5CF6'
            },
            {
                name: 'Theme Info',
                icon: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="8"/>',
                onClick: () => this.showThemeInfo(),
                color: '#EC4899'
            }
        ];

        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'theme-preview-panel-toggle';
        toggleBtn.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

        buttons.forEach(({name, icon, onClick, color, highlight}) => {
            const button = document.createElement('button');
            button.className = 'theme-preview-panel-button';
            const baseStyles = `
                background: ${highlight ? `linear-gradient(135deg, ${color}, ${color}dd)` : 'white'};
                border: 1px solid ${highlight ? 'transparent' : '#e0e0e0'};
                border-radius: 10px;
                padding: 8px 12px;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                transition: all 0.2s;
                min-width: 130px;
                color: ${highlight ? 'white' : color};
                position: relative;
                overflow: hidden;
                
                &:hover {
                    transform: translateX(-3px);
                    box-shadow: 3px 3px 10px ${color}22;
                    border-color: ${color};
                    background: ${highlight ? `linear-gradient(135deg, ${color}dd, ${color})` : `linear-gradient(135deg, white, ${color}11)`};
                }
                
                &:active {
                    transform: translateX(-3px) scale(0.98);
                }
            `;
            
            button.style.cssText = baseStyles;
            
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
            width: 750px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;

        // Get stored values
        const storedBusiness = localStorage.getItem('theme_preview_business') || '';
        const storedPrompt = localStorage.getItem('theme_preview_prompt') || '';

        content.innerHTML = `
            <h2 style="margin: 0 0 24px; font-size: 1.5rem; color: #333;">Generate Content</h2>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Business Name</label>
                <input type="text" value="${storedBusiness}" placeholder="Enter business name" style="
                    width: 700px;
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
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Prompt</label>
                <textarea placeholder="Enter your prompt for content generation" style="
                    width: 700px;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    min-height: 100px;
                    resize: vertical;
                    &:focus {
                        outline: none;
                        border-color: #2196F3;
                        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                    }
                ">${storedPrompt}</textarea>
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Content Type</label>
                <select style="
                    width: 700px;
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
        const promptTextarea = content.querySelector('textarea');
        const typeSelect = content.querySelector('select');
        const generateBtn = content.querySelector('.generate-btn');
        const cancelBtn = content.querySelector('.cancel-btn');

        // Auto-focus the business input if empty
        if (!storedBusiness) {
            businessInput.focus();
        }

        generateBtn.onclick = async () => {
            const business = businessInput.value.trim();
            const prompt = promptTextarea.value.trim();
            const type = typeSelect.value;
            
            if (business && prompt) {
                // Store values in localStorage
                localStorage.setItem('theme_preview_business', business);
                localStorage.setItem('theme_preview_prompt', prompt);
                
                modal.remove();
                const contentArea = document.querySelector('.entry-content');
                
                switch (type) {
                    case 'full':
                        await this.generatePageContent(business, prompt);
                        break;
                    case 'tagline':
                        const tagline = await this.generateSingleTagline(business, prompt);
                        if (tagline) {
                            const taglineElement = document.querySelector('.site-description') || document.createElement('div');
                            taglineElement.textContent = tagline;
                        }
                        break;
                    case 'description':
                        await this.generateContent(business, contentArea, prompt);
                        break;
                }
            } else {
                this.showToast('Please enter both business name and prompt', 'error');
            }
        };

        cancelBtn.onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };

        // Add keyboard shortcuts
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', escHandler);
            }
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                generateBtn.click();
                document.removeEventListener('keydown', escHandler);
            }
        });
    }

    showOriginalImageModal(imageElement) {
        const imageId = imageElement.getAttribute('data-image-id');
        const storedData = localStorage.getItem(`replaced_image_${imageId}`);
        
        if (!storedData) return;
        
        const data = JSON.parse(storedData);
        const originalSrc = data.originalSrc;

        const modal = document.createElement('div');
        modal.className = 'image-preview-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        `;
        
        const content = document.createElement('div');
        content.className = 'image-preview-content';
        content.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            overflow: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        `;
        
        const img = document.createElement('img');
        img.src = originalSrc;
        img.alt = 'Original Image';
        img.style.cssText = `
            max-width: 100%;
            max-height: calc(90vh - 120px);
            object-fit: contain;
        `;

        const restoreButton = document.createElement('button');
        restoreButton.textContent = 'Restore Original Image';
        restoreButton.style.cssText = `
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            &:hover {
                background: #1976D2;
            }
        `;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        `;

        restoreButton.addEventListener('click', () => {
            this.resetImage(imageElement);
            modal.remove();
        });
        
        content.appendChild(img);
        content.appendChild(restoreButton);
        content.appendChild(closeBtn);
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        const closeModal = () => modal.remove();
        closeBtn.onclick = closeModal;
        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        };
        
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
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
        `;

        const content = document.createElement('div');
        content.className = 'theme-preview-modal-content';
        content.style.cssText = `
            background: white;
            border-radius: 16px;
            width: 750px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;

        content.innerHTML = `
            <h2 style="margin: 0 0 24px; font-size: 1.5rem; color: #333;">Edit Content</h2>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Content</label>
                <textarea placeholder="Enter or paste your content here..." style="
                    width: 700px;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    min-height: 100px;
                    resize: vertical;
                    &:focus {
                        outline: none;
                        border-color: #2196F3;
                        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                    }
                ">${element.textContent.trim()}</textarea>
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
                <button class="save-btn" style="
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
                ">Save</button>
            </div>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);

        const contentTextarea = content.querySelector('textarea');
        const saveBtn = content.querySelector('.save-btn');
        const cancelBtn = content.querySelector('.cancel-btn');

        saveBtn.onclick = () => {
            const editedContent = contentTextarea.value.trim();
            if (editedContent) {
                element.textContent = editedContent;
                this.saveContentToStorage(element);
                this.showToast('Content saved successfully', 'success');
                modal.remove();
            }
        };

        cancelBtn.onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };

        // Add keyboard shortcuts
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', escHandler);
            }
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                saveBtn.click();
                document.removeEventListener('keydown', escHandler);
            }
        });

        // Focus the content textarea and place cursor at the end
        contentTextarea.focus();
        contentTextarea.setSelectionRange(contentTextarea.value.length, contentTextarea.value.length);
    }

    // Individual content generation modal
    showGenerateModal(element) {
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
            width: 750px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;

        content.innerHTML = `
            <h2 style="margin: 0 0 24px; font-size: 1.5rem; color: #333;">Generate Individual Content</h2>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Content</label>
                <textarea placeholder="Enter or paste your content here..." style="
                    width: 700px;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    min-height: 100px;
                    resize: vertical;
                    &:focus {
                        outline: none;
                        border-color: #2196F3;
                        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                    }
                ">${element ? element.textContent.trim() : ''}</textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Prompt</label>
                <textarea placeholder="Enter your prompt for content generation" style="
                    width: 700px;
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    min-height: 100px;
                    resize: vertical;
                    &:focus {
                        outline: none;
                        border-color: #2196F3;
                        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                    }
                "></textarea>
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
                    background: #F59E0B;
                    color: white;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    &:hover {
                        background: #D97706;
                    }
                ">Generate</button>
            </div>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);

        const contentTextarea = content.querySelector('textarea:first-of-type');
        const promptTextarea = content.querySelector('textarea:last-of-type');
        const generateBtn = content.querySelector('.generate-btn');
        const cancelBtn = content.querySelector('.cancel-btn');

        generateBtn.onclick = async () => {
            const mainContent = contentTextarea.value.trim();
            const prompt = promptTextarea.value.trim();
            
            if (mainContent && prompt) {
                modal.remove();
                
                // Create a temporary element to hold the content
                const tempElement = document.createElement('div');
                tempElement.textContent = mainContent;
                
                // Generate content for this specific element using the prompt
                await this.generateContent(prompt, tempElement);
                
                // Update the original element with the generated content
                if (element) {
                    element.textContent = tempElement.textContent;
                    this.storeGeneratedContent(element, tempElement.textContent, prompt);
                }
            } else {
                this.showToast('Please enter both content and prompt', 'error');
            }
        };

        cancelBtn.onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };

        // Add keyboard shortcuts
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', escHandler);
            }
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                generateBtn.click();
                document.removeEventListener('keydown', escHandler);
            }
        });
    }
}

// Initialize when DOM is loaded
jQuery(document).ready(function($) {
    new ThemePreviewImageHandler();
}); 