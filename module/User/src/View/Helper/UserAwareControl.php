<?php

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\TranslatorAwareTrait;
use Laminas\View\Renderer\PhpRenderer;
use User\Model\Users;

class UserAwareControl extends AbstractHelper
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
    /** @var User\Model\User|User\Model\Guest $user */
    protected $user;
    /**
     * @param mixed $resource
     * @param mixed $url
     * @param string $type
     * @param null|array $options
     * @return string|void
     */
    public function buildControl($resource, $url, $type = 'button', ?array $options = []): string
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
            /*
           * <a class="btn btn-primary" href="<?= $this->url('profile', ['action' => 'edit-profile', 'userName' => $data->userName]) ?>" role="button">Edit Profile</a>
             */
            $html .= '<a class="' . $this->buttonClass . '"';
            $html .= 'href="' . $url . '" role="button">' . $translator->translate($this->options['link_text']) . '</a>';
            return $html;
        }
        $html .= '<svg class="' . $this->svgClass;
        return $html;
    }

    /**
     * @param string $resource
     * @param string $type
     * @param string $url
     * @param array $options
     */
    public function __invoke(Users $user, AclInterface $acl, $resource, $type, $url, $options = []): string
    {
        $this->user = $user;
        $this->acl  = $acl;
        return $this->buildControl($resource, $type, $url, $options);
    }

    public function getIconPath(): string
    {
        return $this->iconPath;
    }

    public function getIconHeight(): string
    {
        return $this->iconHeight;
    }

    /**
     * @return the $iconWidth
     */
    public function getIconWidth()
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
