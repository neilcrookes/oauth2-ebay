<?php

namespace NeilCrookes\OAuth2\Client\Provider;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use NeilCrookes\OAuth2\Client\Token\EbayAccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class Ebay extends AbstractProvider
{
    use BearerAuthorizationTrait {
        BearerAuthorizationTrait::getAuthorizationHeaders as BearerAuthorizationTraitGetAuthorizationHeaders;
    }
    use ArrayAccessorTrait;

    const BASE_URI_PRODUCTION = 'https://api.ebay.com';
    const BASE_URI_SANDBOX = 'https://api.sandbox.ebay.com';

    /**
     * eBay Global IDs
     */
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
    const GLOBAL_ID_EBAY_CN    = 'EBAY_CN';    // eBay China
    const GLOBAL_ID_EBAY_TH    = 'EBAY_TH';    // eBay Thailand
    const GLOBAL_ID_EBAY_CZ    = 'EBAY_CZ';    // eBay Czech Republic
    const GLOBAL_ID_EBAY_DK    = 'EBAY_DK';    // eBay Denmark
    const GLOBAL_ID_EBAY_FI    = 'EBAY_FI';    // eBay Finland
    const GLOBAL_ID_EBAY_GR    = 'EBAY_GR';    // eBay Greece
    const GLOBAL_ID_EBAY_HU    = 'EBAY_HU';    // eBay Hungary
    const GLOBAL_ID_EBAY_ID    = 'EBAY_ID';    // eBay Indonesia
    const GLOBAL_ID_EBAY_IL    = 'EBAY_IL';    // eBay Israel
    const GLOBAL_ID_EBAY_JP    = 'EBAY_JP';    // eBay Japan
    const GLOBAL_ID_EBAY_NO    = 'EBAY_NO';    // eBay Norway
    const GLOBAL_ID_EBAY_NZ    = 'EBAY_NZ';    // eBay New Zealand
    const GLOBAL_ID_EBAY_PE    = 'EBAY_PE';    // eBay Peru
    const GLOBAL_ID_EBAY_PR    = 'EBAY_PR';    // eBay Puerto Rico
    const GLOBAL_ID_EBAY_PT    = 'EBAY_PT';    // eBay Portugal
    const GLOBAL_ID_EBAY_RU    = 'EBAY_RU';    // eBay Russia
    const GLOBAL_ID_EBAY_SE    = 'EBAY_SE';    // eBay Sweden
    const GLOBAL_ID_EBAY_TW    = 'EBAY_TW';    // eBay Taiwan
    const GLOBAL_ID_EBAY_VN    = 'EBAY_VN';    // eBay Vietnam
    const GLOBAL_ID_EBAY_ZA    = 'EBAY_ZA';    // eBay South Africa

    /**
     * eBay Site IDs (need to fill in the missing ones)
     */
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
//    const SITE_ID_EBAY_CN    = ?;            // eBay China
//    const SITE_ID_EBAY_TH    = ?;            // eBay Thailand
//    const SITE_ID_EBAY_CZ    = ?;            // eBay Czech Republic
//    const SITE_ID_EBAY_DK    = ?;            // eBay Czech Republic
//    const SITE_ID_EBAY_FI    = ?;            // eBay Finland
//    const SITE_ID_EBAY_GR    = ?;            // eBay Greece
//    const SITE_ID_EBAY_HU    = ?;            // eBay Hungary
//    const SITE_ID_EBAY_ID    = ?;            // eBay Indonesia
//    const SITE_ID_EBAY_IL    = ?;            // eBay Israel
//    const SITE_ID_EBAY_JP    = ?;            // eBay Japan
//    const SITE_ID_EBAY_NO    = ?;            // eBay Norway
//    const SITE_ID_EBAY_NZ    = ?;            // eBay New Zealand
//    const SITE_ID_EBAY_PE    = ?;            // eBay Peru
//    const SITE_ID_EBAY_PR    = ?;            // eBay Puerto Rico
//    const SITE_ID_EBAY_PT    = ?;            // eBay Portugal
//    const SITE_ID_EBAY_RU    = ?;            // eBay Russia
//    const SITE_ID_EBAY_SE    = ?;            // eBay Sweden
//    const SITE_ID_EBAY_TW    = ?;            // eBay Taiwan
//    const SITE_ID_EBAY_VN    = ?;            // eBay Vietnam
//    const SITE_ID_EBAY_ZA   = ?;             // eBay South Africa

    /**
     * eBay Marketplace Site URLs (need to fill in the missing ones)
     */
    const SITE_URL_EBAY_US = 'https://www.ebay.com';
    const SITE_URL_EBAY_US_MOTOR = 'https://www.ebay.com/motors';
    const SITE_URL_EBAY_AT = 'https://www.ebay.at';
    const SITE_URL_EBAY_AU = 'https://www.ebay.com.au';
    const SITE_URL_EBAY_NLBE = 'https://www.benl.ebay.be/';
    const SITE_URL_EBAY_FRBE = 'https://www.befr.ebay.be';
    const SITE_URL_EBAY_ENCA = 'https://www.ebay.ca';
    const SITE_URL_EBAY_FRCA = 'https://www.cafr.ebay.ca';
    const SITE_URL_EBAY_CH = 'https://www.ebay.ch';
    const SITE_URL_EBAY_DE = 'https://www.ebay.de';
    const SITE_URL_EBAY_ES = 'https://www.ebay.es';
    const SITE_URL_EBAY_FR = 'https://www.ebay.fr';
    const SITE_URL_EBAY_GB = 'https://www.ebay.co.uk';
    const SITE_URL_EBAY_HK = 'https://www.ebay.com.hk';
    const SITE_URL_EBAY_IE = 'https://www.ebay.ie';
    const SITE_URL_EBAY_IN = 'https://www.ebay.in';
    const SITE_URL_EBAY_IT = 'https://www.ebay.it';
    const SITE_URL_EBAY_MY = 'https://www.ebay.com.my';
    const SITE_URL_EBAY_NL = 'https://www.ebay.nl';
    const SITE_URL_EBAY_PH = 'https://www.ebay.ph';
    const SITE_URL_EBAY_PL = 'https://www.ebay.pl';
    const SITE_URL_EBAY_SG = 'https://www.ebay.com.sg';

    /**
     * @var array
     */
    private $globalIdToSiteIdMap = [
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
//        self::GLOBAL_ID_EBAY_CN    => self::SITE_ID_EBAY_CN,    // eBay China
//        self::GLOBAL_ID_EBAY_TH    => self::SITE_ID_EBAY_TH,    // eBay Thailand
//        self::GLOBAL_ID_EBAY_CZ    => self::SITE_ID_EBAY_CZ,    // eBay Czech Republic
//        self::GLOBAL_ID_EBAY_DK    => self::SITE_ID_EBAY_DK,    // eBay Denmark
//        self::GLOBAL_ID_EBAY_FI    => self::SITE_ID_EBAY_FI,    // eBay Finland
//        self::GLOBAL_ID_EBAY_GR    => self::SITE_ID_EBAY_GR,    // eBay Greece
//        self::GLOBAL_ID_EBAY_HU    => self::SITE_ID_EBAY_HU,    // eBay Hungary
//        self::GLOBAL_ID_EBAY_ID    => self::SITE_ID_EBAY_ID,    // eBay Indonesia
//        self::GLOBAL_ID_EBAY_IL    => self::SITE_ID_EBAY_IL,    // eBay Israel
//        self::GLOBAL_ID_EBAY_JP    => self::SITE_ID_EBAY_JP,    // eBay Japan
//        self::GLOBAL_ID_EBAY_NO    => self::SITE_ID_EBAY_NO,    // eBay Norway
//        self::GLOBAL_ID_EBAY_NZ    => self::SITE_ID_EBAY_NZ,    // eBay New Zealand
//        self::GLOBAL_ID_EBAY_PE    => self::SITE_ID_EBAY_PE,    // eBay Peru
//        self::GLOBAL_ID_EBAY_PR    => self::SITE_ID_EBAY_PR,    // eBay Puerto Rico
//        self::GLOBAL_ID_EBAY_PT    => self::SITE_ID_EBAY_PT,    // eBay Portugal
//        self::GLOBAL_ID_EBAY_RU    => self::SITE_ID_EBAY_RU,    // eBay Russia
//        self::GLOBAL_ID_EBAY_SE    => self::SITE_ID_EBAY_SE,    // eBay Sweden
//        self::GLOBAL_ID_EBAY_TW    => self::SITE_ID_EBAY_TW,    // eBay Taiwan
//        self::GLOBAL_ID_EBAY_VN    => self::SITE_ID_EBAY_VN,    // eBay Vietnam
//        self::GLOBAL_ID_EBAY_ZA    => self::SITE_ID_EBAY_ZA,    // eBay South Africa
    ];

    /**
     * @var array
     */
    private $globalIdToMarketplaceSiteUrlMap = [
        self::GLOBAL_ID_EBAY_US => self::SITE_URL_EBAY_US, // United States en-US, pt-BR, ru-RU
        self::GLOBAL_ID_EBAY_AT => self::SITE_URL_EBAY_AT, // Austria de-AT
        self::GLOBAL_ID_EBAY_AU => self::SITE_URL_EBAY_AU, // Australia en-AU
        self::GLOBAL_ID_EBAY_NLBE => self::SITE_URL_EBAY_NLBE, // Belgium (Nederlandse)
        self::GLOBAL_ID_EBAY_FRBE => self::SITE_URL_EBAY_FRBE, // Belgium (French)  (Française)	fr-BE, nl-BE
        self::GLOBAL_ID_EBAY_ENCA => self::SITE_URL_EBAY_ENCA, // Canada (English)
        self::GLOBAL_ID_EBAY_FRCA => self::SITE_URL_EBAY_FRCA, // Canada (French)   (Française)	en-CA, fr-CA
        self::GLOBAL_ID_EBAY_CH => self::SITE_URL_EBAY_CH, // Switzerland de-CH
        self::GLOBAL_ID_EBAY_DE => self::SITE_URL_EBAY_DE, // Germany de-DE
        self::GLOBAL_ID_EBAY_ES => self::SITE_URL_EBAY_ES, // Spain es-ES
        self::GLOBAL_ID_EBAY_FR => self::SITE_URL_EBAY_FR, // France fr-FR
        self::GLOBAL_ID_EBAY_GB => self::SITE_URL_EBAY_GB, // Great Britain en-GB
        self::GLOBAL_ID_EBAY_HK => self::SITE_URL_EBAY_HK, // Hong Kong zh-HK
        self::GLOBAL_ID_EBAY_IE => self::SITE_URL_EBAY_IE, // Ireland en-IE
        self::GLOBAL_ID_EBAY_IN => self::SITE_URL_EBAY_IN, // India en-GB
        self::GLOBAL_ID_EBAY_IT => self::SITE_URL_EBAY_IT, // Italy it-IT
        self::GLOBAL_ID_EBAY_MY => self::SITE_URL_EBAY_MY, // Malaysia en-US
        self::GLOBAL_ID_EBAY_NL => self::SITE_URL_EBAY_NL, // Netherlands nl-NL
        self::GLOBAL_ID_EBAY_PH => self::SITE_URL_EBAY_PH, // Philippines en-PH
        self::GLOBAL_ID_EBAY_PL => self::SITE_URL_EBAY_PL, // Poland pl-PL
        self::GLOBAL_ID_EBAY_SG => self::SITE_URL_EBAY_SG, // Singapore en-US
        self::GLOBAL_ID_EBAY_MOTOR => self::SITE_URL_EBAY_US_MOTOR, // United States en-US
//        self::GLOBAL_ID_EBAY_TH => 'https://info.ebay.co.th', // Thailand th-TH
//        self::GLOBAL_ID_EBAY_TW => 'https://www.ebay.com.tw', // Taiwan zh-TW
//        self::GLOBAL_ID_EBAY_VN => 'https://www.ebay.vn', // Vietnam en-US
//        self::GLOBAL_ID_EBAY_CN => '?', // Vietnam en-US
//        self::GLOBAL_ID_EBAY_CZ => '?', // Czech Republic ?
//        self::GLOBAL_ID_EBAY_DK => '?', // Denmark ?
//        self::GLOBAL_ID_EBAY_FI => '?', // Finland ?
//        self::GLOBAL_ID_EBAY_GR => '?', // Greece ?
//        self::GLOBAL_ID_EBAY_HU => '?', // Hungary ?
//        self::GLOBAL_ID_EBAY_ID => '?', // Indonesia ?
//        self::GLOBAL_ID_EBAY_IL => '?', // Israel ?
//        self::GLOBAL_ID_EBAY_DK => '?', // Denmark
//        self::GLOBAL_ID_EBAY_FI => '?', // Finland
//        self::GLOBAL_ID_EBAY_GR => '?', // Greece
//        self::GLOBAL_ID_EBAY_HU => '?', // Hungary
//        self::GLOBAL_ID_EBAY_ID => '?', // Indonesia
//        self::GLOBAL_ID_EBAY_IL => '?', // Israel
//        self::GLOBAL_ID_EBAY_JP => '?', // Japan
//        self::GLOBAL_ID_EBAY_NO => '?', // Norway
//        self::GLOBAL_ID_EBAY_NZ => '?', // New Zealand
//        self::GLOBAL_ID_EBAY_PE => '?', // Peru
//        self::GLOBAL_ID_EBAY_PR => '?', // Puerto Rico
//        self::GLOBAL_ID_EBAY_PT => '?', // Portugal
//        self::GLOBAL_ID_EBAY_RU => '?', // Russia
//        self::GLOBAL_ID_EBAY_SE => '?', // Sweden
//        self::GLOBAL_ID_EBAY_TW => '?', // Taiwan
//        self::GLOBAL_ID_EBAY_VN => '?', // Vietnam
//        self::GLOBAL_ID_EBAY_ZA => '?', // South Africa
    ];

    private $localeToDefaultGlobalIdMap = [
        'en-US' => self::GLOBAL_ID_EBAY_US,
        'ru-RU' => self::GLOBAL_ID_EBAY_US,
        'pt-BR' => self::GLOBAL_ID_EBAY_US,
        'de-AT' => self::GLOBAL_ID_EBAY_AT,
        'en-AU' => self::GLOBAL_ID_EBAY_AU,
        'nl-BE' => self::GLOBAL_ID_EBAY_NLBE,
        'fr-BE' => self::GLOBAL_ID_EBAY_FRBE,
        'en-CA' => self::GLOBAL_ID_EBAY_ENCA,
        'fr-CA' => self::GLOBAL_ID_EBAY_FRCA,
        'de-CH' => self::GLOBAL_ID_EBAY_CH,
        'de-DE' => self::GLOBAL_ID_EBAY_DE,
        'es-ES' => self::GLOBAL_ID_EBAY_ES,
        'fr-FR' => self::GLOBAL_ID_EBAY_FR,
        'en-GB' => self::GLOBAL_ID_EBAY_GB,
        'zh-HK' => self::GLOBAL_ID_EBAY_HK,
        'en-IE' => self::GLOBAL_ID_EBAY_IE,
        'in-IN' => self::GLOBAL_ID_EBAY_IN,
        'it-IT' => self::GLOBAL_ID_EBAY_IT,
        'my-MY' => self::GLOBAL_ID_EBAY_MY,
        'nl-NL' => self::GLOBAL_ID_EBAY_NL,
        'en-PH' => self::GLOBAL_ID_EBAY_PH,
        'pl-PL' => self::GLOBAL_ID_EBAY_PL,
        'sg-SG' => self::GLOBAL_ID_EBAY_SG,
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
        self::GLOBAL_ID_EBAY_US => self::BASE_URI_SANDBOX.'/identity/v1/oauth2/token',
        self::GLOBAL_ID_EBAY_FR => self::BASE_URI_SANDBOX.'/identity/v1/oauth2/token',
    ];

    private $productionAccessTokenUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => self::BASE_URI_PRODUCTION.'/identity/v1/oauth2/token',
        self::GLOBAL_ID_EBAY_FR => self::BASE_URI_PRODUCTION.'/identity/v1/oauth2/token',
    ];

    private $sandboxResourceOwnerDetailsUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => self::BASE_URI_SANDBOX.'/ws/api.dll',
        self::GLOBAL_ID_EBAY_FR => self::BASE_URI_SANDBOX.'/ws/api.dll',
    ];

    private $productionResourceOwnerDetailsUrlsByEbayGlobalId = [
        self::GLOBAL_ID_EBAY_US => self::BASE_URI_PRODUCTION.'/ws/api.dll',
        self::GLOBAL_ID_EBAY_FR => self::BASE_URI_PRODUCTION.'/ws/api.dll',
    ];

    /**
     * @var string
     */
    private $defaultGlobalId = self::GLOBAL_ID_EBAY_US;

    /**
     * @var bool
     */
    protected $sandbox = false;

    /**
     * @var string
     */
    protected $globalId;

    /**
     * @var bool
     */
    private $isTraditionalApi = false;

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
     * @return array
     */
    public function getLocaleToDefaultGlobalIdMap()
    {
        return $this->localeToDefaultGlobalIdMap;
    }

    /**
     * @return array
     */
    public function getGlobalIdToSiteIdMap()
    {
        return $this->globalIdToSiteIdMap;
    }

    /**
     * @return array
     */
    public function getGlobalIdToMarketplaceSiteUrlMap()
    {
        return $this->globalIdToMarketplaceSiteUrlMap;
    }

    /**
     * @return string
     */
    public function getDefaultGlobalId()
    {
        return $this->defaultGlobalId;
    }

    /**
     * @inheritdoc
     */
    protected function getAllowedClientOptions(array $options)
    {
        return array_merge(parent::getAllowedClientOptions($options), ['http_errors', 'read_timeout', 'connect_timeout']);
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
     * @throws IdentityProviderException
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        if (!array_key_exists('access_token', $response))
        {
            $message = sprintf('Cannot create access token for eBay since response does not include an `access_token` key');
            throw new IdentityProviderException($message, 0, $response);
        }
        return new EbayAccessToken($response);
    }

    /**
     * @param EbayAccessToken $accessToken
     * @return EbayAccessToken
     * @throws IdentityProviderException
     */
    public function refreshAccessToken(EbayAccessToken $accessToken)
    {
        $newAccessToken = $this->getAccessToken('refresh_token', [
            'refresh_token' => $accessToken->getRefreshToken()
        ]);
        $options = array_merge($accessToken->jsonSerialize(), [
            'access_token' => $newAccessToken->getToken(),
            'expires' => $newAccessToken->getExpires()
        ]);
        return new EbayAccessToken($options);
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
     * @throws IdentityProviderException
     */
    public function fetchResourceOwnerDetails(AccessToken $token)
    {
        $url = $this->getResourceOwnerDetailsUrl($token);

        $body = '<?xml version="1.0" encoding="utf-8"?>
<GetUserRequest xmlns="urn:ebay:apis:eBLBaseComponents">
<DetailLevel>ReturnAll</DetailLevel>
</GetUserRequest>';

        $request = $this->getAuthenticatedRequest(self::METHOD_POST, $url, $token, [
            'headers' => [
                'X-EBAY-API-CALL-NAME' => 'GetUser',
                'Content-Type' => 'text/xml',
                'Content-Length' => strlen($body),
            ],
            'body' => $body,
        ]);

        return $this->getParsedResponse($request);
    }

    /**
     * Returns an authenticated PSR-7 request instance.
     *
     * @param  string $method
     * @param  string $url
     * @param  AccessToken|string $token
     * @param  array $options Any of "headers", "body", and "protocolVersion".
     * @return RequestInterface
     */
    public function getAuthenticatedRequest($method, $url, $token, array $options = [])
    {
        if (false !== strpos($url, '/ws/api.dll'))
        {
            $this->isTraditionalApi = true;
        }
        else
        {
            $this->isTraditionalApi = false;
        }
        return parent::getAuthenticatedRequest($method, $url, $token, $options);
    }

    /**
     * Parses the response according to its content-type header.
     *
     * @throws UnexpectedValueException
     * @param  ResponseInterface $response
     * @return array
     */
    public function parseResponse(ResponseInterface $response)
    {
        $content = trim((string)$response->getBody());

        if (empty($content))
        {
            return [];
        }

        $type = $this->getContentType($response);

        if (strpos($type, 'xml') !== false) {
            return $this->parseXml($response);
        }

        return parent::parseResponse($response);
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
        if (substr((string)$response->getStatusCode(), 0, 1) !== '2') {
            $message = sprintf('Got a %d %s status response from Ebay API', $response->getStatusCode(), $response->getReasonPhrase());
            throw new IdentityProviderException($message, 0, $response->getBody()->getContents());
        }
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
        if (null !== $this->globalId && array_key_exists($this->globalId, $this->globalIdToSiteIdMap))
        {
            return $this->globalIdToSiteIdMap[$this->globalId];
        }
        return $this->globalIdToSiteIdMap[$this->defaultGlobalId];
    }

    /**
     * Returns the authorization headers used by this provider.
     *
     * Typically this is "Bearer" or "MAC". For more information see:
     * http://tools.ietf.org/html/rfc6749#section-7.1
     *
     * No default is provided, providers must overload this method to activate
     * authorization headers.
     *
     * @param  mixed|null $token Either a string or an access token instance
     * @return array
     */
    protected function getAuthorizationHeaders($token = null)
    {
        if (!$token)
        {
            return [];
        }
        if ($this->isTraditionalApi)
        {
            return [
                'X-EBAY-API-IAF-TOKEN' => $token->getToken(),
                'X-EBAY-API-COMPATIBILITY-LEVEL' => '1061',
                'X-EBAY-API-SITEID' => $this->getSiteId(),
            ];
        }
        return $this->BearerAuthorizationTraitGetAuthorizationHeaders($token);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function parseXml(ResponseInterface $response)
    {
        $content = (string)$response->getBody();
        $xml = simplexml_load_string($content);
        return $this->xml2array($xml);
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
}