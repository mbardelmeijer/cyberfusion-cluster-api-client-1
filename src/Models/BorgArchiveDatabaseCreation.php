<?php

namespace Cyberfusion\ClusterApi\Models;

use Cyberfusion\ClusterApi\Support\Arr;
use Cyberfusion\ClusterApi\Contracts\Model;
use Cyberfusion\ClusterApi\Support\Validator;

class BorgArchiveDatabaseCreation extends ClusterModel implements Model
{
    private string $name;
    private int $databaseId;
    private int $borgRepositoryId;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): BorgArchiveDatabaseCreation
    {
        Validator::value($name)
            ->maxLength(64)
            ->pattern('^[a-zA-Z0-9-_]+$')
            ->validate();

        $this->name = $name;

        return $this;
    }

    public function getDatabaseId(): int
    {
        return $this->databaseId;
    }

    public function setDatabaseId(int $databaseId): BorgArchiveDatabaseCreation
    {
        $this->databaseId = $databaseId;

        return $this;
    }

    public function getBorgRepositoryId(): int
    {
        return $this->borgRepositoryId;
    }

    public function setBorgRepositoryId(int $borgRepositoryId): BorgArchiveDatabaseCreation
    {
        $this->borgRepositoryId = $borgRepositoryId;

        return $this;
    }

    public function fromArray(array $data): BorgArchiveDatabaseCreation
    {
        return $this
            ->setName(Arr::get($data, 'name'))
            ->setDatabaseId(Arr::get($data, 'database_id'))
            ->setBorgRepositoryId(Arr::get($data, 'borg_repository_id'));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'database_id' => $this->getDatabaseId(),
            'borg_repository_id' => $this->getBorgRepositoryId(),
        ];
    }
}
