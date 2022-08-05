<?php

declare(strict_types=1);

namespace App\Service;

use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\Mail\Exception\InvalidArgumentException;
use Laminas\Mail\Header\ContentType;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Validator\EmailAddress;
use RuntimeException;
use User\Acl\ResourceAwareTrait;
use User\Service\UserServiceInterface;

use function sprintf;

final class Email implements ResourceInterface
{
    use ResourceAwareTrait;
    use TranslatorAwareTrait;

    public const RESOURCE_ID    = 'mailService';
    public const VERIFICATION   = 'verificationMessage';
    public const WELCOME        = 'welcomeMessage';
    public const RESET_PASSWORD = 'resetPasswordMessage';
    public const NEWSLETTER     = 'newsletterMessage';
    public const CONTACT        = 'contactUs';

    /** @var AclInterface $acl */
    private $acl;
    /** @var array<mixed> $appSettings */
    protected $appSettings;
    /** @var ContentType $contentTypeHeader */
    protected $contentTypeHeader;
    /** @var Request $request */
    protected $request;
    /** @var string $hostName */
    protected $hostName;
    /** @var string $requestScheme  */
    protected $requestScheme;
    /** @var string $resourceId */
    protected $resourceId = 'messages';
    /** @var Message $message */
    public $message;
    /** @var string $subject */
    public $subject;
    /** @var UserServiceInterface $user */
    public $user;
    /** @var SmtpTransport */
    protected $transport;
    /** @var array $config */
    protected $config;
    /** @return void */
    public function __construct(array $config)
    {
        $this->config      = $config;
        $this->appSettings = $this->config['app_settings'];
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

    public function sendMessage(string $address, string $type, ?string $token = null): void
    {
        try {
            $validator = new EmailAddress();
            if (! $validator->isValid($address)) {
                throw new InvalidArgumentException('Invalid email address');
            }
            $message = $this->getMessage();
            $message->addTo($address);
            // This email must match the connection_config key in the options above
            $userName = $this->config['smtp_options']['connection_config']['username'];
            $message->addFrom($userName);
            switch ($type) {
                case self::VERIFICATION:
                    $message->setSubject($this->appSettings['view']['site_name'] . ' account verification');
                    $this->verificationMessage($address, $message, $token);
                    break;
                case self::WELCOME:
                    break;
                case self::RESET_PASSWORD:
                    $message->setSubject($this->appSettings['view']['site_name'] . ' Password Reset Requested');
                    $this->passwordResetMessage($address, $message, $token);
                    break;
                default:
                    throw new RuntimeException('Unsupported message type detected!!');
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    public function contactUsMessage(string $fromAddress, string $fromName, string $formText): void
    {
        $validator = new EmailAddress();
        if (! $validator->isValid($fromAddress)) {
            throw new InvalidArgumentException('Invalid from address.');
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
        $this->message->addTo($this->appSettings['email']['contact_form_email']);
        $this->message->setSubject($this->appSettings['view']['site_name'] . ' Contact Page Submission');
        $this->contentTypeHeader = $this->message->getHeaders()->get('Content-Type');
        $this->contentTypeHeader->setType('multipart/alternative');
        try {
            $this->transport->send($this->message);
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    protected function passwordResetMessage(string $address, Message $message, string $token): void
    {
        try {
            $validator = new EmailAddress();
            if (! $validator->isValid($address)) {
                throw new InvalidArgumentException('Invalid email address');
            }
            //$translator = $this->getTranslator();
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
            $this->contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $this->contentTypeHeader->setType('multipart/alternative');
            try {
                $this->transport->send($message);
            } catch (RuntimeException $e) {
                echo $e->getMessage();
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    protected function verificationMessage(string $address, Message $message, string $token): void
    {
        try {
            $validator = new EmailAddress();
            if (! $validator->isValid($address)) {
                throw new InvalidArgumentException('Invalid email address');
            }
            //$translator = $this->getTranslator();
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
            $this->contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $this->contentTypeHeader->setType('multipart/alternative');
            try {
                $this->transport->send($message);
            } catch (RuntimeException $e) {
                echo $e->getMessage();
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
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
