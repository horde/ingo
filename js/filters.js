/**
 * Provides the javascript for the filters view.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @copyright  2014-2015 Horde LLC
 * @license    ASL (http://www.horde.org/licenses/apache)
 */

var IngoFilters = {

    dragSrcEl: null,

    // Extracts the sequence ID from a div's id attribute using the same
    // format as the original Sortable: /^[^_\-](?:[A-Za-z0-9]*)[_](.*)$/
    getSequence: function(container)
    {
        var re = /^[^_\-](?:[A-Za-z0-9]*)[_](.*)$/;
        return Array.from(container.children).map(function(el) {
            var m = el.id.match(re);
            return m ? m[1] : el.id;
        });
    },

    handleDragStart: function(e)
    {
        IngoFilters.dragSrcEl = this;
        this.style.opacity = '0.4';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', '');
    },

    handleDragOver: function(e)
    {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    },

    handleDrop: function(e)
    {
        e.preventDefault();
        if (IngoFilters.dragSrcEl !== this) {
            var list = this.parentNode,
                children = Array.from(list.children),
                srcIndex = children.indexOf(IngoFilters.dragSrcEl),
                destIndex = children.indexOf(this);
            if (srcIndex < destIndex) {
                list.insertBefore(IngoFilters.dragSrcEl, this.nextSibling);
            } else {
                list.insertBefore(IngoFilters.dragSrcEl, this);
            }
            Horde.stripeElement('filterslist');
            HordeCore.doAction(
                'reSortFilters',
                {
                    sort: JSON.stringify(IngoFilters.getSequence(list))
                }
            );
        }
    },

    handleDragEnd: function()
    {
        this.style.opacity = '1';
    },

    initSortable: function(container)
    {
        Array.from(container.children).forEach(function(el) {
            if (el.tagName === 'DIV') {
                el.draggable = true;
                el.addEventListener('dragstart', IngoFilters.handleDragStart);
                el.addEventListener('dragover', IngoFilters.handleDragOver);
                el.addEventListener('drop', IngoFilters.handleDrop);
                el.addEventListener('dragend', IngoFilters.handleDragEnd);
            }
        });
    },

    onDomLoad: function()
    {
        var applyBtn = document.getElementById('apply_filters');
        if (applyBtn) {
            applyBtn.addEventListener('click', function(e) {
                document.getElementById('actionID').value = 'apply_filters';
                document.getElementById('filters').submit();
                e.preventDefault();
            });
        }

        var filterslist = document.getElementById('filterslist');
        if (filterslist) {
            this.initSortable(filterslist);
        }
    }

};

document.addEventListener('DOMContentLoaded', IngoFilters.onDomLoad.bind(IngoFilters));
