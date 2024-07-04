<?php

declare(strict_types=1);

/**
 * @file
 * Contains \Drupal\idle_hands_logout\Service\IdleHandsLogoutService
 **/

namespace Drupal\idle_hands_logout\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\Url;

/**
 * Defines a IdleHandsLogout service.
 */
class IdleHandsLogoutService
{

    /**
     * The module handler service.
     *
     * @var \Drupal\Core\Extension\ModuleHandlerInterface
     */
    protected ModuleHandlerInterface $moduleHandler;

    /**
     * The configuration for idle_hands_logout
     * 
     * @var \Drupal\Core\Config\Config
     */
    protected Config $idleHandsLogoutSettings;

    /**
     * The configuration factory service.
     * 
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected ConfigFactoryInterface $configFactory;

    /**
     * The session.
     *
     * @var \Drupal\Core\Session\SessionManager
     */
    protected SessionManager $session;

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected AccountInterface $currentUser;

    /**
     * Constructs the RiseLogoutService
     * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
     *     The module handler.
     * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
     *     The config factory.
     * @param \Drupal\Core\Session\SessionManager $sessionManager
     *     The session.
     * @param \Drupal\Core\Session\AccountInterface $current_user
     *     Data of the current user
     */

    public function __construct(ModuleHandlerInterface $module_handler, ConfigFactoryInterface $configFactory, SessionManager $sessionManager, AccountInterface $current_user)
    {
        $this->moduleHandler           = $module_handler;
        $this->idleHandsLogoutSettings = $configFactory->get('idle_hands_logout.settings');
        $this->session                 = $sessionManager;
        $this->currentUser             = $current_user;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetLogoutUrl() {
        return $this->idleHandsLogoutSettings->get('targetLogoutUrl');
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckSamlAuth() {
        return $this->idleHandsLogoutSettings->get('checkSamlAuth');
    }

    /**
     * Standard logout for local accounts.
     */
    public function logout() {

        $user = $this->currentUser;

        $userIsAuthenticated = $user->isAuthenticated();
        $userIsAnonymous     = $user->isAnonymous();

        if (!$userIsAnonymous && $userIsAuthenticated) {
            // Destroy the current session.
            $this->moduleHandler->invokeAll('user_logout', [$user]);
            $this->session->clear();
            $user->setAccount(new AnonymousUserSession());
        }

    }

    /**
     * Logout for accounts authenticated using SAML Auth.
     * 
     * @param int    $showMessage
     * @param string $redirect_url
     */
    public function samlLogout(int $showMessage, string $redirect_url) {

        $user = $this->currentUser;

        $this->session->destroy();
        $user->setAccount(new AnonymousUserSession());
        $url = Url::fromUserInput(
            $redirect_url,
            [
                'absolute' => TRUE,
                'query' => [
                    'idle_hands_logout' => $showMessage,
                ],
            ]
        );
        $simplesaml = \Drupal::service('simplesamlphp_auth.manager');
        $simplesaml->logout($url->toString());

    }

}
