<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractController;
use Laminas\Db\Sql\Select;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use User\Model\UserTable;
use Zend\Paginator\AdapterPluginManager;

class WidgetController extends AbstractController
{
    /** @var Paginator $paginator */
    protected $paginator;
    public function __construct()
    {
    }

    public function _init()
    {
            $config = $this->sm->get('Config');
        $group      = $this->getEvent()->getRouteMatch()->getParam('group', 'all');
        $table      = $this->sm->get(UserTable::class);
        $sql        = $table->getSql();
        $select     = new Select();
        $select
            ->from('user_roles')
            ->join('users', 'users.role = user_roles.role', [
                'userName',
                'email',
                'firstName',
                'lastName',
                'profileImage',
                'age',
                'birthday',
                'gender',
                'race',
                'bio',
                'companyName',
                'regDate',
                'active',
                'verified',
            ])
            ->order(['users.id DSC']);
        if ($group === 'all') {
            $select->where->greaterThan('user_roles.id', 0);
        } else {
            $select->where->equalTo('user_roles.role', $group);
        }
            $pluginManager = $this->sm->get(AdapterPluginManager::class);
        $adapter           = $pluginManager->get(DbSelect::class, [$select, $sql, $table->getResultSetPrototype()]);
        $paginator         = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($config['widgets']['member_list']['items_per_page']);
        $this->paginator = $paginator;
        $this->view->setTerminal(true);
    }

    public function memberListAction()
    {
        $this->view->setTerminal(true);
        $page = (int) $this->params('page', '1');
// $this->paginator->setDefaultItemCountPerPage(1);
        $this->paginator->setCurrentPageNumber($page);
        $this->view->setVariables(['paginator' => $this->paginator, 'listType' => $this->config['widgets']['admin_member_list']['display_groups']]);
        return $this->view;
    }

    public function listDataAction()
    {
        $this->view->setTerminal(true);
        $page = (int) $this->params('page', '1');
//$this->paginator->setDefaultItemCountPerPage(2);
        $this->paginator->setCurrentPageNumber($page);
        $this->view->setVariable('paginator', $this->paginator);
        return $this->view;
    }
}
