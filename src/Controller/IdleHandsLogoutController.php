<?php

declare(strict_types=1);

/**
 * @file
 * Contains \Drupal\idle_hands_logout\Controller\IdleHandsLogoutController
 **/

namespace Drupal\idle_hands_logout\Controller;

use Drupal\idle_hands_logout\Service\IdleHandsLogoutService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * Returns responses for Idle Hands Logout module routes.
 */
class IdleHandsLogoutController extends ControllerBase
{

    /**
     * The idle hands logout service.
     *
     * @var \Drupal\idle_hands_logout\Service\IdleHandsLogoutService
     */
    protected $idleHandsLogoutService;

    /**
     * IdleHandsLogoutController constructor.
     *
     * @param IdleHandsLogoutService $idleHandsLogoutService
     */
    public function __construct(IdleHandsLogoutService $idleHandsLogoutService)
    {
        $this->idleHandsLogoutService = $idleHandsLogoutService;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('idle_hands_logout.service'),
        );
    }

    /**
     * Idle Hands Logout logout.
     * 
     * @param int $showMessage
     * 
     * @return RedirectResponse|null
     */
    public function idleHandsLogoutLogout(int $showMessage = 1): ?RedirectResponse
    {

        $redirect_url  = $this->idleHandsLogoutService->getTargetLogoutUrl();
        $checkSamlAuth = $this->idleHandsLogoutService->getCheckSamlAuth();

        if($checkSamlAuth) {
            $simplesaml          = \Drupal::service('simplesamlphp_auth.manager');
            $samlIsActive        = $simplesaml->isActivated();
            $samlIsAuthenticated = $simplesaml->isAuthenticated();

            if ($samlIsActive && $samlIsAuthenticated) {
                $this->idleHandsLogoutService->samlLogout($showMessage, $redirect_url);
                return null;
            }
        }

        $this->idleHandsLogoutService->logout();

        $url = Url::fromUserInput(
            $redirect_url,
            [
                'absolute' => TRUE,
                'query' => [
                    'idle_hands_logout' => $showMessage,
                ],
            ]
        );

        return new RedirectResponse($url->toString());

    }

}
