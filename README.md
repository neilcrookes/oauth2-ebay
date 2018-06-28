# Shopify Provider for OAuth 2.0 Client

This package provides Ebay OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require neilcrookes/oauth2-ebay
```

## Usage

Usage is the same as The League's OAuth client, using `NeilCrookes\OAuth2\Client\Provider\Ebay` as the provider.

```
$provider = new \NeilCrookes\OAuth2\Client\Provider\Ebay([
    'clientId' => 'YOUR EBAY APP ID',
    'clientSecret' => 'YOUR EBAY CERTIFICATE ID',
    'redirectUri' => 'YOUR EBAY "RU" NAME',
    'scopeSeparator' => ' ',
    'sandbox' => true, //defaults to false, i.e. production
]);

$accessToken = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

$ebayUser = $provider->getResourceOwner($accessToken);

echo $ebayUser->getId(); // eBay User's user id
echo $ebayUser->getEmail(); // eBay User's email address
```