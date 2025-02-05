/**
 * Theme Preview Image Handler
 * Handles image uploads and replacements
 */

class ThemePreviewImageHandler {
    constructor() {
        this.settings = window.themePreviewSettings || {};
        this.userId = this.getUserId();
        this.contentTracker = this.initializeContentTracker();
        this.currentTheme = this.settings.themeName;
        this.checkThemeChange();
        this.init();
        // Make the instance globally accessible
        window.themePreviewHandler = this;
    }

    checkThemeChange() {
        const storedTheme = localStorage.getItem('current_theme_name');
        const currentTheme = this.settings.themeName;

        if (storedTheme && storedTheme !== currentTheme) {
            // Theme has changed, clear all previous theme data
            this.clearAllThemeData();
        }

        // Store current theme name
        localStorage.setItem('current_theme_name', currentTheme);
    }

    clearAllThemeData() {
        const preserveKeys = [
            'loglevel',
            'theme_preview_business',
            'theme_preview_content_tracker',
            'theme_preview_prompt',
            'theme_preview_user_id'
        ];

        // Clear only theme-specific data from localStorage
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            // Skip preserved keys
            if (preserveKeys.includes(key)) continue;
            
            // Remove only theme-specific content
            if (key.startsWith('replaced_image_') || 
                key.startsWith('edited_content_') || 
                key.startsWith('generated_content_') ||
                key.startsWith('edited_link_')) {
                keysToRemove.push(key);
            }
        }

        // Remove the collected keys
        keysToRemove.forEach(key => localStorage.removeItem(key));

        console.log('Cleared previous theme-specific data');
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

    storeGeneratedContent(element, content, prompt = '') {
        if (!element) return null;
        
        // Get the most specific path
        const path = this.getElementPath(element);
        
        // Generate content ID using the specific path
        const timestamp = Date.now();
        const idString = `${path}_${timestamp}_${Math.random()}_${this.userId}`;
        let hash = 0;
        for (let i = 0; i < idString.length; i++) {
            const char = idString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        const contentId = `content_${timestamp}_${Math.abs(hash)}`;

        // Remove any existing content for this element
        const existingId = element.getAttribute('data-content-id');
        if (existingId) {
            localStorage.removeItem(`generated_content_${existingId}`);
        }

        // Store the content with the specific path
        const data = {
            content: content,
            prompt: prompt,
            timestamp: timestamp,
            userId: this.userId,
            path: path,
            version: 1,
            type: 'generated',
            themeName: this.settings.themeName
        };
        
        localStorage.setItem(`generated_content_${contentId}`, JSON.stringify(data));
        
        // Update element attributes
        element.setAttribute('data-content-id', contentId);
        element.setAttribute('data-is-generated', 'true');
        element.setAttribute('data-content-version', '1');
        
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
            timestamp: Date.now(),
            userId: this.userId,
            path: this.getElementPath(element),
            themeName: this.settings.themeName,
            type: 'edited'
        };
        localStorage.setItem(`edited_content_${contentId}`, JSON.stringify(data));
        element.setAttribute('data-content-id', contentId);
        element.setAttribute('data-is-edited', 'true');
    }

    recoverStoredContent() {
        // Process each content type separately
        const processContent = (prefix) => {
            const contentItems = [];
            // First collect all content items
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith(prefix)) {
                    try {
                        const data = JSON.parse(localStorage.getItem(key));
                        if (data.userId === this.userId && data.themeName === this.settings.themeName) {
                            contentItems.push({
                                key: key,
                                data: data,
                                contentId: key.replace(prefix, '')
                            });
                        }
                    } catch (error) {
                        console.error('Error parsing stored content:', error);
                    }
                }
            }

            // Sort by timestamp to apply in correct order
            contentItems.sort((a, b) => a.data.timestamp - b.data.timestamp);

            // Apply content
            contentItems.forEach(item => {
                try {
                    const element = this.findElementByPath(item.data.path);
                    if (element && !element.closest('.theme-preview-modal, .theme-preview-right-panel, .theme-preview-toast')) {
                        // Update element content and attributes
                        element.textContent = item.data.content;
                        element.setAttribute('data-content-id', item.contentId);
                        element.setAttribute('data-is-' + item.data.type, 'true');
                        if (item.data.version) {
                            element.setAttribute('data-content-version', item.data.version.toString());
                            this.addVersionBadge(element, item.data.version);
                        }

                        // Update content tracker
                        this.updateContentTracker(item.contentId, {
                            type: item.data.type,
                            path: item.data.path,
                            timestamp: item.data.timestamp
                        });
                    }
                } catch (error) {
                    console.error('Error recovering content:', error);
                }
            });
        };

        // Process edited content first, then generated content
        processContent('edited_content_');
        processContent('generated_content_');
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
        this.addContentActionButtons();
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

            // Ensure we have an image ID
            const imageId = imageElement.getAttribute('data-image-id') || this.generateImageId(imageElement);

            // Store replacement data with additional metadata
            const replacementData = {
                originalSrc: imageElement.getAttribute('data-original-src') || imageElement.src,
                currentSrc: result.secure_url,
                publicId: result.public_id,
                timestamp: Date.now(),
                userId: this.userId,
                themeName: this.settings.themeName,
                path: this.getElementPath(imageElement)
            };

            // Save to local storage with theme-specific key
            const storageKey = `replaced_image_${imageId}`;
            localStorage.setItem(storageKey, JSON.stringify(replacementData));

            // Update image attributes
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
                name: 'Browse Photos',
                icon: '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7l-3 3.72L9 13l-3 4h12l-4-5z"/><circle cx="6.5" cy="8.5" r="1.5"/>',
                onClick: () => this.showUnsplashModal(img)
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

    async showUnsplashModal(imageElement) {
        const storedBusiness = localStorage.getItem('theme_preview_business') || '';
        const modal = document.createElement('div');
        modal.className = 'unsplash-modal';
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
        content.className = 'unsplash-modal-content';
        content.style.cssText = `
            background: white;
            border-radius: 12px;
            width: 90vw;
            max-width: 1200px;
            height: 80vh;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        `;

        const header = document.createElement('div');
        header.style.cssText = `
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            gap: 16px;
            align-items: center;
        `;

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.value = storedBusiness;
        searchInput.placeholder = 'Search photos...';
        searchInput.style.cssText = `
            flex: 1;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            &:focus {
                outline: none;
                border-color: #2196F3;
                box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
            }
        `;

        const searchButton = document.createElement('button');
        searchButton.innerHTML = `
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search
        `;
        searchButton.style.cssText = `
            padding: 12px 24px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            &:hover {
                background: #1976D2;
            }
        `;

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s;
            &:hover {
                background: #f5f5f5;
            }
        `;

        const imageGrid = document.createElement('div');
        imageGrid.style.cssText = `
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
            align-content: start;
        `;

        const loadingSpinner = document.createElement('div');
        loadingSpinner.className = 'unsplash-loading-spinner';
        loadingSpinner.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        `;
        loadingSpinner.innerHTML = `
            <div style="
                width: 40px;
                height: 40px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #2196F3;
                border-radius: 50%;
                animation: unsplashSpin 1s linear infinite;
            "></div>
        `;

        const style = document.createElement('style');
        style.textContent = `
            @keyframes unsplashSpin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .unsplash-image-item {
                position: relative;
                padding-bottom: 100%;
                background: #f5f5f5;
                border-radius: 8px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.2s;
            }
            .unsplash-image-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .unsplash-image-item img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .unsplash-image-item .photographer {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 8px;
                background: rgba(0,0,0,0.6);
                color: white;
                font-size: 12px;
                opacity: 0;
                transition: opacity 0.2s;
            }
            .unsplash-image-item:hover .photographer {
                opacity: 1;
            }
        `;

        document.head.appendChild(style);

        // Bind searchUnsplash to preserve 'this' context
        const searchUnsplash = async (query) => {
            loadingSpinner.style.display = 'block';
            imageGrid.innerHTML = '';
            
            try {
                if (!this.settings?.unsplashAccessKey) {
                    throw new Error('Unsplash access key is missing. Please add it in WordPress Admin > Theme Preview > Settings.');
                }

                const response = await fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=30`, {
                    headers: {
                        'Authorization': `Client-ID ${this.settings.unsplashAccessKey}`
                    }
                });
                
                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.errors?.[0] || 'Failed to fetch images');
                }
                
                const data = await response.json();
                
                data.results.forEach(photo => {
                    const item = document.createElement('div');
                    item.className = 'unsplash-image-item';
                    
                    const img = document.createElement('img');
                    img.src = photo.urls.small;
                    img.alt = photo.alt_description || 'Unsplash image';
                    
                    const photographer = document.createElement('div');
                    photographer.className = 'photographer';
                    photographer.textContent = `Photo by ${photo.user.name}`;
                    
                    item.appendChild(img);
                    item.appendChild(photographer);
                    
                    item.addEventListener('click', async () => {
                        try {
                            const response = await fetch(photo.urls.regular);
                            const blob = await response.blob();
                            const file = new File([blob], 'unsplash-image.jpg', { type: 'image/jpeg' });

                            // Store original image data before replacement
                            if (!imageElement.hasAttribute('data-original-src')) {
                                imageElement.setAttribute('data-original-src', imageElement.src);
                            }

                            // Generate and store image ID before upload
                            const imageId = this.generateImageId(imageElement);
                            imageElement.setAttribute('data-image-id', imageId);

                            // Upload to Cloudinary and store data
                            await this.handleImageUpload(file, imageElement);

                            modal.remove();
                            style.remove();
                            this.showToast('Image applied successfully', 'success');

                            // Re-add action buttons to ensure proper state
                            this.addActionButton(imageElement);
                        } catch (error) {
                            console.error('Failed to apply Unsplash image:', error);
                            this.showToast('Failed to apply image', 'error');
                        }
                    });
                    
                    imageGrid.appendChild(item);
                });
            } catch (error) {
                console.error('Unsplash search error:', error);
                this.showToast(error.message || 'Failed to search images', 'error');
            } finally {
                loadingSpinner.style.display = 'none';
            }
        };

        header.appendChild(searchInput);
        header.appendChild(searchButton);
        header.appendChild(closeBtn);
        content.appendChild(header);
        content.appendChild(imageGrid);
        content.appendChild(loadingSpinner);
        modal.appendChild(content);
        document.body.appendChild(modal);

        // Initial search with business name
        if (storedBusiness) {
            searchUnsplash(storedBusiness);
        }

        // Event listeners
        searchButton.onclick = () => searchUnsplash(searchInput.value.trim());
        searchInput.onkeyup = (e) => {
            if (e.key === 'Enter') {
                searchUnsplash(searchInput.value.trim());
            }
        };
        closeBtn.onclick = () => {
            modal.remove();
            style.remove();
        };
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.remove();
                style.remove();
            }
        };
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
                if (data.userId === this.userId && data.themeName === this.settings.themeName) {
                    if (!img.hasAttribute('data-original-src')) {
                        img.setAttribute('data-original-src', data.originalSrc);
                    }
                    img.src = data.currentSrc;
                    img.setAttribute('data-is-replaced', 'true');
                    img.setAttribute('data-image-id', imageId);

                    // Ensure we can find this image again using its path
                    const currentPath = this.getElementPath(img);
                    if (currentPath !== data.path) {
                        data.path = currentPath;
                        localStorage.setItem(`replaced_image_${imageId}`, JSON.stringify(data));
                    }

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

            // Collect all text content from the page including links
            const contentElements = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, a, .wp-block-heading, .wp-block-paragraph'));
            const contentMap = new Map();

            // Create a structured content map
            contentElements.forEach(element => {
                const tag = element.tagName.toLowerCase();
                const text = element.textContent.trim();
                const isLink = tag === 'a';
                if (text) {
                    if (!contentMap.has(tag)) {
                        contentMap.set(tag, []);
                    }
                    contentMap.get(tag).push({
                        element: element,
                        text: text,
                        path: this.getElementPath(element),
                        isLink: isLink,
                        href: isLink ? element.href : null
                    });
                }
            });

            // Create a structured prompt
            const contentStructure = Array.from(contentMap.entries()).map(([tag, items]) => ({
                tag: tag,
                items: items.map(item => ({
                    text: item.text,
                    type: tag,
                    isLink: item.isLink,
                    href: item.href
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
                            content: `You are an AI that creates website content. Generate content for a ${business} website. 
                            Use this specific prompt for guidance: ${storedPrompt}. 
                            Maintain the exact same structure and formatting. 
                            For links (<a> tags), generate appropriate text while preserving their original purpose and context.
                            Return ONLY a valid JSON array with the exact same structure as provided, without any markdown formatting or code blocks.`
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
                        await new Promise(resolve => setTimeout(resolve, 700));
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
                // Remove any existing generated content for this element
                const existingId = element.getAttribute('data-content-id');
                if (existingId) {
                    localStorage.removeItem(`generated_content_${existingId}`);
                }

                // Update element with new content
                element.innerHTML = newContent;
                this.showToast('Content generated successfully', 'success');

                // Store only once with the most specific path
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
        `;
        
        const img = document.createElement('img');
        img.src = imageElement.src;
        img.alt = imageElement.alt;
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
        modal.className = 'theme-info-modal';
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
        content.className = 'theme-info-content';
        content.style.cssText = `
            background: white;
            border-radius: 12px;
            width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            padding: 24px;
        `;

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
            &:hover {
                color: #000;
            }
        `;

        // Theme Info Sections
        const sections = [
            {
                title: 'Theme Information',
                items: [
                    { label: 'Name', value: this.settings.themeName },
                    { label: 'Version', value: this.settings.themeVersion },
                    { label: 'Author', value: this.settings.themeAuthor },
                    { label: 'Description', value: this.settings.themeDescription }
                ]
            },
            {
                title: 'Font Information',
                items: [
                    { label: 'Headings Font', value: this.getComputedFontFamily('h1, h2, h3, h4, h5, h6') },
                    { label: 'Body Font', value: this.getComputedFontFamily('body') }
                ]
            },
            {
                title: 'Color Scheme',
                items: [
                    { label: 'Primary Color', value: this.getComputedStyle('--wp--preset--color--primary', '#000000') },
                    { label: 'Secondary Color', value: this.getComputedStyle('--wp--preset--color--secondary', '#000000') },
                    { label: 'Background Color', value: this.getComputedStyle('body', '#ffffff', 'backgroundColor') },
                    { label: 'Text Color', value: this.getComputedStyle('body', '#000000', 'color') }
                ]
            },
            {
                title: 'Layout Information',
                items: [
                    { label: 'Content Width', value: this.getComputedStyle('body', 'auto', 'maxWidth') },
                    { label: 'Spacing Unit', value: this.getComputedStyle('--wp--style--block-gap', '20px') }
                ]
            }
        ];

        sections.forEach(section => {
            const sectionEl = document.createElement('div');
            sectionEl.style.cssText = `
                margin-bottom: 24px;
                &:last-child {
                    margin-bottom: 0;
                }
            `;

            const title = document.createElement('h3');
            title.textContent = section.title;
            title.style.cssText = `
                font-size: 18px;
                font-weight: 600;
                color: #1e1e1e;
                margin: 0 0 16px 0;
                padding-bottom: 8px;
                border-bottom: 1px solid #e0e0e0;
            `;

            const grid = document.createElement('div');
            grid.style.cssText = `
                display: grid;
                grid-template-columns: 140px 1fr;
                gap: 12px 16px;
            `;

            section.items.forEach(item => {
                const label = document.createElement('div');
                label.textContent = item.label;
                label.style.cssText = `
                    font-weight: 500;
                    color: #666;
                    font-size: 14px;
                `;

                const value = document.createElement('div');
                value.textContent = item.value || 'Not specified';
                value.style.cssText = `
                    font-size: 14px;
                    color: #1e1e1e;
                `;

                grid.appendChild(label);
                grid.appendChild(value);
            });

            sectionEl.appendChild(title);
            sectionEl.appendChild(grid);
            content.appendChild(sectionEl);
        });

        content.appendChild(closeBtn);
        modal.appendChild(content);
        document.body.appendChild(modal);

        const closeModal = () => modal.remove();
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
        });
    }

    getComputedFontFamily(selector) {
        const element = document.querySelector(selector);
        if (!element) return 'Not found';
        const style = window.getComputedStyle(element);
        return style.fontFamily;
    }

    getComputedStyle(selector, fallback, property = null) {
        if (selector.startsWith('--')) {
            const value = getComputedStyle(document.documentElement).getPropertyValue(selector);
            return value.trim() || fallback;
        }
        const element = document.querySelector(selector);
        if (!element) return fallback;
        const style = window.getComputedStyle(element);
        return property ? style[property] : fallback;
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
        const storedBusiness = localStorage.getItem('theme_preview_business') || '';
        const storedPrompt = localStorage.getItem('theme_preview_prompt') || '';

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
            <h2 style="margin: 0 0 24px; font-size: 1.5rem; color: #333;">Generate All Content</h2>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Business Name</label>
                <input type="text" value="${storedBusiness}" placeholder="Enter business name" style="
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
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Prompt</label>
                <textarea placeholder="Enter your prompt for content generation" style="
                    width: 100%;
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
                ">Generate All</button>
            </div>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);

        const businessInput = content.querySelector('input');
        const promptTextarea = content.querySelector('textarea');
        const generateBtn = content.querySelector('.generate-btn');
        const cancelBtn = content.querySelector('.cancel-btn');

        generateBtn.onclick = async () => {
            const business = businessInput.value.trim();
            const prompt = promptTextarea.value.trim();
            
            if (business && prompt) {
                // Store values in localStorage
                localStorage.setItem('theme_preview_business', business);
                localStorage.setItem('theme_preview_prompt', prompt);
                
                modal.remove();
                await this.generatePageContent(business, prompt);
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

        // Auto-focus the business input if empty
        if (!storedBusiness) {
            businessInput.focus();
        }
    }

    // Individual content generation modal
    showGenerateModal(element) {
        const storedBusiness = localStorage.getItem('theme_preview_business') || '';

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
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Business Name</label>
                <input type="text" value="${storedBusiness}" placeholder="Enter business name" style="
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
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Current Content</label>
                <div style="
                    padding: 10px;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    background: #f8f9fa;
                    font-size: 14px;
                    margin-bottom: 16px;
                    color: #666;
                ">${element.textContent.trim() || 'No content'}</div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Prompt for this Content</label>
                <textarea placeholder="Enter a specific prompt for this content..." style="
                    width: 100%;
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
        const generateBtn = content.querySelector('.generate-btn');
        const cancelBtn = content.querySelector('.cancel-btn');

        generateBtn.onclick = async () => {
            const business = businessInput.value.trim();
            const prompt = promptTextarea.value.trim();
            
            if (business && prompt) {
                // Only store business name in localStorage
                localStorage.setItem('theme_preview_business', business);
                
                modal.remove();

                try {
                    // Remove any existing content IDs and data
                    const existingId = element.getAttribute('data-content-id');
                    if (existingId) {
                        localStorage.removeItem(`generated_content_${existingId}`);
                        localStorage.removeItem(`edited_content_${existingId}`);
                        element.removeAttribute('data-content-id');
                        element.removeAttribute('data-is-generated');
                        element.removeAttribute('data-is-edited');
                    }

                    // Generate new content with temporary prompt
                    const newContent = await this.generateContent(business, element, prompt);
                    
                    // Store the generated content with temporary prompt
                    const contentId = this.generateContentId(element);
                    const data = {
                        content: newContent,
                        prompt: prompt,
                        timestamp: Date.now(),
                        userId: this.userId,
                        path: this.getElementPath(element),
                        themeName: this.settings.themeName,
                        type: 'generated'
                    };
                    
                    localStorage.setItem(`generated_content_${contentId}`, JSON.stringify(data));
                    element.setAttribute('data-content-id', contentId);
                    element.setAttribute('data-is-generated', 'true');
                    
                    this.showToast('Content generated successfully', 'success');
                } catch (error) {
                    console.error('Failed to generate content:', error);
                    this.showToast('Failed to generate content', 'error');
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

        // Auto-focus the business input if empty
        if (!storedBusiness) {
            businessInput.focus();
        }
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

    // Function to show link edit modal
    showLinkEditModal(element) {
        const linkId = element.getAttribute('data-link-id') || this.generateLinkId(element);
        element.setAttribute('data-link-id', linkId);

        // Get stored link data if it exists
        const storedData = localStorage.getItem(`edited_link_${linkId}`);
        const linkData = storedData ? JSON.parse(storedData) : null;

        const modal = document.createElement('div');
        modal.className = 'link-edit-modal';
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
            z-index: 99999;
        `;

        const content = document.createElement('div');
        content.className = 'link-edit-modal-content';
        content.style.cssText = `
            background: white;
            border-radius: 12px;
            width: 500px;
            padding: 24px;
            position: relative;
        `;

        const header = document.createElement('div');
        header.style.cssText = `
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        `;
        header.innerHTML = '<h3 style="margin: 0; font-size: 18px;">Edit Link Text</h3>';

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
        `;
        header.appendChild(closeBtn);

        const form = document.createElement('div');
        form.style.cssText = `
            display: flex;
            flex-direction: column;
            gap: 16px;
        `;

        // Text input field only
        const textGroup = document.createElement('div');
        textGroup.style.cssText = 'display: flex; flex-direction: column; gap: 8px;';
        textGroup.innerHTML = `
            <label style="font-weight: 500; color: #333; font-size: 14px;">Link Text</label>
            <input type="text" value="${element.textContent}" style="
                padding: 8px 12px;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                font-size: 14px;
                &:focus {
                    outline: none;
                    border-color: #2196F3;
                    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
                }
            ">
        `;

        const buttonGroup = document.createElement('div');
        buttonGroup.style.cssText = `
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
        `;

        const cancelBtn = document.createElement('button');
        cancelBtn.textContent = 'Cancel';
        cancelBtn.style.cssText = `
            padding: 8px 16px;
            border: 1px solid #e0e0e0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            &:hover {
                background: #f5f5f5;
            }
        `;

        const saveBtn = document.createElement('button');
        saveBtn.textContent = 'Save';
        saveBtn.style.cssText = `
            padding: 8px 16px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            &:hover {
                background: #1976D2;
            }
        `;

        form.appendChild(textGroup);
        buttonGroup.appendChild(cancelBtn);
        buttonGroup.appendChild(saveBtn);
        form.appendChild(buttonGroup);

        content.appendChild(header);
        content.appendChild(form);
        modal.appendChild(content);
        document.body.appendChild(modal);

        // Get input element
        const textInput = textGroup.querySelector('input');

        // Event handlers
        const closeModal = () => modal.remove();

        closeBtn.onclick = closeModal;
        cancelBtn.onclick = closeModal;
        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        };

        saveBtn.onclick = () => {
            const newText = textInput.value;

            // Save link data with hash
            const linkId = this.saveLinkToStorage(element, newText);

            // Update the link text only
            element.textContent = newText;
            element.setAttribute('data-is-edited', 'true');
            element.setAttribute('data-link-id', linkId);
            element.setAttribute('data-content-hash', this.generateContentHash(newText));

            this.showToast('Link text updated successfully', 'success');
            closeModal();
        };

        // Add keyboard shortcuts
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
            }
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                saveBtn.click();
                document.removeEventListener('keydown', escHandler);
            }
        });

        // Focus the text input
        textInput.focus();
        textInput.select();
    }

    generateLinkId(element) {
        const path = this.getElementPath(element);
        const text = element.textContent.trim();
        const timestamp = Date.now();
        
        const idString = `${path}_${text}_${timestamp}_${this.userId}`;
        let hash = 0;
        for (let i = 0; i < idString.length; i++) {
            const char = idString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return `link_${Math.abs(hash)}`;
    }

    saveLinkToStorage(element, text) {
        const linkId = element.getAttribute('data-link-id') || this.generateLinkId(element);
        const linkData = {
            text: text,
            path: this.getElementPath(element),
            timestamp: Date.now(),
            userId: this.userId,
            hash: this.generateContentHash(text)
        };
        localStorage.setItem(`edited_link_${linkId}`, JSON.stringify(linkData));
        return linkId;
    }

    generateContentHash(content) {
        let hash = 0;
        for (let i = 0; i < content.length; i++) {
            const char = content.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return `hash_${Math.abs(hash)}`;
    }

    recoverStoredLinks() {
        // Get all stored link data
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('edited_link_')) {
                const linkData = JSON.parse(localStorage.getItem(key));
                if (linkData.userId === this.userId) {
                    const element = this.findElementByPath(linkData.path);
                    if (element && element.tagName === 'A') {
                        // Verify content hash
                        const currentHash = this.generateContentHash(linkData.text);
                        if (currentHash === linkData.hash) {
                            // Update text only
                            element.textContent = linkData.text;
                            element.setAttribute('data-is-edited', 'true');
                            element.setAttribute('data-link-id', key.replace('edited_link_', ''));
                            element.setAttribute('data-content-hash', linkData.hash);
                        }
                    }
                }
            }
        }
    }

    addLinkActionButtons() {
        document.querySelectorAll('a').forEach(link => {
            if (!this.shouldProcessLink(link)) return;
            this.addLinkActionButton(link);
        });
    }

    shouldProcessLink(link) {
        return !link.hasAttribute('data-has-actions') && 
               !link.closest('#wpadminbar, .theme-preview-toast, .content-actions');
    }

    addLinkActionButton(link) {
        link.setAttribute('data-has-actions', 'true');
        const wrapper = this.ensureWrapper(link);

        const actionsContainer = document.createElement('div');
        actionsContainer.className = 'link-actions-container';
        actionsContainer.style.cssText = `
            position: absolute;
            top: -30px;
            right: 0;
            display: flex;
            gap: 5px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 999;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        `;

        // Only Edit button
        const editButton = document.createElement('button');
        editButton.className = 'link-action-button edit-button';
        editButton.style.cssText = `
            background: none;
            border: none;
            border-radius: 4px;
            padding: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: #1e1e1e;
            transition: all 0.2s ease;
            &:hover {
                background: rgba(0,0,0,0.05);
            }
        `;
        
        editButton.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span>Edit Text</span>
        `;
        
        editButton.addEventListener('click', () => this.showLinkEditModal(link));
        actionsContainer.appendChild(editButton);

        wrapper.appendChild(actionsContainer);

        // Show/hide action buttons
        wrapper.addEventListener('mouseenter', () => {
            actionsContainer.style.opacity = '1';
            actionsContainer.style.visibility = 'visible';
        });
        wrapper.addEventListener('mouseleave', () => {
            actionsContainer.style.opacity = '0';
            actionsContainer.style.visibility = 'hidden';
        });
    }

    addContentActionButtons() {
        document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, .wp-block-heading, .wp-block-paragraph').forEach(element => {
            if (!this.shouldProcessContent(element)) return;
            this.addContentActionButton(element);
        });
    }

    shouldProcessContent(element) {
        return !element.hasAttribute('data-has-actions') && 
               !element.closest('#wpadminbar, .theme-preview-toast, .content-actions, .theme-preview-modal') &&
               element.tagName !== 'A';
    }

    addContentActionButton(element) {
        element.setAttribute('data-has-actions', 'true');
        const wrapper = this.ensureWrapper(element);

        const actionsContainer = document.createElement('div');
        actionsContainer.className = 'content-actions-container';
        actionsContainer.style.cssText = `
            position: absolute;
            top: -35px;
            right: 0;
            display: flex;
            gap: 8px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
            background: rgba(255, 255, 255, 0.98);
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(8px);
            transform: translateY(5px);
        `;

        const buttonStyle = `
            background: none;
            border: none;
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #1e1e1e;
            transition: all 0.2s ease;
            font-weight: 500;
            white-space: nowrap;
            &:hover {
                background: rgba(33, 150, 243, 0.1);
                color: #2196F3;
            }
            &:active {
                transform: scale(0.95);
            }
        `;

        const buttons = [
            {
                name: 'Edit',
                icon: '<path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
                onClick: () => this.showEditModal(element)
            },
            {
                name: 'Generate',
                icon: '<path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/>',
                onClick: () => this.showGenerateModal(element)
            }
        ];

        buttons.forEach(({name, icon, onClick}) => {
            const button = document.createElement('button');
            button.className = `content-action-button ${name.toLowerCase()}-button`;
            button.style.cssText = buttonStyle;
            button.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    ${icon}
                </svg>
                <span>${name}</span>
            `;
            
            button.addEventListener('click', onClick);
            actionsContainer.appendChild(button);
        });

        wrapper.appendChild(actionsContainer);

        wrapper.addEventListener('mouseenter', () => {
            actionsContainer.style.opacity = '1';
            actionsContainer.style.visibility = 'visible';
            actionsContainer.style.transform = 'translateY(0)';
        });

        wrapper.addEventListener('mouseleave', () => {
            actionsContainer.style.opacity = '0';
            actionsContainer.style.visibility = 'hidden';
            actionsContainer.style.transform = 'translateY(5px)';
        });
    }

    generateContentId(element) {
        const path = this.getElementPath(element);
        const timestamp = Date.now();
        
        const idString = `${path}_${timestamp}_${Math.random()}_${this.userId}`;
        let hash = 0;
        for (let i = 0; i < idString.length; i++) {
            const char = idString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        
        return `content_${timestamp}_${Math.abs(hash)}`;
    }
}

// Initialize when DOM is loaded
jQuery(document).ready(function($) {
    new ThemePreviewImageHandler();
}); 