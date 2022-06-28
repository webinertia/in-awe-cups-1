<?php

declare(strict_types=1);

namespace App\Service;

use Laminas\Config\Config;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\Mail\Exception\DomainException;
use Laminas\Mail\Exception\InvalidArgumentException;
use Laminas\Mail\Header\ContentType;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use RuntimeException;

use function sprintf;

final class Email implements ResourceInterface
{
    use TranslatorAwareTrait;

    public const RESOURCE_ID    = 'mailService';
    public const VERIFICATION   = 'verificationMessage';
    public const WELCOME        = 'welcomeMessage';
    public const RESET_PASSWORD = 'resetPasswordMessage';
    public const NEWSLETTER     = 'newsletterMessage';
    public const CONTACT        = 'contactUs';
    /** @var Acl $acl */
    private $acl;
    /** @var Config $appSettings */
    protected $appSettings;
    /** @var Request $request */
    protected $request;
    /** @var string|HTTP_HOST $hostName */
    protected $hostName;
    /** @var string|http https|REQUEST_SCHEME $requestScheme  */
    protected $requestScheme;
    /** @var Message $message */
    public $message;
    /** @var string $subject */
    public $subject;
    /** @var Users $user */
    public $user;
    /** @var ServiceManager $sm */
    protected $sm;
    /** @var SmtpTransport */
    protected $transport;
    /** @var array $config */
    protected $config;
    /**
     * @param Config|null $settings
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(array $config)
    {
        $this->config      = $config;
        $this->appSettings = new Config($this->config['app_settings']);
        $transport         = new SmtpTransport();
        $this->setMessage(new Message());
        $options = new SmtpOptions($this->config['smtp_options']);
        $transport->setOptions($options);
        // currently only Smtp transport is supported
        $this->setTransport($transport);
    }

    public function setTransport(TransportInterface $transport): void
    {
        $this->transport = $transport;
    }

    /**
     * @param mixed $address
     * @param mixed $type
     * @param mixed $token
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function sendMessage($address, $type, $token = null): void
    {
        try {
            $message = $this->getMessage();
            $message->addTo($address);
            // This email must match the connection_config key in the options above
            $userName = $this->config['smtp_options']['connection_config']['username'];
            $message->addFrom($userName);
            switch ($type) {
                case self::VERIFICATION:
                    $message->setSubject($this->appSettings->view->site_name . ' account verification');
                    $this->verificationMessage($address, $message, $token);

                    break;
                case self::WELCOME:
                    break;
                case self::RESET_PASSWORD:
                    $message->setSubject($this->appSettings->view->site_name . ' Password Reset Requested');
                    $this->passwordResetMessage($address, $message, $token);

                    break;
                default:
                    throw new RuntimeException('Unsupported message type detected!!');
                break;
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $fromAddress
     * @param string $fromName
     * @param string $formText
     */
    public function contactUsMessage($fromAddress, $fromName, $formText): void
    {
        if (empty($fromAddress)) {
            throw new RuntimeException('Unknown From address...');
        }
        $textContent    = $formText;
        $text           = new MimePart($textContent);
        $text->type     = Mime::TYPE_TEXT;
        $text->charset  = 'utf-8';
        $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $htmlMarkup     = '<p>' . $textContent . '</p>';
        $html           = new MimePart($htmlMarkup);
        $html->type     = Mime::TYPE_HTML;
        $html->charset  = 'utf-8';
        $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $body           = new MimeMessage();
        $body->setParts([
            $text,
            $html,
        ]);
        $this->message->setBody($body);
        $this->message->setFrom($fromAddress, $fromName);
        $this->message->addTo($this->appSettings->email->contact_form_email);
        $this->message->setSubject($this->appSettings->view->site_name . ' Contact Page Submission');
        /** @var ContentType $contentTypeHeader */
        $contentTypeHeader = $this->message->getHeaders()->get('Content-Type');
        $contentTypeHeader->setType('multipart/alternative');
        try {
            $this->transport->send($this->message);
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $address
     * @param Message $message
     * @param string $token
     */
    protected function passwordResetMessage($address, $message, $token): void
    {
        try {
            //$translator = $this->getTranslator();
            if (empty($token)) {
                throw new RuntimeException('You must pass a token to send a password reset email!!');
            }
            $textContent    = 'Please click the link below to change your password.';
            $text           = new MimePart($textContent);
            $text->type     = Mime::TYPE_TEXT;
            $text->charset  = 'utf-8';
            $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $htmlMarkup     = '<p>' . $textContent . '<br>';
            $format         = '<a href="%s://%s/user/password/reset/reset-password?token=%s">Change Password</a>';
            $htmlMarkup    .= sprintf($format, $this->requestScheme, $this->hostName, $token);
            $htmlMarkup    .= '</p>';
            $html           = new MimePart($htmlMarkup);
            $html->type     = Mime::TYPE_HTML;
            $html->charset  = 'utf-8';
            $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $body           = new MimeMessage();
            $body->setParts([
                $text,
                $html,
            ]);
            $message->setBody($body);
            /** @var ContentType $contentTypeHeader */
            $contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $contentTypeHeader->setType('multipart/alternative');
            try {
                $this->transport->send($message);
            } catch (RuntimeException $e) {
                echo $e->getMessage();
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $address
     * @param Message $message
     * @param string $token
     * @throws RuntimeException
     * @throws ServiceNotFoundException
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    protected function verificationMessage($address, $message, $token): void
    {
        try {
            //$translator = $this->getTranslator();
            if (empty($token)) {
                throw new RuntimeException('You must pass a token to send a verification email!!');
            }
            $textContent    = 'Please click the link below to verify your account.';
            $text           = new MimePart($textContent);
            $text->type     = Mime::TYPE_TEXT;
            $text->charset  = 'utf-8';
            $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $htmlMarkup     = '<p>' . $textContent . '<br>';
            $format         = '<a href="%s://%s/user/register/verify?token=%s">Verify Account</a>';
            $htmlMarkup    .= sprintf($format, $this->requestScheme, $this->hostName, $token);
            $htmlMarkup    .= '</p>';
            $html           = new MimePart($htmlMarkup);
            $html->type     = Mime::TYPE_HTML;
            $html->charset  = 'utf-8';
            $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $body           = new MimeMessage();
            $body->setParts([
                $text,
                $html,
            ]);
            $message->setBody($body);
            /** @var ContentType $contentTypeHeader */
            $contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $contentTypeHeader->setType('multipart/alternative');
            try {
                $this->transport->send($message);
            } catch (RuntimeException $e) {
                echo $e->getMessage();
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    /** Return the AclInterface resourceId */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /** Return the Message Object */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /** Set the Message Object */
    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }
}
