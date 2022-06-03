<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractController;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

use function get_parent_class;

abstract class AbstractAdminController extends AbstractController implements ResourceInterface
{
    public const RESOURCE_ID = 'admin';
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\Permissions\Acl\Resource\ResourceInterface::getResourceId()
     */
    public function getResourceId()
    {
        // TODO Auto-generated method stub
        return self::RESOURCE_ID;
    }

    public function init(): self
    {
        if (! $this->acl->isAllowed($this->user, $this, 'admin.access')) {
            $this->redirect()->toRoute('forbidden');
        }
        $adminParent = self::class;
        switch ($adminParent === get_parent_class(static::class)) {
            case true:
                $this->layout('layout/dashboard-dark');
                break;
            default:
                break;
        }
        parent::init();
        return $this;
    }
}
