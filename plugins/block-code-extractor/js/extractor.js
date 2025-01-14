(function() {
    window.addEventListener('load', function() {
        // Wait for WordPress to be ready
        if (typeof wp === 'undefined' || !wp.data || !wp.blocks) {
            console.error('WordPress editor not found');
            return;
        }

        function addButtons() {
            // Remove existing buttons if any
            const existingButtons = document.querySelector('.block-code-extractor-buttons');
            if (existingButtons) {
                existingButtons.remove();
            }

            // Create wrapper for our buttons
            const wrapper = document.createElement('div');
            wrapper.className = 'block-code-extractor-buttons';

            // Add "Copy All" button
            const copyAllButton = document.createElement('button');
            copyAllButton.className = 'components-button is-primary';
            copyAllButton.innerHTML = '<span class="dashicons dashicons-admin-page" style="margin-right: 8px;"></span>Copy All';
            copyAllButton.onclick = async function() {
                try {
                    const blocks = wp.data.select('core/block-editor').getBlocks();
                    const serializedBlocks = wp.blocks.serialize(blocks);
                    await navigator.clipboard.writeText(serializedBlocks);
                    
                    wp.data.dispatch('core/notices').createNotice(
                        'success',
                        'All blocks copied!',
                        { type: 'snackbar' }
                    );
                } catch (error) {
                    wp.data.dispatch('core/notices').createNotice(
                        'error',
                        'Failed to copy blocks',
                        { type: 'snackbar' }
                    );
                }
            };

            // Add "Copy Groups" button
            const copyGroupsButton = document.createElement('button');
            copyGroupsButton.className = 'components-button is-secondary';
            copyGroupsButton.innerHTML = '<span class="dashicons dashicons-groups" style="margin-right: 8px;"></span>Copy Groups';
            copyGroupsButton.onclick = async function() {
                try {
                    const blocks = wp.data.select('core/block-editor').getBlocks();
                    const seenClientIds = new Set(); // Track seen blocks to prevent duplicates
                    const groupBlocks = [];
                    
                    function hasGroupChild(block) {
                        if (block.name === 'core/group') return true;
                        if (block.innerBlocks && block.innerBlocks.length > 0) {
                            return block.innerBlocks.some(hasGroupChild);
                        }
                        return false;
                    }

                    function processBlock(block) {
                        if (block.name === 'core/group') {
                            if (!seenClientIds.has(block.clientId)) {
                                seenClientIds.add(block.clientId);
                                return block;
                            }
                            return null;
                        }

                        // If this block contains a group, process it with its structure
                        if (block.innerBlocks && block.innerBlocks.length > 0 && hasGroupChild(block)) {
                            const newBlock = { ...block };
                            newBlock.innerBlocks = block.innerBlocks
                                .map(processBlock)
                                .filter(b => b !== null);
                            return newBlock;
                        }

                        return null;
                    }

                    // Process all blocks
                    blocks.forEach(block => {
                        const processedBlock = processBlock(block);
                        if (processedBlock) {
                            groupBlocks.push(processedBlock);
                        }
                    });
                    
                    if (groupBlocks.length === 0) {
                        wp.data.dispatch('core/notices').createNotice(
                            'info',
                            'No group blocks found',
                            { type: 'snackbar' }
                        );
                        return;
                    }
                    
                    const serializedGroups = wp.blocks.serialize(groupBlocks);
                    await navigator.clipboard.writeText(serializedGroups);
                    
                    wp.data.dispatch('core/notices').createNotice(
                        'success',
                        `${groupBlocks.length} group blocks copied!`,
                        { type: 'snackbar' }
                    );
                } catch (error) {
                    wp.data.dispatch('core/notices').createNotice(
                        'error',
                        'Failed to copy group blocks',
                        { type: 'snackbar' }
                    );
                }
            };

            // Add buttons to wrapper
            wrapper.appendChild(copyAllButton);
            wrapper.appendChild(copyGroupsButton);

            // Add wrapper directly to body
            document.body.appendChild(wrapper);
            return true;
        }

        // Try to add buttons immediately and keep checking
        const interval = setInterval(() => {
            if (document.body && wp.data && wp.blocks) {
                addButtons();
                // Keep checking every 2 seconds in case the editor reloads
                setTimeout(addButtons, 2000);
            }
        }, 2000);
    });
})();