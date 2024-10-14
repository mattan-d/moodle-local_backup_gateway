/**
 *  Local_backup_gateway file description here.
 *
 * @package
 *  @copyright  2023 mattandor <mattan@centricapp.co.il>
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  */

define(['jquery'], function($) {
    return {
        checkstate: function(waitmessage) {
            console.log('Local Remote Backup Provider: AMD Ready.');

            $('.backupremote a').on('click', function(e) {
                require(['core/notification'], function(notification) {
                    notification.addNotification({
                        message: waitmessage,
                        type: 'info'
                    });
                });
            });
        }
    };
});