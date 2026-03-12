/**
 * Ingo Responsive Rules List
 * Client-side search/filter functionality
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 */

(function() {
    'use strict';

    /**
     * Initialize rule filter functionality
     */
    function initRuleFilter() {
        const filterInput = document.getElementById('rule-filter');
        const clearBtn = document.querySelector('.btn-clear');
        const ruleItems = document.querySelectorAll('.rule-item');
        const emptyStateFiltered = document.querySelector('.empty-state-filtered');

        if (!filterInput || !ruleItems.length) {
            return;
        }

        /**
         * Filter rules by search query
         */
        function filterRules() {
            const query = filterInput.value.toLowerCase().trim();
            let visibleCount = 0;

            ruleItems.forEach(item => {
                const name = item.getAttribute('data-rule-name').toLowerCase();
                const matches = !query || name.includes(query);

                item.hidden = !matches;

                if (matches) {
                    visibleCount++;
                }
            });

            // Show/hide clear button
            if (clearBtn) {
                clearBtn.hidden = !query;
            }

            // Show/hide filtered empty state
            if (emptyStateFiltered) {
                emptyStateFiltered.hidden = visibleCount > 0;
            }
        }

        /**
         * Clear search filter
         */
        function clearFilter() {
            filterInput.value = '';
            filterRules();
            filterInput.focus();
        }

        // Event listeners
        filterInput.addEventListener('input', filterRules);

        if (clearBtn) {
            clearBtn.addEventListener('click', clearFilter);
        }

        // Clear on Escape key
        filterInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                clearFilter();
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRuleFilter);
    } else {
        initRuleFilter();
    }

})();
