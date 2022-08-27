<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 * phpcs:ignoreFile
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\Trait\JsonDataTrait;
use App\Db\TableGateway;
use App\Log\LoggerAwareInterface;
use App\Log\LoggerAwareInterfaceTrait;
use App\Log\LogEvent;
use App\Form\ContactForm;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use App\Model\ContactMessage;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\View\Model\ViewModel;
use SplFileObject;
use stdClass;
use User\Acl\ResourceAwareTrait;
use Webinertia\Utils\Debug;

final class TestController extends AbstractAppController implements LoggerAwareInterface
{
    use JsonDataTrait;
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'test';
    public function indexAction(): ViewModel
    {

        $settings = ['security' => ['enable_captcha' => false]];
        $form = new ContactForm($settings, []);
        $message = new ContactMessage();

        $isPost = $this->request->getQUery()->get('isPost');
        if (! $isPost) {
            // show john in the form
            $userData = [
                'fullName' => null,
                'email'    => null,
                'message'  => null,
            ];
        }
        if ($isPost) {
            // send janes message
            $userData = [
                'fullName' => 'Jane Doe',
                'email'    => 'test@jane.com',
                'message'  => 'New message',
            ];
        }
        $message->exchangeArray($userData);
        $form->bind($message);
        //$form->setData($message->toArray());
        $valid = $form->isValid();
        Debug::dump($valid, 'is form valid?');
        //$data = $form->getData(FormInterface::VALUES_AS_ARRAY);
        $data = $form->getData();
       // $data = $form->getObject();
        Debug::dump($data);

        $this->setJsonValue('status', 'complete');
        $this->jsonData(['status' => 'suspended', 'message' => 'onHold']);
        //Debug::dump($this->jsonData());
        $settings = $this->getService('config')['app_settings'];
        $appSettings    = $this->getService('config')['app_settings'];
        $moduleSettings = $this->getService('config')['module_settings']['user'];

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            Debug::dump($data);
        }

        $this->getEventManager()->trigger(LogEvent::NOTICE, 'This is a standard log message');

        $this->getEventManager()->trigger(LogEvent::ALERT, 'log_login_success');

        $limit = $this->params()->fromQuery('limit');
        if ($limit > 0) {
            for ($i = 0; $i < $limit; $i++) {
                $this->getEventManager()->trigger(LogEvent::DEBUG, 'Auto generated log message number ' . $i);
            }
        }
        return $this->view;
    }
}
