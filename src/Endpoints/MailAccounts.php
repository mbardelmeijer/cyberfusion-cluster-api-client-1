<?php

namespace Cyberfusion\ClusterApi\Endpoints;

use DateTimeInterface;
use Cyberfusion\ClusterApi\Enums\TimeUnit;
use Cyberfusion\ClusterApi\Exceptions\RequestException;
use Cyberfusion\ClusterApi\Models\MailAccount;
use Cyberfusion\ClusterApi\Models\MailAccountUsage;
use Cyberfusion\ClusterApi\Request;
use Cyberfusion\ClusterApi\Response;
use Cyberfusion\ClusterApi\Support\ListFilter;

class MailAccounts extends Endpoint
{
    /**
     * @param ListFilter|null $filter
     * @return Response
     * @throws RequestException
     */
    public function list(ListFilter $filter = null): Response
    {
        if (is_null($filter)) {
            $filter = new ListFilter();
        }

        $request = (new Request())
            ->setMethod(Request::METHOD_GET)
            ->setUrl(sprintf('mail-accounts?%s', $filter->toQuery()));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        return $response->setData([
            'mailAccounts' => array_map(
                function (array $data) {
                    return (new MailAccount())->fromArray($data);
                },
                $response->getData()
            ),
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws RequestException
     */
    public function get(int $id): Response
    {
        $request = (new Request())
            ->setMethod(Request::METHOD_GET)
            ->setUrl(sprintf('mail-accounts/%d', $id));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        return $response->setData([
            'mailAccount' => (new MailAccount())->fromArray($response->getData()),
        ]);
    }

    /**
     * @param int $id
     * @param DateTimeInterface $from
     * @param string $timeUnit
     * @return Response
     * @throws RequestException
     */
    public function usages(int $id, DateTimeInterface $from, string $timeUnit = TimeUnit::HOURLY): Response
    {
        $url = sprintf(
            'mail-accounts/usages/%d?%s',
            $id,
            http_build_query([
                'timestamp' => $from->format('c'),
                'time_unit' => $timeUnit,
            ])
        );

        $request = (new Request())
            ->setMethod(Request::METHOD_GET)
            ->setUrl($url);

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        return $response->setData([
            'mailAccountUsage' => (new MailAccountUsage())->fromArray($response->getData()),
        ]);
    }

    /**
     * @param MailAccount $mailAccount
     * @return Response
     * @throws RequestException
     */
    public function create(MailAccount $mailAccount): Response
    {
        $this->validateRequired($mailAccount, 'create', [
            'local_part',
            'password',
            'mail_domain_id',
        ]);

        $request = (new Request())
            ->setMethod(Request::METHOD_POST)
            ->setUrl('mail-accounts')
            ->setBody($this->filterFields($mailAccount->toArray(), [
                'local_part',
                'password',
                'quota',
                'mail_domain_id',
            ]));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $mailAccount = (new MailAccount())->fromArray($response->getData());

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($mailAccount->getClusterId());

        return $response->setData([
            'mailAccount' => $mailAccount,
        ]);
    }

    /**
     * @param MailAccount $mailAccount
     * @return Response
     * @throws RequestException
     */
    public function update(MailAccount $mailAccount): Response
    {
        $this->validateRequired($mailAccount, 'update', [
            'local_part',
            'password',
            'mail_domain_id',
            'id',
            'cluster_id',
        ]);

        $request = (new Request())
            ->setMethod(Request::METHOD_PUT)
            ->setUrl(sprintf('mail-accounts/%d', $mailAccount->getId()))
            ->setBody($this->filterFields($mailAccount->toArray(), [
                'local_part',
                'password',
                'quota',
                'mail_domain_id',
                'id',
                'cluster_id',
            ]));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $mailAccount = (new MailAccount())->fromArray($response->getData());

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($mailAccount->getClusterId());

        return $response->setData([
            'mailAccount' => $mailAccount,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws RequestException
     */
    public function delete(int $id): Response
    {
        // Log the affected cluster by retrieving the model first
        $result = $this->get($id);
        if ($result->isSuccess()) {
            $clusterId = $result
                ->getData('mailAccount')
                ->getClusterId();

            $this
                ->client
                ->addAffectedCluster($clusterId);
        }

        $request = (new Request())
            ->setMethod(Request::METHOD_DELETE)
            ->setUrl(sprintf('mail-accounts/%d', $id));

        return $this
            ->client
            ->request($request);
    }
}
