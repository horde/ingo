<?php

declare(strict_types=1);

namespace Horde\Ingo\Responsive;

use Horde\Core\Controller\ResponsiveControllerTrait;
use Horde_Registry;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Exception;
use Horde_Themes_Image;
use Ingo_Storage_FilterIterator_Match;

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
    use ResponsiveControllerTrait;

    /**
     * Constructor
     *
     * @param Horde_Registry $registry Registry instance
     * @param UriFactoryInterface $uriFactory PSR-17 URI factory
     * @param ResponseFactoryInterface $responseFactory PSR-17 response factory
     * @param StreamFactoryInterface $streamFactory PSR-17 stream factory
     */
    public function __construct(
        private Horde_Registry $registry,
        private UriFactoryInterface $uriFactory,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory
    ) {}

    /**
     * Get application name for topbar display
     *
     * @return string Localized application name
     */
    protected function getAppName(): string
    {
        return _("Mail Filters");
    }

    /**
     * Get template base path for this application
     *
     * @return string Template base path with trailing slash
     */
    protected function getTemplateBasePath(): string
    {
        return INGO_TEMPLATES . '/responsive/';
    }

    /**
     * Get Horde registry instance
     *
     * @return Horde_Registry Registry instance
     */
    protected function getRegistry(): Horde_Registry
    {
        return $this->registry;
    }

    /**
     * Get PSR-17 URI factory instance
     *
     * @return UriFactoryInterface URI factory
     */
    protected function getUriFactory(): UriFactoryInterface
    {
        return $this->uriFactory;
    }

    /**
     * Get PSR-17 response factory instance
     *
     * @return ResponseFactoryInterface Response factory
     */
    protected function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    /**
     * Get PSR-17 stream factory instance
     *
     * @return StreamFactoryInterface Stream factory
     */
    protected function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory;
    }
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
        global $session, $injector;

        // Load rules from storage
        $storage = $injector->getInstance('Ingo_Factory_Storage')->create();

        // Get filters matching current script categories
        $filters = Ingo_Storage_FilterIterator_Match::create(
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

        // Render template (trait handles topbar, assets, response)
        return $this->renderTemplate('rules.html.php', [
            'rules' => $rules,
        ], ['responsive-rules.js']);
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
        global $injector;

        // Load rule from storage
        $storage = $injector->getInstance('Ingo_Factory_Storage')->create();
        $rule = $storage->getRuleByUid($uid);

        if (!$rule) {
            return $this->redirectTo(
                $this->buildUrl('responsive'),
                _("Rule not found."),
                'horde.error'
            );
        }

        // Render template (trait handles topbar, assets, response)
        return $this->renderTemplate('rule.html.php', [
            'rule' => [
                'uid' => $uid,
                'name' => $rule->name,
                'description' => $rule->description(),
                'disabled' => $rule->disable ?? false,
            ],
            'backUrl' => $this->buildUrl('responsive'),
        ]);
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
                return Horde_Themes_Image::tag($iconMap[$class]);
            } catch (Exception $e) {
                // Fallback to empty string (template will use emoji)
                return '';
            }
        }

        // Return empty string for default emoji (📋)
        return '';
    }
}
