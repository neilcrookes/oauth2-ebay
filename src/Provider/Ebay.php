<?php

namespace NeilCrookes\OAuth2\Client\Provider;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use NeilCrookes\OAuth2\Client\Token\EbayAccessToken;
use Psr\Http\Message\ResponseInterface;

class Ebay extends AbstractProvider
{
    use BearerAuthorizationTrait, ArrayAccessorTrait;

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = '';

    const SITE_ID_EBAY_US    = 0;              // eBay United States
    const SITE_ID_EBAY_ENCA  = 2;              // eBay Canada (English)
    const SITE_ID_EBAY_GB    = 3;              // eBay UK
    const SITE_ID_EBAY_AU    = 15;             // eBay Australia
    const SITE_ID_EBAY_AT    = 16;             // eBay Austria
    const SITE_ID_EBAY_FRBE  = 23;             // eBay Belgium (French)
    const SITE_ID_EBAY_FR    = 71;             // eBay France
    const SITE_ID_EBAY_DE    = 77;             // eBay Germany
    const SITE_ID_EBAY_MOTOR = 100;            // eBay Motors
    const SITE_ID_EBAY_IT    = 101;            // eBay Italy
    const SITE_ID_EBAY_NLBE  = 123;            // eBay Belgium (Dutch)
    const SITE_ID_EBAY_NL    = 146;            // eBay Netherlands
    const SITE_ID_EBAY_ES    = 186;            // eBay Spain
    const SITE_ID_EBAY_CH    = 193;            // eBay Switzerland
    const SITE_ID_EBAY_HK    = 201;            // eBay Hong Kong
    const SITE_ID_EBAY_IN    = 203;            // eBay India
    const SITE_ID_EBAY_IE    = 205;            // eBay Ireland
    const SITE_ID_EBAY_MY    = 207;            // eBay Malaysia
    const SITE_ID_EBAY_FRCA  = 210;            // eBay Canada (French)
    const SITE_ID_EBAY_PH    = 211;            // eBay Philippines
    const SITE_ID_EBAY_PL    = 212;            // eBay Poland
    const SITE_ID_EBAY_SG    = 216;            // eBay Singapore
    
    const GLOBAL_ID_EBAY_US    = 'EBAY_US';    // eBay United States
    const GLOBAL_ID_EBAY_ENCA  = 'EBAY_ENCA';  // eBay Canada (English)
    const GLOBAL_ID_EBAY_GB    = 'EBAY_GB';    // eBay UK
    const GLOBAL_ID_EBAY_AU    = 'EBAY_AU';    // eBay Australia
    const GLOBAL_ID_EBAY_AT    = 'EBAY_AT';    // eBay Austria
    const GLOBAL_ID_EBAY_FRBE  = 'EBAY_FRBE';  // eBay Belgium (French)
    const GLOBAL_ID_EBAY_FR    = 'EBAY_FR';    // eBay France
    const GLOBAL_ID_EBAY_DE    = 'EBAY_DE';    // eBay Germany
    const GLOBAL_ID_EBAY_MOTOR = 'EBAY_MOTOR'; // eBay Motors
    const GLOBAL_ID_EBAY_IT    = 'EBAY_IT';    // eBay Italy
    const GLOBAL_ID_EBAY_NLBE  = 'EBAY_NLBE';  // eBay Belgium (Dutch)
    const GLOBAL_ID_EBAY_NL    = 'EBAY_NL';    // eBay Netherlands
    const GLOBAL_ID_EBAY_ES    = 'EBAY_ES';    // eBay Spain
    const GLOBAL_ID_EBAY_CH    = 'EBAY_CH';    // eBay Switzerland
    const GLOBAL_ID_EBAY_HK    = 'EBAY_HK';    // eBay Hong Kong
    const GLOBAL_ID_EBAY_IN    = 'EBAY_IN';    // eBay India
    const GLOBAL_ID_EBAY_IE    = 'EBAY_IE';    // eBay Ireland
    const GLOBAL_ID_EBAY_MY    = 'EBAY_MY';    // eBay Malaysia
    const GLOBAL_ID_EBAY_FRCA  = 'EBAY_FRCA';  // eBay Canada (French)
    const GLOBAL_ID_EBAY_PH    = 'EBAY_PH';    // eBay Philippines
    const GLOBAL_ID_EBAY_PL    = 'EBAY_PL';    // eBay Poland
    const GLOBAL_ID_EBAY_SG    = 'EBAY_SG';    // eBay Singapore

    private $defaultGlobalId = self::GLOBAL_ID_EBAY_US;

    private $globalIdToSideIdMap = [
        self::GLOBAL_ID_EBAY_US    => self::SITE_ID_EBAY_US,    // eBay United States
        self::GLOBAL_ID_EBAY_ENCA  => self::SITE_ID_EBAY_ENCA,  // eBay Canada (English)
        self::GLOBAL_ID_EBAY_GB    => self::SITE_ID_EBAY_GB,    // eBay UK
        self::GLOBAL_ID_EBAY_AU    => self::SITE_ID_EBAY_AU,    // eBay Australia
        self::GLOBAL_ID_EBAY_AT    => self::SITE_ID_EBAY_AT,    // eBay Austria
        self::GLOBAL_ID_EBAY_FRBE  => self::SITE_ID_EBAY_FRBE,  // eBay Belgium (French)
        self::GLOBAL_ID_EBAY_FR    => self::SITE_ID_EBAY_FR,    // eBay France
        self::GLOBAL_ID_EBAY_DE    => self::SITE_ID_EBAY_DE,    // eBay Germany
        self::GLOBAL_ID_EBAY_MOTOR => self::SITE_ID_EBAY_MOTOR, // eBay Motors
        self::GLOBAL_ID_EBAY_IT    => self::SITE_ID_EBAY_IT,    // eBay Italy
        self::GLOBAL_ID_EBAY_NLBE  => self::SITE_ID_EBAY_NLBE,  // eBay Belgium (Dutch)
        self::GLOBAL_ID_EBAY_NL    => self::SITE_ID_EBAY_NL,    // eBay Netherlands
        self::GLOBAL_ID_EBAY_ES    => self::SITE_ID_EBAY_ES,    // eBay Spain
        self::GLOBAL_ID_EBAY_CH    => self::SITE_ID_EBAY_CH,    // eBay Switzerland
        self::GLOBAL_ID_EBAY_HK    => self::SITE_ID_EBAY_HK,    // eBay Hong Kong
        self::GLOBAL_ID_EBAY_IN    => self::SITE_ID_EBAY_IN,    // eBay India
        self::GLOBAL_ID_EBAY_IE    => self::SITE_ID_EBAY_IE,    // eBay Ireland
        self::GLOBAL_ID_EBAY_MY    => self::SITE_ID_EBAY_MY,    // eBay Malaysia
        self::GLOBAL_ID_EBAY_FRCA  => self::SITE_ID_EBAY_FRCA,  // eBay Canada (French)
        self::GLOBAL_ID_EBAY_PH    => self::SITE_ID_EBAY_PH,    // eBay Philippines
        self::GLOBAL_ID_EBAY_PL    => self::SITE_ID_EBAY_PL,    // eBay Poland
        self::GLOBAL_ID_EBAY_SG    => self::SITE_ID_EBAY_SG,    // eBay Singapore
    ];

    private $sandboxAuthorizeUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => 'https://auth.sandbox.ebay.com/oauth2/authorize',
        self::GLOBAL_ID_EBAY_FR => 'https://auth.sandbox.ebay.fr/oauth2/authorize',
    ];

    private $productionAuthorizeUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => 'https://auth.ebay.com/oauth2/authorize',
        self::GLOBAL_ID_EBAY_FR => 'https://auth.ebay.fr/oauth2/authorize',
    ];

    private $sandboxAccessTokenUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => 'https://api.sandbox.ebay.com/identity/v1/oauth2/token',
        self::GLOBAL_ID_EBAY_FR => 'https://api.sandbox.ebay.fr/identity/v1/oauth2/token',
    ];

    private $productionAccessTokenUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => 'https://api.ebay.com/identity/v1/oauth2/token',
        self::GLOBAL_ID_EBAY_FR => 'https://api.ebay.com/identity/v1/oauth2/token',
    ];

    private $sandboxResourceOwnerDetailsUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => 'https://api.sandbox.ebay.com/ws/api.dll',
        self::GLOBAL_ID_EBAY_FR => 'https://api.sandbox.ebay.com/ws/api.dll',
    ];

    private $productionResourceOwnerDetailsUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => 'https://api.ebay.com/ws/api.dll',
        self::GLOBAL_ID_EBAY_FR => 'https://api.ebay.com/ws/api.dll',
    ];
    
    /**
     * @var bool
     */
    protected $sandbox = false;

    /**
     * @var string
     */
    protected $globalId;

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        $authorizeUrlsByEbayGlobalId = $this->sandbox ? $this->sandboxAuthorizeUrlsByEbayGlobalId : $this->productionAuthorizeUrlsByEbayGlobalId;
        if (null !== $this->globalId && array_key_exists($this->globalId, $authorizeUrlsByEbayGlobalId))
        {
            return $authorizeUrlsByEbayGlobalId[$this->globalId];
        }
        return $authorizeUrlsByEbayGlobalId[$this->defaultGlobalId];
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        $accessTokenUrlsByEbayGlobalId = $this->sandbox ? $this->sandboxAccessTokenUrlsByEbayGlobalId : $this->productionAccessTokenUrlsByEbayGlobalId;
        if (null !== $this->globalId && array_key_exists($this->globalId, $accessTokenUrlsByEbayGlobalId))
        {
            return $accessTokenUrlsByEbayGlobalId[$this->globalId];
        }
        return $accessTokenUrlsByEbayGlobalId[$this->defaultGlobalId];
    }

    /**
     * @param array $params
     * @return array
     */
    protected function getAccessTokenOptions(array $params)
    {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => sprintf(
                    'Basic %s',
                    base64_encode(sprintf('%s:%s', $params['client_id'], $params['client_secret']))
                ),
            ],
        ];

        unset($params['client_id'], $params['client_secret']);

        if ($this->getAccessTokenMethod() === self::METHOD_POST) {
            $options['body'] = $this->getAccessTokenBody($params);
        }

        return $options;
    }

    /**
     * Creates an access token from a response.
     *
     * The grant that was used to fetch the response can be used to provide
     * additional context.
     *
     * Creates an instance of EbayAccessToken, which extends base, and provides a setResourceOwnerId method, since
     * eBay's API doesn't return it in the 'get access token response', but we can get it when we get the resource owner
     * details later on.
     *
     * @param  array $response
     * @param  AbstractGrant $grant
     * @return AccessToken
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        return new EbayAccessToken($response);
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $resourceOwnerDetailsUrlsByEbayGlobalId = $this->sandbox ? $this->sandboxResourceOwnerDetailsUrlsByEbayGlobalId : $this->productionResourceOwnerDetailsUrlsByEbayGlobalId;
        if (null !== $this->globalId && array_key_exists($this->globalId, $resourceOwnerDetailsUrlsByEbayGlobalId))
        {
            return $resourceOwnerDetailsUrlsByEbayGlobalId[$this->globalId];
        }
        return $resourceOwnerDetailsUrlsByEbayGlobalId[$this->defaultGlobalId];
    }

    /**
     * Requests resource owner details.
     *
     * @param  AccessToken $token
     * @return mixed
     */
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $url = $this->getResourceOwnerDetailsUrl($token);

        $body = '<?xml version="1.0" encoding="utf-8"?>
<GetUserRequest xmlns="urn:ebay:apis:eBLBaseComponents">
</GetUserRequest>';

        $request = $this->getAuthenticatedRequest(self::METHOD_POST, $url, $token, [
            'headers' => [
                'X-EBAY-API-IAF-TOKEN' => $token->getToken(),
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1061',
                'X-EBAY-API-CALL-NAME' => 'GetUser',
                'X-EBAY-API-SITEID' => $this->getSiteId(),
                'Content-Type' => 'text/xml',
                'Content-Length' => strlen($body),
            ],
            'body' => $body,
        ]);

        $response = $this->getParsedResponse($request);

        $response = simplexml_load_string($response);

        return $this->xml2array($response);
    }

    /**
     * @param $xmlObject
     * @param array $out
     * @return array
     */
    function xml2array($xmlObject, $out = [])
    {
        foreach ((array)$xmlObject as $index => $node)
        {
            $out[$index] = (is_object($node) || is_array($node)) ? $this->xml2array($node) : $node;
        }
        return $out;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        return;
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  EbayAccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        if (null !== ($resourceOwnerId = $this->getValueByKey($response, 'User.UserID')))
        {
            $token->setResourceOwnerId($resourceOwnerId);
        }
        return new EbayUser($response);
    }

    /**
     * @return int
     */
    private function getSiteId()
    {
        if (null !== $this->globalId && array_key_exists($this->globalId, $this->globalIdToSideIdMap))
        {
            return $this->globalIdToSideIdMap[$this->globalId];
        }
        return $this->globalIdToSideIdMap[$this->defaultGlobalId];
    }
}