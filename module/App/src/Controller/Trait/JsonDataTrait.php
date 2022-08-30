<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Laminas\Stdlib\ArrayUtils;

use function is_array;

trait JsonDataTrait
{
    /** @var array<string, mixed> $data */
    protected $viewData = [];
    /** @var array<int, string> $allowedKeys */
    protected $allowedKeys = [
        'message'        => null,
        'displayAction'  => null,
        'redirectUrl'    => null,
        'redirectParams' => null,
        'success'        => null,
        'target'         => null,
        'isJson'         => null,
        'status'         => null,
        'formHasErrors'  => null,
        'data'           => null,
        'step'           => null,
    ];

    /**
     * @param mixed $data
     */
    protected function jsonData(?array $data = null): ?array
    {
        $headers = $this->response->getHeaders();
        $headers->addHeaderLine('X-Powered-BY', 'Webinertia Data Systems');
        if ($data !== null) {
            $this->viewData = ArrayUtils::merge($this->viewData, $data);
        }
        return $this->viewData;
    }

    public function setJsonValue(string $key, mixed $value): void
    {
        if (ArrayUtils::inArray($key, $this->allowedKeys, true)) {
            if (is_array($this->viewData[$key]) && is_array($value)) {
                $this->viewData[$key] = ArrayUtils::merge($this->viewData[$key], $value);
            } else {
                $this->viewData[$key] = $value;
            }
        }
    }

    public function getJsonData(): array
    {
        return $this->viewData;
    }
}
