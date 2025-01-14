<?php

namespace Cyberfusion\ClusterApi\Models;

use Cyberfusion\ClusterApi\Support\Arr;
use Cyberfusion\ClusterApi\Contracts\Model;
use Cyberfusion\ClusterApi\Enums\PassengerEnvironment;
use Cyberfusion\ClusterApi\Enums\PassengerAppType;
use Cyberfusion\ClusterApi\Support\Validator;

class PassengerApp extends ClusterModel implements Model
{
    private string $name;
    private int $unixUserId;
    private string $environment = PassengerEnvironment::PRODUCTION;
    private array $environmentVariables = [];
    private int $maxPoolSize;
    private int $maxRequests;
    private int $poolIdleTime;
    private int $port;
    private string $appType = PassengerAppType::NODEJS;
    private string $appRoot;
    private ?string $nodejsVersion;
    private ?string $startupFile;
    private bool $isNamespaced = false;
    private ?int $cpuLimit = null;
    private ?string $unitName;
    private ?int $id = null;
    private ?int $clusterId = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): PassengerApp
    {
        Validator::value($name)
            ->maxLength(64)
            ->pattern('^[a-z0-9-_]+$')
            ->validate();

        $this->name = $name;

        return $this;
    }

    public function getUnixUserId(): int
    {
        return $this->unixUserId;
    }

    public function setUnixUserId(int $unixUserId): PassengerApp
    {
        $this->unixUserId = $unixUserId;

        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment): PassengerApp
    {
        Validator::value($environment)
            ->valueIn(PassengerEnvironment::AVAILABLE)
            ->validate();

        $this->environment = $environment;

        return $this;
    }

    public function getEnvironmentVariables(): array
    {
        return $this->environmentVariables;
    }

    public function setEnvironmentVariables(array $environmentVariables): PassengerApp
    {
        $this->environmentVariables = $environmentVariables;

        return $this;
    }

    public function getMaxPoolSize(): int
    {
        return $this->maxPoolSize;
    }

    public function setMaxPoolSize(int $maxPoolSize): PassengerApp
    {
        $this->maxPoolSize = $maxPoolSize;

        return $this;
    }

    public function getMaxRequests(): int
    {
        return $this->maxRequests;
    }

    public function setMaxRequests(int $maxRequests): PassengerApp
    {
        $this->maxRequests = $maxRequests;

        return $this;
    }

    public function getPoolIdleTime(): int
    {
        return $this->poolIdleTime;
    }

    public function setPoolIdleTime(int $poolIdleTime): PassengerApp
    {
        $this->poolIdleTime = $poolIdleTime;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): PassengerApp
    {
        $this->port = $port;

        return $this;
    }

    public function getAppType(): string
    {
        return $this->appType;
    }

    public function setAppType(string $appType): PassengerApp
    {
        Validator::value($appType)
            ->valueIn(PassengerAppType::AVAILABLE)
            ->validate();

        $this->appType = $appType;

        return $this;
    }

    public function getAppRoot(): string
    {
        return $this->appRoot;
    }

    public function setAppRoot(string $appRoot): PassengerApp
    {
        Validator::value($appRoot)
            ->path()
            ->validate();

        $this->appRoot = $appRoot;

        return $this;
    }

    public function getNodejsVersion(): ?string
    {
        return $this->nodejsVersion;
    }

    public function setNodejsVersion(?string $nodejsVersion): PassengerApp
    {
        Validator::value($nodejsVersion)
            ->pattern('^[0-9]{1,2}\.[0-9]{1,2}$')
            ->validate();

        $this->nodejsVersion = $nodejsVersion;

        return $this;
    }

    public function getStartupFile(): ?string
    {
        return $this->startupFile;
    }

    public function setStartupFile(?string $startupFile): PassengerApp
    {
        Validator::value($startupFile)
            ->nullable()
            ->path()
            ->endsWith('.js')
            ->validate();

        $this->startupFile = $startupFile;

        return $this;
    }

    public function isNamespaced(): bool
    {
        return $this->isNamespaced;
    }

    public function setIsNamespaced(bool $isNamespaced): PassengerApp
    {
        $this->isNamespaced = $isNamespaced;

        return $this;
    }

    public function getCpuLimit(): ?int
    {
        return $this->cpuLimit;
    }

    public function setCpuLimit(?int $cpuLimit): PassengerApp
    {
        $this->cpuLimit = $cpuLimit;

        return $this;
    }

    public function getUnitName(): ?string
    {
        return $this->unitName;
    }

    public function setUnitName(?string $unitName): PassengerApp
    {
        $this->unitName = $unitName;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): PassengerApp
    {
        $this->id = $id;

        return $this;
    }

    public function getClusterId(): ?int
    {
        return $this->clusterId;
    }

    public function setClusterId(?int $clusterId): PassengerApp
    {
        $this->clusterId = $clusterId;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): PassengerApp
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): PassengerApp
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function fromArray(array $data): PassengerApp
    {
        return $this
            ->setName(Arr::get($data, 'name'))
            ->setUnixUserId(Arr::get($data, 'unix_user_id'))
            ->setEnvironment(Arr::get($data, 'environment'))
            ->setEnvironmentVariables(Arr::get($data, 'environment_variables'))
            ->setMaxPoolSize(Arr::get($data, 'max_pool_size'))
            ->setMaxRequests(Arr::get($data, 'max_requests'))
            ->setPoolIdleTime(Arr::get($data, 'pool_idle_time'))
            ->setPort(Arr::get($data, 'port'))
            ->setAppType(Arr::get($data, 'app_type'))
            ->setNodejsVersion(Arr::get($data, 'nodejs_version'))
            ->setStartupFile(Arr::get($data, 'startup_file'))
            ->setIsNamespaced((bool)Arr::get($data, 'is_namespaced'))
            ->setCpuLimit(Arr::get($data, 'cpu_limit'))
            ->setAppRoot(Arr::get($data, 'app_root'))
            ->setUnitName(Arr::get($data, 'unit_name'))
            ->setId(Arr::get($data, 'id'))
            ->setClusterId(Arr::get($data, 'cluster_id'))
            ->setCreatedAt(Arr::get($data, 'created_at'))
            ->setUpdatedAt(Arr::get($data, 'updated_at'));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'unix_user_id' => $this->getUnixUserId(),
            'environment' => $this->getEnvironment(),
            'environment_variables' => $this->getEnvironmentVariables(),
            'max_pool_size' => $this->getMaxPoolSize(),
            'max_requests' => $this->getMaxRequests(),
            'pool_idle_time' => $this->getPoolIdleTime(),
            'port' => $this->getPort(),
            'app_type' => $this->getAppType(),
            'nodejs_version' => $this->getNodejsVersion(),
            'startup_file' => $this->getStartupFile(),
            'is_namespaced' => $this->isNamespaced(),
            'cpu_limit' => $this->getCpuLimit(),
            'app_root' => $this->getAppRoot(),
            'unit_name' => $this->getUnitName(),
            'id' => $this->getId(),
            'cluster_id' => $this->getClusterId(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
