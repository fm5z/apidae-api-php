<?php

namespace Apidae\ApiClient;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Exception\CommandClientException;
use GuzzleHttp\Command\Exception\CommandException;
use GuzzleHttp\Command\Exception\CommandServerException;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\ResultInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use Apidae\ApiClient\Description\Agenda;
use Apidae\ApiClient\Description\Exports;
use Apidae\ApiClient\Description\Metadata;
use Apidae\ApiClient\Description\Reference;
use Apidae\ApiClient\Description\Search;
use Apidae\ApiClient\Description\Sso;
use Apidae\ApiClient\Description\TouristicObjects;
use Apidae\ApiClient\Description\User;
use Apidae\ApiClient\Exception\InvalidExportDirectoryException;
use Apidae\ApiClient\Exception\InvalidMetadataFormatException;
use Apidae\ApiClient\Exception\ApidaeException;
use Apidae\ApiClient\Subscriber\AuthenticationSubscriber;
use Apidae\ApiClient\Subscriber\ObjectsGlobalConfigSubscriber;

/**
 * Magic operations:
 *
 * @method array getObjectById() getObjectById(array $params)
 * @method array getObjectByIdentifier() getObjectByIdentifier(array $params)
 *
 * @method array getMetadata() getMetadata(array $params)
 * @method array deleteMetadata() deleteMetadata(array $params)
 * @method array putMetadata() putMetadata(array $params)
 *
 * @method array confirmExport() confirmExport(array $params)
 *
 * @method array searchObject() searchObject(array $params)
 * @method array searchObjectIdentifier() searchObjectIdentifier(array $params)
 *
 * @method array searchAgenda() searchAgenda(array $params)
 * @method array searchAgendaIdentifier() searchAgendaIdentifier(array $params)
 * @method array searchDetailedAgenda() searchDetailedAgenda(array $params)
 * @method array searchDetailedAgendaIdentifier() searchDetailedAgendaIdentifier(array $params)
 *
 * @method array getReferenceCity() getReferenceCity(array $params)
 * @method array getReferenceElement() getReferenceElement(array $params)
 * @method array getReferenceInternalCriteria() getReferenceInternalCriteria(array $params)
 * @method array getReferenceSelection() getReferenceSelection(array $params)
 *
 * @method array getSsoToken() getSsoToken(array $params)
 * @method array refreshSsoToken() refreshSsoToken(array $params)
 *
 * @method array getUserProfile() getUserProfile()
 * @method array getUserPermissionOnObject() getUserPermissionOnObject(array $params)
 */
class Client extends GuzzleClient
{
    protected $config = [
      'baseUri'       => 'https://api.apidae-tourisme.com/',
      'apiKey'        => null,
      'projectId'     => null,

        // Auth for metadata
      'OAuthClientId' => null,
      'OAuthSecret'   => null,

        // Export
      'exportDir'     => '/tmp/apidaeExports/',

        // For object lists
      'responseFields' => [],
      'locales'        => [],
      'count'          => 20,

        // For HTTP Client
      'timeout'           => 0,
      'connectTimeout'    => 0,
      'proxy'             => null,
      'verify'            => true,

        // For SSO authentication
      'ssoBaseUrl'        => 'https://base.apidae-tourisme.com',
      'ssoRedirectUrl'    => 'http://localhost/',
      'ssoClientId'       => null,
      'ssoSecret'         => null,

        // Access tokens by scope
      'accessTokens'      => [],
    ];

    /**
     * @param array $config
     * @param Description $description
     */
    public function __construct(array $config = [], Description $description = null)
    {
        if ($description === null) {
            $operations = array_merge(
              TouristicObjects::$operations,
              Metadata::$operations,
              Exports::$operations,
              Search::$operations,
              Agenda::$operations,
              Reference::$operations,
              Sso::$operations,
              User::$operations
            );

            $descriptionData = [
              'baseUri' => $this->config['baseUri'],
              'operations' => $operations,
              'models' => [
                'getResponse' => [
                  'type' => 'object',
                  'additionalProperties' => [
                    'location' => 'json',
                  ],
                ],
              ],
            ];

            $description = new Description($descriptionData);
        }

        $this->config = new \ArrayObject(array_merge($this->config, $config));

        if (isset($this->config['handler']) === false) {
            $this->config['handler'] = HandlerStack::create();

        }

        $config = array_filter([
          'base_uri' => $this->config['baseUri'],
          'timeout'           => $this->config['timeout'],
          'connect_timeout'   => $this->config['connectTimeout'],
          'proxy'             => $this->config['proxy'],
          'verify'            => $this->config['verify'],
          'handler'           => $this->config['handler'],
        ]);

        $client = new BaseClient($config);

        parent::__construct($client, $description, new ApidaeSerializer($description, $client, [], (array) $this->config));

        $stack = $this->getHandlerStack();
        $stack->before('validate_description', new ObjectsGlobalConfigSubscriber($description, (array) $this->config), 'objects_global_config');
        $stack->before('validate_description', new AuthenticationSubscriber($description, (array) $this->config), 'authentication');
    }

    /**
     * Download and read zip export
     *
     * @param  array                            $params
     * @return \Symfony\Component\Finder\Finder
     */
    public function getExportFiles(array $params)
    {
        $client = $this->getHttpClient();

        if (empty($params['url'])) {
            throw new \InvalidArgumentException("Missing 'url' parameter! Must be the 'urlRecuperation' you got from the notification.");
        }

        if (preg_match('/\.zip$/i', $params['url']) !== 1) {
            throw new \InvalidArgumentException("'url' parameter does not looks good! Must be the 'urlRecuperation' you got from the notification.");
        }

        $temporaryDirectory = $this->getExportDirectory();
        $exportPath         = sprintf('%s/%s', realpath($temporaryDirectory), date('Y-m-d-His'));
        $zipFullPath        = sprintf('%s/export.zip', $exportPath);
        $exportFullPath     = sprintf('%s/export/', $exportPath);

        mkdir($exportPath);
        mkdir($exportFullPath);

        $resource = fopen($zipFullPath, 'w');

        // Download the ZIP file in temp directory
        try {
            $client->get($params['url'], ['sink' => $resource]);
        } catch (\Exception $e) {
            $this->handleHttpError($e);
        }

        // Extract the ZIP file
        try {
          $zip = new \ZipArchive;
          if ($zip->open($zipFullPath) === TRUE) {
            $zip->extractTo($exportFullPath);
            $zip->close();
          }
        } catch (\Exception $e) {
          $this->handleHttpError($e);
        }
        
        return $exportFullPath;
    }

    /**
     * Remove all ZIP and exported files from the exportDir (cleanup files we have created)
     *
     * @return bool
     */
    public function cleanExportFiles() : bool
    {
        $exportDirectory = $this->getExportDirectory();

        $iterator = new \RecursiveDirectoryIterator(
          $exportDirectory,
          \RecursiveDirectoryIterator::SKIP_DOTS
        );

        $files = new \RecursiveIteratorIterator(
          $iterator,
          \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getSsoUrl() : string
    {
        $params = array(
          'response_type' => 'code',
          'client_id'     => $this->config['ssoClientId'],
          'redirect_uri'  => $this->config['ssoRedirectUrl'],
          'scope'         => AuthenticationHandler::SSO_SCOPE,
        );

        $query = \GuzzleHttp\Psr7\build_query($params);

        $uri = new Uri($this->config['ssoBaseUrl']);
        $uri = $uri->withPath('/oauth/authorize');
        $uri = $uri->withQuery($query);

        return (string) $uri;
    }

    /**
     * @param $scope
     * @param $token
     */
    public function setAccessToken($scope, $token)
    {
        $this->config['accessTokens'][$scope] = $token;
    }

    /**
     * Execute a single command.
     *
     * @param CommandInterface $command Command to execute
     *
     * @return ResultInterface The result of the executed command
     * @throws CommandException
     */
    public function execute(CommandInterface $command) : ResultInterface
    {
        try {
            return $this->executeAsync($command)->wait();
        } catch (\Exception $e) {
            $this->handleHttpError($e);
        }
    }

    /**
     * @param \Exception $e
     * @throws ApidaeException
     * @throws \Exception
     */
    private function handleHttpError(\Exception $e)
    {
        if ($e->getPrevious() instanceof InvalidMetadataFormatException) {
            throw $e->getPrevious();
        }
        if ($e instanceof CommandClientException) {
          throw new ApidaeException($e);
        }

        if ($e instanceof CommandServerException) {
          throw new ApidaeException($e);
        }

        if ($e instanceof CommandException) {
          throw $e;
        }

        if ($e instanceof RequestException) {
          throw new ApidaeException($e);
        }

        throw $e;
    }

    /**
     * @return SpecificDirectory
     */
    private function getExportDirectory()
    {
      $dir = $this->config['exportDir'];

        if (!file_exists($dir)) {
            mkdir($dir);
        }

        if (!file_exists($dir)) {
            throw new InvalidExportDirectoryException();
        }

        return $dir;
    }
}