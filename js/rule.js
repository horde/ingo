/**
 * Provides the javascript for the rule view.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @copyright  2014-2015 Horde LLC
 * @license    ASL (http://www.horde.org/licenses/apache)
 */

var IngoRule = {

    delete_condition: function(num)
    {
        document.getElementById('actionID').value = 'rule_delete';
        document.getElementById('conditionnumber').value = num;
        document.getElementById('rule').submit();
        return true;
    },

    onDomLoad: function()
    {
        var ruleForm = document.getElementById('rule');

        ['all', 'any'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    ruleForm.submit();
                });
            }
        });

        ruleForm.addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT') {
                e.preventDefault();
                ruleForm.submit();
            }
        });

        document.getElementById('rule_save').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('actionID').value = 'rule_save';
            ruleForm.submit();
        });

        document.getElementById('rule_cancel').addEventListener('click', function(e) {
            e.preventDefault();
            document.location.href = this.filtersurl;
        }.bind(this));
    }

};

document.addEventListener('DOMContentLoaded', IngoRule.onDomLoad.bind(IngoRule));
