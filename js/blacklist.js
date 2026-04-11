/**
 * Provides the javascript for the blacklist view.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @copyright  2014-2015 Horde LLC
 * @license    ASL (http://www.horde.org/licenses/apache)
 */

var IngoBlacklist = {

    // Vars used and defaulting to null/false:
    //   filtersurl

    onDomLoad: function()
    {
        document.getElementById('actionvalue').addEventListener('change', function(e) {
            if (e.target.value) {
                document.getElementById('action_folder').value = 1;
            }
        });

        document.getElementById('blacklist_return').addEventListener('click', function(e) {
            document.location.href = this.filtersurl;
            e.preventDefault();
        }.bind(this));
    }
};

document.addEventListener('DOMContentLoaded', IngoBlacklist.onDomLoad.bind(IngoBlacklist));
