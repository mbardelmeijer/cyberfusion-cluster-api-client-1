# Cyberfusion Cluster API client

Client for the [Cyberfusion Cluster API](https://cluster-api.cyberfusion.nl/).

This client was built for and tested on the **1.156** version of the API.

## Support

This client is officially supported by Cyberfusion. If you have any questions, open an issue on GitHub or send an email to support@cyberfusion.nl.

The client was created by Vdhicts, a company which develops and implements IT solutions for businesses and educational institutions.

## Requirements

This client requires PHP 7.4 or higher and uses Guzzle. PHP 7.4 is supported until its EOL date (November 28, 2022).

## Installation

This client can be used in any PHP project and with any framework.

Install the client with Composer:

`composer require cyberfusion/cluster-api-client`

## Usage

Refer to the [API documentation](https://cluster-api.cyberfusion.nl/) for information about API requests.

### Getting started

```php
use Cyberfusion\ClusterApi\Client;
use Cyberfusion\ClusterApi\Configuration;
use Cyberfusion\ClusterApi\ClusterApi;

// Create the configuration with your username/password
$configuration = Configuration::withCredentials('username', 'password');

// Start the client once and authorize
$client = new Client($configuration);

// Initialize the API
$api = new ClusterApi($client);

// Perform the request
$result = $api->virtualHosts()->list();

// Access the virtual host models
$virtualHosts = $result->getData('virtualHosts');
```

### Sandbox mode

To test your implementation, enable the sandbox mode. Changes won't be made to production clusters.

To enable sandbox mode, use the third parameter of the configuration, or set it separately:

```php
$configuration = Configuration::withCredentials('username', 'password', true);
```

... or:

```php
$configuration = (new Configuration())
    ->setUsername('username')
    ->setPassword('password')
    ->setSandbox(true);
```

### Requests

The endpoint methods may ask for filters, models and IDs. The method type hints tell you which input is requested.

#### Models

The endpoint may request a model. Most create and update requests do.

```php
$unixUserUsername = 'foo';

$unixUser = (new UnixUser())
    ->setUsername($unixUserUsername)
    ->setPassword('bar')
    ->setDefaultPhpVersion('7.4')
    ->setVirtualHostsDirectory(sprintf('/home/%d', $unixUserUsername))
    ->setClusterId(1);

$result = $api
    ->unixUsers()
    ->create($unixUser);
```

When models need to be provided, the required properties are checked before executing the request.

`RequestException` is thrown when properties are missing. See the error message for more details.

#### Filtering data

Some endpoints require a `ListFilter` and return a list of models according to the filter. It's also possible to change the sort order.

A `ListFilter` can be initialized for a model, so it automatically validates if the provided fields are available for the model.

```php
$listFilter = ListFilter::forModel(new Cluster());
$listFilter->addFilter('name', 'test');
$listFilter->addFilter('groups', 'test2');
$listFilter->addSort('name', ListFilter::SORT_DESC);
```

You are able to initialize the `ListFilter` manually, but fields are not validated in that case.

#### Manually make requests

To use the API directly, use the `request()` method on the `Client`. This method needs a `Request` class. See the class itself for its options.

### Responses

The endpoint methods throw exceptions when requests fail due to timeouts. When the API replies with HTTP protocol errors, the `Response` class is returned nonetheless. Check if the request succeeded with: `$response->isSuccess()`. API responses are automatically converted to models.

### Authentication

The API is authenticated with a username and password and returns an access token. This client takes care of authentication. To get credentials, contact Cyberfusion.

```php
$configuration = Configuration::withCredentials('username', 'password');
```

When you authenticate with username and password, this client automatically retrieves the access token.

The access token is valid for 30 minutes, so there's no need to store it. To store it anyway, access it with `$configuration->getAccessToken()`.

#### Manually authenticate

```php
use Cyberfusion\ClusterApi\Client;
use Cyberfusion\ClusterApi\ClusterApi;
use Cyberfusion\ClusterApi\Configuration;
use Cyberfusion\ClusterApi\Models\Login;

// Initialize the configuration without any credentials or access token
$configuration = new Configuration();

// Start the client with manual authentication
$client = new Client($configuration, true);

// Initialize the API
$api = new ClusterApi($client);

// Create the request
$login = (new Login())
    ->setUsername('username')
    ->setPassword('password');

// Perform the request
$response = $api
    ->authentication()
    ->login($login);

// Store the access token in the configuration
if ($response->isSuccess()) {
    $configuration->setAccessToken($response->getData('access_token'));
}
```

### Enums

Some properties should contain certain values. These values can be found in the enum classes.

### Exceptions

In case of errors, the client throws an exception which extends `ClusterApiException`.

All exceptions have a code. These can be found in the `ClusterApiException` class.

### Deployment

Change to most of the objects in the Cluster API require a deployment of the cluster. See the [API documentation](https://cluster-api.cyberfusion.nl/redoc#operation/create_cluster_deployment_api_v1_cluster_deployments_post) for more information.

This client keeps track of changed clusters. The `deploy` method on the client automatically deploys all changed clusters:

```php
$clusterDeployments = $client->deploy();
```

The result is an array of `Deployment` objects (or an empty array who no clusters are changed) which allows you to check if the cluster is properly deployed:

```php
foreach ($clusterDeployments as $clusterDeployment) {
    $success = $clusterDeployment->isSuccess();
    if (!$success) {
        // Do something with $clusterDeployment->getError();
    }
}
```

See the `Deployment` class for more options.

#### Automatic deployment

This client is also able to deploy all changed clusters automatically. This is opt-in behavior, as you won't be able to access the result of the deployment.

```php
$configuration = new Configuration();
$configuration
    ->setAutoDeploy() // Enable the auto deployment of changed clusters
    ->setAutoDeployCallbackUrl(''); // Provide the callback url for automatic deployments

// Initialize the client
$client = new Client($configuration, true);

// Initialize the API
$api = new ClusterApi($client);
```

#### Manual deployment

```php
$api
    ->clusters()
    ->commit($clusterId, $callbackUrl = null);
```

### Laravel

This client can be easily used in any Laravel application. Add your API credentials to the `.env` file:

```
CLUSTER_USERNAME=username
CLUSTER_PASSWORD=password
```

Next, create a config file called `cluster.php` in the `config` directory:

```php
<?php

return [
    'username' => env('CLUSTER_USERNAME'),
    'password' => env('CLUSTER_PASSWORD'),
];
```

Use those files to build the configuration:

```php
$configuration = Configuration::withCredentials(config('cluster.username'), config('cluster.password'));
```

## Tests

Unit tests are available in the `tests` directory. Run:

`composer test`

To generate a code coverage report in the `build/report` directory, run:

`composer test-coverage`

## Contribution

Contributions are welcome. See the [contributing guidelines](CONTRIBUTING.md).

## Security

If you discover any security related issues, please email opensource@cyberfusion.nl instead of using the issue tracker.

## License

This client is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
