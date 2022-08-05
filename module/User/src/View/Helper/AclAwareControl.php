<?php

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\TranslatorAwareTrait;
use Laminas\View\Renderer\PhpRenderer;
use User\Service\UserServiceInterface;

final class AclAwareControl extends AbstractHelper
{
    use TranslatorAwareTrait;

    /** @var string $iconPath */
    public $iconPath = '/icons/bootstrap-icons.svg#';
    /** @var string $iconHeight */
    public $iconHeight = '32';
    /** @var string $iconWidth */
    public $iconWidth = '32';
    /** @var string $fill */
    public $fill = 'currentColor';
    /** @var string $iconName */
    public $iconName = '';
    /** @var string $svgClass */
    public $svgClass = 'bi';
    /** @var array $options */
    public $options = [];
    /** @var string $type */
    public $type = 'button';
    /** @var string $buttonClass */
    public $buttonClass = 'btn btn-primary';
    /** @var PhpRenderer */
    protected $view;
    /** @var string $icon_options */
    private $iconOptionsConfigKey = 'icon_options';
    /** @var string $buttonsOptionsConfigKey */
    private $buttonOptionsConfigKey = 'button_options';
    /** @var UserServiceInterface */
    protected $user;
    /** @var AclInterface $acl */
    protected $acl;
    /** @var ResourceInterface|string $resource */
    protected $resource;
    /** @var string $privilege */
    protected $privilege;
    /** @var string $url */
    protected $url;
    /** @var array<mixed> $iconOptions */
    protected $iconOptions = [];

    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param string $resource
     * @param string $privilege
     * @param string $type
     * @param string $url
     * @param array $options
     */
    public function __invoke(UserServiceInterface $user, $resource, $privilege, $type, $url, $options = []): string
    {
        $this->user      = $user;
        $this->resource  = $resource;
        $this->privilege = $privilege;
        $this->type      = $type;
        $this->url       = $url;
        $this->options   = $options;
        if (! $this->acl->isAllowed($user, $resource, $privilege)) {
            return '';
        }
        return $this->buildControl($resource, $privilege, $type, $url, $options);
    }

    /**
     * @param string $resource
     * @param string $privilege
     * @param string $url
     * @param string $type
     * @param null|array $options
     */
    public function buildControl($resource, $privilege, $type, $url, $options = []): string
    {
        $translator = $this->getTranslator();
        $html       = '';
        $this->setType($type);
        if ($this->type === 'button') {
            if (isset($options[$this->buttonOptionsConfigKey])) {
                $this->setOptions($options[$this->buttonOptionsConfigKey]);
                if (isset($this->options['class'])) {
                    $this->buttonClass = $this->options['class'];
                }
            }
            // if we are building a button then build it and return early
            $html .= '<a class="' . $this->buttonClass . '"';
            $html .= 'href="'
            . $url . '" role="button">'
            . $translator->translate($this->options['link_text'])
            . '</a>';
            //return $html;
        }
        $html .= '<svg class="' . $this->svgClass;
        return $html;
    }

    public function getIconPath(): string
    {
        return $this->iconPath;
    }

    public function getIconHeight(): string
    {
        return $this->iconHeight;
    }

    public function getIconWidth(): string
    {
        return $this->iconWidth;
    }

    public function getFill(): string
    {
        return $this->fill;
    }

    public function getIconName(): string
    {
        return $this->iconName;
    }

    public function getSvgClass(): string
    {
        return $this->svgClass;
    }

    public function getIconOptions(): array
    {
        return $this->iconOptions;
    }

    /** @param string $iconPath */
    public function setIconPath($iconPath): void
    {
        $this->iconPath = $iconPath;
    }

    /** @param string $iconHeight */
    public function setIconHeight($iconHeight): void
    {
        $this->iconHeight = $iconHeight;
    }

    /** @param string $iconWidth */
    public function setIconWidth($iconWidth): void
    {
        $this->iconWidth = $iconWidth;
    }

    /** @param string $fill */
    public function setFill($fill): void
    {
        $this->fill = $fill;
    }

    /** @param string $iconName */
    public function setIconName($iconName): void
    {
        $this->iconName = $iconName;
    }

    /** @param string $svgClass */
    public function setSvgClass($svgClass): void
    {
        $this->svgClass = $svgClass;
    }

    /** @param array $options */
    public function setOptions($options): void
    {
        $this->options = $options;
    }

    /** @param string $type */
    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
