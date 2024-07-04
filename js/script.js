(function($, Drupal) {

    Drupal.behaviors.idle_hands_logout = {
        attach: function (context, settings) {

            let localSettings;
            localSettings = jQuery.extend(true, {}, settings.idle_hands_logout);

            let maxInactivitySeconds = localSettings.maxInactivitySeconds;
            let inactivityDialogDuration = localSettings.inactivityDialogDuration;
            let documentTitle = localSettings.documentTitle;
            let dialogTitle = localSettings.dialogTitle;
            let dialogMessage = localSettings.dialogMessage;
            let dialogTimeRemainingLabel = localSettings.dialogTimeRemainingLabel;
            let stayLoggedInButtonText = localSettings.stayLoggedInButtonText;
            let logoutNowButtonText = localSettings.logoutNowButtonText;
            let showDialogTimer = localSettings.showDialogTimer;

            $.idleHands({
                activityEvents: 'click keypress scroll wheel mousewheel mousemove keyup touchmove',
                applicationId: 'idle_hands_logout',
                dialogMessage: dialogMessage,
                dialogTimeRemainingLabel: dialogTimeRemainingLabel,
                dialogTitle: dialogTitle,
                documentTitle: documentTitle,
                heartbeatCallback: (function (data, textStatus, jqXHR) {
                    console.log('pulse');
                }),
                heartbeatUrl: window.location.href,
                heartRate: 30,
                inactivityLogoutUrl: '/idle_hands_logout/1',
                inactivityDialogDuration: inactivityDialogDuration,
                localStoragePrefix: 'idle_hands_logout',
                logoutNowButtonText: logoutNowButtonText,
                manualLogoutUrl: '/idle_hands_logout/0',
                maxInactivitySeconds: maxInactivitySeconds,
                stayLoggedInButtonText: stayLoggedInButtonText,
                showDialogTimer: showDialogTimer
            });

        }
    }

})(jQuery, Drupal);
