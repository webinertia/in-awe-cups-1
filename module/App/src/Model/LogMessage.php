<?php
/**
 * phpcs:ignoreFile
 */
declare(strict_types=1);

namespace App\Model;

use Laminas\Permissions\Acl\Resource\ResourceInterface;
use User\Acl\ResourceAwareTrait;

use function get_object_vars;

final class LogMessage implements ResourceInterface
{
    use ResourceAwareTrait;

    /** @var int $logId */
    protected $logId;
    /** @var int $extra_userId */
    protected $extra_userId;
    /** @var string $extra_userName */
    protected $extra_userName;
    /** @var string $extra_role */
    protected $extra_role;
    /** @var string $extra_firstName */
    protected $extra_firstName;
    /** @var string $extra_lastName */
    protected $extra_lastName;
    /** @var string $extra_email */
    protected $extra_email;
    /** @var string $extra_profileImage */
    protected $extra_profileImage;
    /** @var string $priorityName */
    protected $priorityName;
    /** @var string $message */
    protected $message;
    /** @var string $timeStamp */
    protected $timeStamp;
    /** @var int $priority */
    protected $priority;
    /** @var string $extra_referenceId */
    protected $extra_referenceId;
    /** @var string $extra_errno */
    protected $extra_errno;
    /** @var string $extra_file */
    protected $extra_file;
    /** @var string $extra_line */
    protected $extra_line;
    /** @var string $extra_trace */
    protected $extra_trace;
    /** @var int $fileId */
    protected $fileId;
    /** @var string $resourceId */
    protected $resourceId = 'logs';

    public function exchangeArray(array $data)
    {
        $this->logId = $data['logId'] ?? null;
        $this->extra_userId = $data['extra_userId'] ?? null;
        $this->extra_userName = $data['extra_userName'] ?? null;
        $this->extra_role = $data['extra_role'] ?? null;
        $this->extra_firstName = $data['extra_firstName'] ?? null;
        $this->extra_lastName = $data['extra_lastName'] ?? null;
        $this->extra_email = $data['extra_email'] ?? null;
        $this->extra_profileImage = $data['extra_profileImage'] ?? null;
        $this->priorityName = $data['priorityName'] ?? null;
        $this->message = $data['message'] ?? null;
        $this->timeStamp = $data['timeStamp'] ?? null;
        $this->priority = $data['priority'] ?? null;
        $this->extra_referenceId = $data['extra_referenceId'] ?? null;
        $this->extra_errno = $data['extra_errno'] ?? null;
        $this->extra_file = $data['extra_file'] ?? null;
        $this->extra_line = $data['extra_line'] ?? null;
        $this->extra_trace = $data['extra_trace'] ?? null;
        $this->fileId = $data['fileId'] ?? null;
    }

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
