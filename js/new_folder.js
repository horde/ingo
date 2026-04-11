/**
 * Provides the javascript for creating a new mailbox.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @copyright  2014-2015 Horde LLC
 * @license    ASL (http://www.horde.org/licenses/apache)
 */

var IngoNewFolder = {

    // Set in PHP code: folderprompt

    changeHandler: function(e)
    {
        var folder,
            elt = e.target,
            id = elt.id + '_new',
            newfolder = document.getElementById(id),
            sel = elt.options[elt.selectedIndex];

        if (!newfolder &&
            sel.classList.contains('flistCreate') &&
            (folder = window.prompt(this.folderprompt + '\n', '')) &&
            folder !== '') {
            this.setNewFolder(elt, folder);
            e.preventDefault();
        }
    },

    setNewFolder: function(elt, folder)
    {
        var sel,
            id = elt.id + '_new';

        elt.selectedIndex = elt.querySelector('.flistCreate').index;
        sel = elt.options[elt.selectedIndex];

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.id = id;
        hidden.name = id;
        hidden.value = folder;
        elt.after(hidden);

        sel.text = sel.text + ' [' + folder + ']';
    },

    onDomLoad: function()
    {
        document.querySelectorAll('.flistSelect').forEach(function(el) {
            el.addEventListener('change', this.changeHandler.bind(this));
        }, this);
    }

};

document.addEventListener('DOMContentLoaded', IngoNewFolder.onDomLoad.bind(IngoNewFolder));
