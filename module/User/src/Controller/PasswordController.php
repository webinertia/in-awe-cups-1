<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Service\Email;
use DateTime;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;
use User\Filter\RegistrationHash;
use User\Form\ResetPassword;
use User\Service\UserInterface;

final class PasswordController extends AbstractAppController
{
    /** @var string $resourceId */
    protected $resourceId = 'account';
    public function resetAction(): ViewModel
    {
        try {
            $appSettings = $this->service('config')['app_settings'];
            $step        = $this->params('step', 'zero');
            $dateTime    = new DateTime('NOW');
            $options     = ['db' => $this->usrGateway, 'enableCaptcha' => $appSettings['security']['enable_captcha']];
            $form        = new ResetPassword(null, $options);
            $this->view->setVariable('showForm', true);
            switch ($step) {
                case 'submit-email':
                    $startTime = $dateTime->format($appSettings['server']['time_format']);
                    $formData  = ['resetTimeStamp' => $startTime, 'step' => 'two'];
                    $form->remove('password');
                    $form->remove('conf_password');
                    $form->setAttribute('action', '/user/password/reset/send-email');
                    $form->setData($formData);
                    break;
                case 'send-email':
                    if ($this->request->isPost()) {
                        $this->view->setVariable('showForm', false);
                        $form->setValidationGroup(['email', 'resetTimeStamp']);
                        $post = $this->request->getPost();
                        $form->setData($post);
                        if ($form->isValid()) {
                            $data = $form->getData();
                        }
                        $user = $this->usrGateway->fetchByColumn('email', $post['email']);
                        if ($user instanceof UserInterface) {
                            $filter               = new RegistrationHash();
                            $hash                 = $filter->filter(
                                ['email' => $post['email'], 'timestamp' => $post['resetTimeStamp']]
                            );
                            $user->resetTimeStamp = $post['resetTimeStamp'];
                            $user->resetHash      = $hash;
                            if ($this->usrGateway->update(['id' => $user->id], $user->toArray(true))) {
                                // send reset email
                                $mailService = $this->sm->get(Email::class);
                                try {
                                    $mailService->sendMessage($post['email'], Email::RESET_PASSWORD, $hash);
                                } catch (Throwable $th) {
                                    $this->getLogger()->error($th->getMessage());
                                }
                                // redirect
                                $this->getLogger()->info('User {firstName} {lastName} requested to reset password');
                                // condition is when you have just submitted your email to be sent a link to reset
                                // @codingStandardsIgnoreStart
                                $this->flashMessenger()->addInfoMessage(
                                    'You have been sent a reset link via the submitted email, please click the provided link to rest your password'
                                );
                                // @codingStandardsIgnoreEnd
                                $this->redirect()->toRoute('home');
                            } else {
                                throw new RuntimeException('Information not saved');
                            }
                        }
                    }
                    break;
                case 'reset-password':
                    try {
                        $token = $this->request->getQuery('token');
                        $user  = $this->usrGateway->fetchByColumn('resetHash', $token);
                    } catch (Throwable $th) {
                        $this->getLogger()->warning(
                            'Unknown user from IP:'
                            . $this->request->getServer('REMOTE_ADDR')
                            . ' attempted to reset password with invalid or expired token'
                        );
                        // @codingStandardsIgnoreStart
                        $this->flashMessenger()
                        ->addErrorMessage(
                            'The supplied reset token is invlaid or expired please contact the site adminitrators. This action has been logged'
                        );
                        // @codingStandardsIgnoreEnd
                        // this needs to redirect to a contact page.
                        $this->redirect()->toRoute('home');
                    }
                    if (! $this->request->isPost()) {
                        $this->view->setVariable('showForm', true);
                        $form->remove('email');
                        $form->setAttribute('action', '/user/password/reset/reset-password?token=' . $token);
                        $startTime = DateTime::createFromFormat(
                            $this->appSettings->server->time_format,
                            $user->resetTimeStamp
                        );
                        $limit     = DateTime::createFromFormat(
                            $this->appSettings->server->time_format,
                            $dateTime->format($this->appSettings->server->time_format)
                        );
                        $interval  = $startTime->diff($limit);
                        if ($interval->d > 0) {
                            $this->flashmessenger()->addErrorMessage(
                                'Your reset link has expired, please submit your email to send a valid reset link'
                            );
                            return $this->redirect()->toRoute(
                                'password',
                                ['action' => 'reset', 'step' => 'submit-email']
                            );
                        }
                    } else {
                        $form->setInputFilter($form->addInputFilter());
                        $form->remove('email');
                        $form->setValidationGroup(['password', 'conf_password']);
                        $post = $this->request->getPost();
                        $form->setData($post);
                        if ($form->isValid()) {
                            $data                 = $form->getData();
                            $user->password       = $data['password'];
                            $user->resetTimeStamp = null;
                            $user->resetHash      = null;
                            if ($this->usrGateway->update(['id' => $user->id], $user->toArray(true))) {
                                $this->flashmessenger()->addSuccessMessage(
                                    'Your password has been succesfully updated'
                                );
                                return $this->redirect()->toRoute('user', ['action' => 'login']);
                            } else {
                                $this->flashmessenger()->addErrorMessage(
                                    'Your password was not updated due to an error processing your request'
                                );
                                return $this->redirect()->toRoute('home');
                            }
                        }
                    }
                    break;
            }
            $this->view->setVariable('form', $form);
            return $this->view;
        } catch (RuntimeException $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}
