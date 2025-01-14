<?php

namespace Cyberfusion\ClusterApi\Models;

use Cyberfusion\ClusterApi\Support\Arr;
use Cyberfusion\ClusterApi\Contracts\Model;
use Cyberfusion\ClusterApi\Enums\VirtualHostServerSoftwareName;
use Cyberfusion\ClusterApi\Enums\AllowOverrideDirectives;
use Cyberfusion\ClusterApi\Enums\AllowOverrideOptionDirectives;
use Cyberfusion\ClusterApi\Support\Validator;

class VirtualHost extends ClusterModel implements Model
{
    private string $domain;
    private array $serverAliases = [];
    private int $unixUserId;
    private string $documentRoot;
    private string $publicRoot;
    private ?int $fpmPoolId = null;
    private ?int $passengerAppId = null;
    private ?string $customConfig = null;
    private ?string $serverSoftwareName = null;
    private ?string $domainRoot = null;
    private ?array $allowOverrideDirectives;
    private ?array $allowOverrideOptionDirectives;
    private ?int $id = null;
    private ?int $clusterId = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): VirtualHost
    {
        $this->domain = $domain;

        return $this;
    }

    public function getServerAliases(): array
    {
        return $this->serverAliases;
    }

    public function setServerAliases(array $serverAliases): VirtualHost
    {
        Validator::value($serverAliases)
            ->unique()
            ->validate();

        $this->serverAliases = $serverAliases;

        return $this;
    }

    public function getUnixUserId(): int
    {
        return $this->unixUserId;
    }

    public function setUnixUserId(int $unixUserId): VirtualHost
    {
        $this->unixUserId = $unixUserId;

        return $this;
    }

    public function getDocumentRoot(): string
    {
        return $this->documentRoot;
    }

    public function setDocumentRoot(string $documentRoot): VirtualHost
    {
        Validator::value($documentRoot)
            ->path()
            ->validate();

        $this->documentRoot = $documentRoot;

        return $this;
    }

    public function getPublicRoot(): string
    {
        return $this->publicRoot;
    }

    public function setPublicRoot(string $publicRoot): VirtualHost
    {
        Validator::value($publicRoot)
            ->path()
            ->validate();

        $this->publicRoot = $publicRoot;

        return $this;
    }

    public function getFpmPoolId(): ?int
    {
        return $this->fpmPoolId;
    }

    public function setFpmPoolId(?int $fpmPoolId): VirtualHost
    {
        $this->fpmPoolId = $fpmPoolId;

        return $this;
    }

    public function getPassengerAppId(): ?int
    {
        return $this->passengerAppId;
    }

    public function setPassengerAppId(?int $passengerAppId): VirtualHost
    {
        $this->passengerAppId = $passengerAppId;

        return $this;
    }

    public function getCustomConfig(): ?string
    {
        return $this->customConfig;
    }

    public function setCustomConfig(?string $customConfig): VirtualHost
    {
        Validator::value($customConfig)
            ->nullable()
            ->maxLength(65535)
            ->pattern('^[ -~\n]+$')
            ->validate();

        $this->customConfig = $customConfig;

        return $this;
    }

    public function getServerSoftwareName(): string
    {
        return $this->serverSoftwareName;
    }

    public function setServerSoftwareName(string $serverSoftwareName): VirtualHost
    {
        Validator::value($serverSoftwareName)
            ->valueIn(VirtualHostServerSoftwareName::AVAILABLE)
            ->validate();

        $this->serverSoftwareName = $serverSoftwareName;

        return $this;
    }

    public function getDomainRoot(): ?string
    {
        return $this->domainRoot;
    }

    public function setDomainRoot(?string $domainRoot): VirtualHost
    {
        Validator::value($domainRoot)
            ->nullable()
            ->path()
            ->validate();

        $this->domainRoot = $domainRoot;

        return $this;
    }

    public function getAllowOverrideDirectives(): ?array
    {
        if ($this->getServerSoftwareName() === VirtualHostServerSoftwareName::SERVER_SOFTWARE_NGINX) {
            return null;
        }

        if (is_null($this->allowOverrideDirectives)) {
            return AllowOverrideDirectives::DEFAULTS;
        }

        return $this->allowOverrideDirectives;
    }

    public function setAllowOverrideDirectives(?array $allowOverrideDirectives): VirtualHost
    {
        Validator::value($allowOverrideDirectives)
            ->nullable()
            ->valuesIn(AllowOverrideDirectives::AVAILABLE)
            ->unique()
            ->validate();

        $this->allowOverrideDirectives = $allowOverrideDirectives;

        return $this;
    }

    public function getAllowOverrideOptionDirectives(): ?array
    {
        if ($this->getServerSoftwareName() === VirtualHostServerSoftwareName::SERVER_SOFTWARE_NGINX) {
            return null;
        }

        if (is_null($this->allowOverrideOptionDirectives)) {
            return AllowOverrideOptionDirectives::DEFAULTS;
        }

        return $this->allowOverrideOptionDirectives;
    }

    public function setAllowOverrideOptionDirectives(?array $allowOverrideOptionDirectives): VirtualHost
    {
        Validator::value($allowOverrideOptionDirectives)
            ->nullable()
            ->valuesIn(AllowOverrideOptionDirectives::AVAILABLE)
            ->unique()
            ->validate();

        $this->allowOverrideOptionDirectives = $allowOverrideOptionDirectives;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): VirtualHost
    {
        $this->id = $id;

        return $this;
    }

    public function getClusterId(): ?int
    {
        return $this->clusterId;
    }

    public function setClusterId(?int $clusterId): VirtualHost
    {
        $this->clusterId = $clusterId;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): VirtualHost
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): VirtualHost
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function fromArray(array $data): VirtualHost
    {
        return $this
            ->setDomain(Arr::get($data, 'domain'))
            ->setServerAliases(Arr::get($data, 'server_aliases', []))
            ->setUnixUserId(Arr::get($data, 'unix_user_id'))
            ->setDocumentRoot(Arr::get($data, 'document_root'))
            ->setPublicRoot(Arr::get($data, 'public_root'))
            ->setFpmPoolId(Arr::get($data, 'fpm_pool_id'))
            ->setPassengerAppId(Arr::get($data, 'passenger_app_id'))
            ->setDomainRoot(Arr::get($data, 'domain_root'))
            ->setCustomConfig(Arr::get($data, 'custom_config'))
            ->setAllowOverrideDirectives(Arr::get($data, 'allow_override_directives'))
            ->setAllowOverrideOptionDirectives(Arr::get($data, 'allow_override_option_directives'))
            ->setServerSoftwareName(Arr::get($data, 'server_software_name'))
            ->setId(Arr::get($data, 'id'))
            ->setClusterId(Arr::get($data, 'cluster_id'))
            ->setCreatedAt(Arr::get($data, 'created_at'))
            ->setUpdatedAt(Arr::get($data, 'updated_at'));
    }

    public function toArray(): array
    {
        return [
            'domain' => $this->getDomain(),
            'server_aliases' => $this->getServerAliases(),
            'unix_user_id' => $this->getUnixUserId(),
            'document_root' => $this->getDocumentRoot(),
            'public_root' => $this->getPublicRoot(),
            'fpm_pool_id' => $this->getFpmPoolId(),
            'passenger_app_id' => $this->getPassengerAppId(),
            'custom_config' => $this->getCustomConfig(),
            'id' => $this->getId(),
            'cluster_id' => $this->getClusterId(),
            'domain_root' => $this->getDomainRoot(),
            'allow_override_directives' => $this->getAllowOverrideDirectives(),
            'allow_override_option_directives' => $this->getAllowOverrideOptionDirectives(),
            'server_software_name' => $this->getServerSoftwareName(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
