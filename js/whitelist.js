/**
 * Provides the javascript for the whitelist view.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @copyright  2014-2015 Horde LLC
 * @license    ASL (http://www.horde.org/licenses/apache)
 */

var IngoWhitelist = {

    onDomLoad: function()
    {
        document.getElementById('whitelist_return').addEventListener('click', function(e) {
            document.location.href = this.filtersurl;
            e.preventDefault();
        }.bind(this));
    }

};

document.addEventListener('DOMContentLoaded', IngoWhitelist.onDomLoad.bind(IngoWhitelist));
