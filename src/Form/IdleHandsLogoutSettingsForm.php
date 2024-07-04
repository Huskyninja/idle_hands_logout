<?php

declare(strict_types=1);

/**
 * @file
 * Contains \Drupal\idle_hands_logout\Form\idleHandsLogoutSettingsForm
 **/

namespace Drupal\idle_hands_logout\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for use with configuration of Idle Hands Logout
 */
class IdleHandsLogoutSettingsForm extends ConfigFormBase
{

    /**
     * Defines the timeout limit.
     *
     * @var int
     *
     * Maximum 32-bit integer that can be handled by setInterval.
     * See https://developer.mozilla.org/en-US/docs/Web/API/setInterval
     */
    private $maxAllowedTimeout = 2147483;

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'idle_hands_logout_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'idle_hands_logout.settings'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        // Turn off browser based HTML5 form validation in favor of Inline Form Errors core module
        $form['#attributes']['novalidate'] = 'novalidate';

        $config = $this->config('idle_hands_logout.settings');

        $form['enabled'] = [
            '#type'          => 'checkbox',
            '#title'         => $this->t('Enable Idle Hands Logout functions'),
            '#default_value' => $config->get('enabled'),
            '#weight'        => -20,
            '#description'   => $this->t(
                'Enable the Idle Hands Timeout module on the site to track user inactivity.'
            ),
        ];

        $form['maxInactivitySeconds'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Timeout for inactivity in seconds'),
            '#default_value' => $config->get('maxInactivitySeconds'),
            '#weight'        => -16,
            '#size'          => 10,
            '#required'      => TRUE,
            '#description'   => $this->t(
                'The length of the inactivity, in seconds, before showing the logout dialog.
                Value is total time and includes the time displaying the dialog.'
            ),
        ];

        $form['inactivityDialogDuration'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Time displaying the dialog in seconds'),
            '#default_value' => $config->get('inactivityDialogDuration'),
            '#weight'        => -12,
            '#size'          => 10,
            '#required'      => TRUE,
            '#description'   => $this->t(
                'The number of seconds giving the user the chance to extend an inactive session, with the dialog showing.
                Must be less then the Timeout for inactivity value.'
            ),
        ];

        $form['targetLogoutUrl'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Inactivity redirect URL'),
            '#default_value' => $config->get('targetLogoutUrl'),
            '#weight'        => -8,
            '#required'      => TRUE,
            '#description'   => $this->t(
                'The internal URL to send users when they have logged out.'
            ),
        ];

        $form['checkSamlAuth'] = [
            '#type'          => 'checkbox',
            '#title'         => $this->t('Use SAML Auth'),
            '#default_value' => $config->get('checkSamlAuth'),
            '#weight'        => -4,
            '#description'   => $this->t(
                'Check if a user is logged in using SAML Auth, and if so use the SAML Auth process to log them out.'
            ),
        ];

        $form['documentTitle'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Flashing title text'),
            '#default_value' => $config->get('documentTitle'),
            '#weight'        => 0,
            '#description'   => $this->t(
                'The text that will flash in the browser tab along with the document title.'
            ),
        ];

        $form['dialogTitle'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Dialog title'),
            '#default_value' => $config->get('dialogTitle'),
            '#weight'        => 4,
            '#description'   => $this->t(
                'The title text for the logout dialog'
            ),
        ];

        $form['dialogMessage'] = [
            '#type'          => 'textarea',
            '#title'         => $this->t('Dialog message'),
            '#default_value' => $config->get('dialogMessage'),
            '#weight'        => 8,
            '#description'   => $this->t(
                'The message displayed in the logout dialog.'
            ),
        ];

        $form['showDialogTimer'] = [
            '#type'          => 'checkbox',
            '#title'         => $this->t('Show time remaining countdown'),
            '#default_value' => $config->get('showDialogTimer'),
            '#weight'        => 10,
            '#description'   => $this->t(
                'Show the time remainig until logout, in seconds, on the dialog.'
            ),
        ];

        $form['dialogTimeRemainingLabel'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Time remaining message'),
            '#default_value' => $config->get('dialogTimeRemainingLabel'),
            '#weight'        => 12,
            '#description'   => $this->t(
                'The message for the timeout remaining, in seconds, within the dialog.'
            ),
        ];

        $form['stayLoggedInButtonText'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Continue session button text'),
            '#default_value' => $config->get('stayLoggedInButtonText'),
            '#weight'        => 16,
            '#description'   => $this->t(
                'The text used for the button in the dialog for a user to continue their session.'
            ),
        ];

        $form['logoutNowButtonText'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Log out now button text'),
            '#default_value' => $config->get('logoutNowButtonText'),
            '#weight'        => 20,
            '#description'   => $this->t(
                'The text used for the button in the dialog for a user end their session and log out immediately.'
            ),
        ];

        return parent::buildForm($form, $form_state);

    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {

        $values = $form_state->getValues();

        // maxInactivitySeconds
        $maxInactivitySeconds = $values['maxInactivitySeconds'];
        if ($maxInactivitySeconds <= 60) {
            $form_state->setErrorByName(
                'maxInactivitySeconds',
                $this->t('Timeout for inactivity must be greater than 60 seconds.')
            );
        } elseif ($maxInactivitySeconds >= $this->maxAllowedTimeout) {
            $form_state->setErrorByName(
                'maxInactivitySeconds',
                $this->t(
                    'Timeout for inactivity must be an integer less than the maximum timeout of %limit seconds.',
                    ['%limit' => $this->maxAllowedTimeout]
                )
            );
        } elseif (
            !is_numeric($maxInactivitySeconds)
            || (int) $maxInactivitySeconds != $maxInactivitySeconds
            || $maxInactivitySeconds <= 60
        ) {
            $form_state->setErrorByName(
                'maxInactivitySeconds',
                $this->t('The timeout must be an integer greater than 60.')
            );
        }

        // inactivityDialogDuration
        $inactivityDialogDuration = $values['inactivityDialogDuration'];
        if ($inactivityDialogDuration >= $maxInactivitySeconds) {
            $form_state->setErrorByName(
                'inactivityDialogDuration',
                $this->t(
                    'The modal display time must be less than the current timeout for inactivity, %timeout seconds.',
                    ['%timeout' => $maxInactivitySeconds]
                )
            );
        } elseif ($inactivityDialogDuration >= $this->maxAllowedTimeout) {
            $form_state->setErrorByName(
                'inactivityDialogDuration',
                $this->t(
                    'The modal display time must be an integer less than the maximum timeout of %limit seconds.',
                    ['%limit' => $this->maxAllowedTimeout]
                )
            );
        } elseif (
            !is_numeric($inactivityDialogDuration)
            || (int) $inactivityDialogDuration != $inactivityDialogDuration
            || $inactivityDialogDuration < 60
        ) {
            $form_state->setErrorByName(
                'inactivityDialogDuration',
                $this->t('The modal display time must be an integer greater than or equal to 60.')
            );
        }

        // targetLogoutUrl
        $targetLogoutUrl = $values['targetLogoutUrl'];
        if (strpos($targetLogoutUrl, '/') !== 0) {
            $form_state->setErrorByName(
                'targetLogoutUrl', 
                $this->t("Redirect URL at logout :targetLogoutUrl must begin with a '/'", [':targetLogoutUrl' => $targetLogoutUrl])
            );
        }

        // dialogMessage
        $dialogMessage = trim($values['dialogMessage']);
        if (empty($dialogMessage)) {
            $form_state->setValue('dialogMessage', 'Your session is about to expire due to inactivity.');
        }

        // dialogTimeRemainingLabel
        $dialogTimeRemainingLabel = trim($values['dialogTimeRemainingLabel']);
        if (empty($dialogTimeRemainingLabel)) {
            $form_state->setValue('dialogTimeRemainingLabel', 'Time remaining');
        }

        // dialogTitle
        $dialogTitle = trim($values['dialogTitle']);
        if (empty($dialogTitle)) {
            $form_state->setValue('dialogTitle', 'Session Expiration Warning');
        }

        // logoutNowButtonText
        $logoutNowButtonText = trim($values['logoutNowButtonText']);
        if (empty($logoutNowButtonText)) {
            $form_state->setValue('logoutNowButtonText', 'Logout Now');
        }

        // stayLoggedInButtonText
        $stayLoggedInButtonText = trim($values['stayLoggedInButtonText']);
        if (empty($stayLoggedInButtonText)) {
            $form_state->setValue('stayLoggedInButtonText', 'Stay Logged In');
        }

        parent::validateForm($form, $form_state);

    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $values             = $form_state->getValues();
        $idleHandsLogoutSettings = $this->config('idle_hands_logout.settings');

        $idleHandsLogoutSettings
            ->set('enabled', $values['enabled'])
            ->set('maxInactivitySeconds', $values['maxInactivitySeconds'])
            ->set('inactivityDialogDuration', $values['inactivityDialogDuration'])
            ->set('targetLogoutUrl', $values['targetLogoutUrl'])
            ->set('checkSamlAuth', $values['checkSamlAuth'])
            ->set('documentTitle', $values['documentTitle'])
            ->set('dialogTitle', $values['dialogTitle'])
            ->set('dialogMessage', $values['dialogMessage'])
            ->set('showDialogTimer', $values['showDialogTimer'])
            ->set('dialogTimeRemainingLabel', $values['dialogTimeRemainingLabel'])
            ->set('stayLoggedInButtonText', $values['stayLoggedInButtonText'])
            ->set('logoutNowButtonText', $values['logoutNowButtonText'])
            ->save();

        parent::submitForm($form, $form_state);

    }

}
