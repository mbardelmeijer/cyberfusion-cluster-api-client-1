<?php

namespace Cyberfusion\ClusterApi\Endpoints;

use Cyberfusion\ClusterApi\Exceptions\RequestException;
use Cyberfusion\ClusterApi\Models\Cms;
use Cyberfusion\ClusterApi\Models\CmsOption;
use Cyberfusion\ClusterApi\Models\CmsConfigurationConstant;
use Cyberfusion\ClusterApi\Models\CmsInstallation;
use Cyberfusion\ClusterApi\Models\TaskCollection;
use Cyberfusion\ClusterApi\Request;
use Cyberfusion\ClusterApi\Response;
use Cyberfusion\ClusterApi\Support\ListFilter;
use Cyberfusion\ClusterApi\Support\Str;

class Cmses extends Endpoint
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
            ->setUrl(sprintf('cmses?%s', $filter->toQuery()));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        return $response->setData([
            'cmses' => array_map(
                function (array $data) {
                    return (new Cms())->fromArray($data);
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
            ->setUrl(sprintf('cmses/%d', $id));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        return $response->setData([
            'cms' => (new Cms())->fromArray($response->getData()),
        ]);
    }

    /**
     * @param Cms $cms
     * @return Response
     * @throws RequestException
     */
    public function create(Cms $cms): Response
    {
        $this->validateRequired($cms, 'create', [
            'software_name',
            'virtual_host_id',
        ]);

        $request = (new Request())
            ->setMethod(Request::METHOD_POST)
            ->setUrl('cmses')
            ->setBody($this->filterFields($cms->toArray(), [
                'software_name',
                'is_manually_created',
                'virtual_host_id',
            ]));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $cms = (new Cms())->fromArray($response->getData());

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($cms->getClusterId());

        return $response->setData([
            'cms' => $cms,
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
                ->getData('cms')
                ->getClusterId();

            $this
                ->client
                ->addAffectedCluster($clusterId);
        }

        $request = (new Request())
            ->setMethod(Request::METHOD_DELETE)
            ->setUrl(sprintf('cmses/%d', $id));

        return $this
            ->client
            ->request($request);
    }

    /**
     * @param int $id
     * @param CmsInstallation $cmsInstallation
     * @param string|null $callbackUrl
     * @return Response
     * @throws RequestException
     */
    public function install(int $id, CmsInstallation $cmsInstallation, string $callbackUrl = null): Response
    {
        $this->validateRequired($cmsInstallation, 'create', [
            'database_name',
            'database_user_name',
            'database_user_password',
            'database_host',
            'site_title',
            'site_url',
            'locale',
            'version',
            'admin_username',
            'admin_password',
            'admin_email_address',
        ]);

        $url = Str::optionalQueryParameters(
            sprintf('cmses/%d/install', $id),
            ['callback_url' => $callbackUrl]
        );

        $request = (new Request())
            ->setMethod(Request::METHOD_POST)
            ->setUrl($url)
            ->setBody($this->filterFields($cmsInstallation->toArray(), [
                'database_name',
                'database_user_name',
                'database_user_password',
                'database_host',
                'site_title',
                'site_url',
                'locale',
                'version',
                'admin_username',
                'admin_password',
                'admin_email_address',
            ]));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $taskCollection = (new TaskCollection())->fromArray($response->getData());

        // Retrieve the CMS again, so we log affected clusters and can return the CMS object
        $retrieveResponse = $this->get($id);
        if (!$retrieveResponse->isSuccess()) {
            return $retrieveResponse;
        }

        $cms = $retrieveResponse->getData('cms');

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($cms->getClusterId());

        return $response->setData([
            'taskCollection' => $taskCollection,
            'cms' => $cms,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws RequestException
     */
    public function oneTimeLogin(int $id): Response
    {
        $request = (new Request())
            ->setMethod(Request::METHOD_GET)
            ->setUrl(sprintf('cmses/%d/one-time-login', $id));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        return $response->setData([
            'url' => $response->getData('url'),
        ]);
    }

    /**
     * @param int $id
     * @param CmsOption $cmsOption
     * @return Response
     * @throws RequestException
     */
    public function updateOption(int $id, CmsOption $cmsOption): Response
    {
        $this->validateRequired($cmsOption, 'update', [
            'name',
            'value',
        ]);

        $request = (new Request())
            ->setMethod(Request::METHOD_PUT)
            ->setUrl(sprintf('cmses/%d/options/%d', $id, $cmsOption->getName()))
            ->setBody($this->filterFields($cmsOption->toArray(), [
                'name',
                'value',
            ]));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $cmsOption = (new CmsOption())->fromArray($response->getData());

        // Retrieve the CMS again, so we log affected clusters and can return the CMS object
        $retrieveResponse = $this->get($id);
        if (!$retrieveResponse->isSuccess()) {
            return $retrieveResponse;
        }

        $cms = $retrieveResponse->getData('cms');

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($cms->getClusterId());

        return $response->setData([
            'cmsOption' => $cmsOption,
            'cms' => $cms,
        ]);
    }

    /**
     * @param int $id
     * @param CmsConfigurationConstant $cmsConfigurationConstant
     * @return Response
     * @throws RequestException
     */
    public function updateConfigurationConstant(int $id, CmsConfigurationConstant $cmsConfigurationConstant): Response
    {
        $this->validateRequired($cmsConfigurationConstant, 'update', [
            'name',
            'value',
        ]);

        $request = (new Request())
            ->setMethod(Request::METHOD_PUT)
            ->setUrl(sprintf('cmses/%d/configuration-constants/%d', $id, $cmsConfigurationConstant->getName()))
            ->setBody($this->filterFields($cmsConfigurationConstant->toArray(), [
                'name',
                'value',
            ]));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $cmsConfigurationConstant = (new CmsConfigurationConstant())->fromArray($response->getData());

        // Retrieve the CMS again, so we log affected clusters and can return the CMS object
        $retrieveResponse = $this->get($id);
        if (!$retrieveResponse->isSuccess()) {
            return $retrieveResponse;
        }

        $cms = $retrieveResponse->getData('cms');

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($cms->getClusterId());

        return $response->setData([
            'cmsConfigurationConstant' => $cmsConfigurationConstant,
            'cms' => $cms,
        ]);
    }

    /**
     * @param int $id
     * @param string $searchString
     * @param string $replaceString
     * @param string|null $callbackUrl
     * @return Response
     * @throws RequestException
     */
    public function searchReplace(int $id, string $searchString, string $replaceString, string $callbackUrl = null): Response
    {
        $url = Str::optionalQueryParameters(
            sprintf('cmses/%d/search-replace?search_string=%d&replace_string=%d',
                $id,
                $searchString,
                $replaceString
            ),
            ['callback_url' => $callbackUrl]
        );

        $request = (new Request())
            ->setMethod(Request::METHOD_POST)
            ->setUrl($url);

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        $taskCollection = (new TaskCollection())->fromArray($response->getData());

        return $response->setData([
            'taskCollection' => $taskCollection,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws RequestException
     */
    public function regenerateSalts(int $id): Response
    {
        $request = (new Request())
            ->setMethod(Request::METHOD_POST)
            ->setUrl(sprintf('cmses/%d/regenerate-salts', $id));

        $response = $this
            ->client
            ->request($request);
        if (!$response->isSuccess()) {
            return $response;
        }

        // Retrieve the CMS again, so we log affected clusters and can return the CMS object
        $retrieveResponse = $this->get($id);
        if (!$retrieveResponse->isSuccess()) {
            return $retrieveResponse;
        }

        $cms = $retrieveResponse->getData('cms');

        // Log which cluster is affected by this change
        $this
            ->client
            ->addAffectedCluster($cms->getClusterId());

        return $response->setData([
            'cms' => $cms,
        ]);
    }
}
