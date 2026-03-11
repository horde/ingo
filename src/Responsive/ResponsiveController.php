<?php

declare(strict_types=1);

namespace Horde\Ingo\Responsive;

use Horde\Core\View\ResponsiveTemplateView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Responsive Rules Controller
 *
 * Modern mobile-first mail filtering rules interface.
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category  Horde
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 */
class ResponsiveController implements RequestHandlerInterface
{
    /**
     * Handle request
     *
     * @param ServerRequestInterface $request The request object
     *
     * @return ResponseInterface The response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        global $injector;

        // Get route match from router (stored in 'route' attribute by AppRouter)
        $route = $request->getAttribute('route', []);

        // Determine action based on route parameters
        if (isset($route['uid'])) {
            return $this->viewRule($request, $route['uid']);
        }

        return $this->index($request);
    }

    /**
     * Display rules list
     *
     * @param ServerRequestInterface $request The request object
     *
     * @return ResponseInterface The response
     */
    private function index(ServerRequestInterface $request): ResponseInterface
    {
        global $registry, $session, $injector;

        // Get responsive assets helper
        $responsiveAssets = new \Horde\Core\Assets\ResponsiveAssets($registry);

        // Load rules from storage
        $storage = $injector->getInstance('Ingo_Factory_Storage')->create();

        // Get filters matching current script categories
        $filters = \Ingo_Storage_FilterIterator_Match::create(
            $storage,
            $session->get('ingo', 'script_categories')
        );

        // Build rules array for template
        $rules = [];
        foreach ($filters as $filter) {
            // Skip disabled rules
            if ($filter->disable) {
                continue;
            }

            // Determine icon based on rule type
            $icon = $this->getRuleIcon($filter);

            $rules[] = [
                'uid' => $filter->uid,
                'name' => $filter->name,
                'icon' => $icon,
            ];
        }

        // Build topbar
        $topbar = $this->renderTopbar();

        // Prepare view data
        $viewData = [
            'topbar' => $topbar,
            'rules' => $rules,
            'cssUrls' => $responsiveAssets->getCssUrls(),
            'jsUrls' => array_merge(
                $responsiveAssets->getJsUrls(['responsive-topbar.js'], 'horde'),
                $responsiveAssets->getJsUrls(['responsive-rules.js'])
            ),
        ];

        // Render template
        $templatePath = INGO_TEMPLATES . '/responsive/rules.html.php';
        $view = new ResponsiveTemplateView($templatePath, $viewData);

        $streamFactory = $injector->getInstance('Psr\Http\Message\StreamFactoryInterface');
        $responseFactory = $injector->getInstance('Psr\Http\Message\ResponseFactoryInterface');

        return $responseFactory->createResponse(200)
            ->withBody($streamFactory->createStream($view->render()));
    }

    /**
     * Display rule detail
     *
     * @param ServerRequestInterface $request The request object
     * @param string $uid Rule UID
     *
     * @return ResponseInterface The response
     */
    private function viewRule(ServerRequestInterface $request, string $uid): ResponseInterface
    {
        global $registry, $injector, $notification;

        // Get responsive assets helper
        $responsiveAssets = new \Horde\Core\Assets\ResponsiveAssets($registry);

        // Load rule from storage
        $storage = $injector->getInstance('Ingo_Factory_Storage')->create();
        $rule = $storage->getRuleByUid($uid);

        if (!$rule) {
            $notification->push(_("Rule not found."), 'horde.error');
            // Redirect back to rules list
            header('Location: ' . \Horde::url('responsive', true));
            exit;
        }

        // Build topbar
        $topbar = $this->renderTopbar();

        // Prepare view data
        $viewData = [
            'topbar' => $topbar,
            'rule' => [
                'uid' => $uid,
                'name' => $rule->name,
                'description' => $rule->description(),
                'disabled' => $rule->disable ?? false,
            ],
            'cssUrls' => $responsiveAssets->getCssUrls(),
            'jsUrls' => $responsiveAssets->getJsUrls(['responsive-topbar.js'], 'horde'),
            'backUrl' => \Horde::url('responsive', true),
        ];

        // Render template
        $templatePath = INGO_TEMPLATES . '/responsive/rule.html.php';
        $view = new ResponsiveTemplateView($templatePath, $viewData);

        $streamFactory = $injector->getInstance('Psr\Http\Message\StreamFactoryInterface');
        $responseFactory = $injector->getInstance('Psr\Http\Message\ResponseFactoryInterface');

        return $responseFactory->createResponse(200)
            ->withBody($streamFactory->createStream($view->render()));
    }

    /**
     * Get icon HTML for a rule
     *
     * @param object $filter Rule object
     *
     * @return string Icon HTML or empty string for default emoji
     */
    private function getRuleIcon($filter): string
    {
        $iconMap = [
            'Ingo_Rule_System_Blacklist' => 'blacklist.png',
            'Ingo_Rule_System_Whitelist' => 'whitelist.png',
            'Ingo_Rule_System_Vacation' => 'vacation.png',
            'Ingo_Rule_System_Forward' => 'forward.png',
            'Ingo_Rule_System_Spam' => 'spam.png',
        ];

        $class = get_class($filter);

        if (isset($iconMap[$class])) {
            // Use Horde_Themes_Image to generate proper image tag
            try {
                return \Horde_Themes_Image::tag($iconMap[$class]);
            } catch (\Exception $e) {
                // Fallback to empty string (template will use emoji)
                return '';
            }
        }

        // Return empty string for default emoji (📋)
        return '';
    }

    /**
     * Render the responsive topbar
     *
     * @return string HTML topbar
     */
    private function renderTopbar(): string
    {
        global $registry;

        $topbarData = [
            'appName' => _("Mail Filters"),
            'portalUrl' => (string) $registry->getServiceLink('portal')->setRaw(true),
            'logoutUrl' => (string) $registry->getServiceLink('logout')->setRaw(true),
            'userName' => $registry->getAuth(),
        ];

        $templatePath = HORDE_TEMPLATES . '/responsive/topbar.html.php';
        $view = new ResponsiveTemplateView($templatePath, $topbarData);

        return $view->render();
    }
}
