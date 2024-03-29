<?php

namespace Apidae\ApiClient\Description;

use Apidae\ApiClient\Subscriber\AuthenticationSubscriber;

class User
{
    public static $operations = array(
        // @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/oauth/services-associes-au-sso/v002ssoutilisateurprofil
        'getUserProfile' => [
            'httpMethod' => 'GET',
            'uri' => '/api/v002/sso/utilisateur/profil',
            'responseModel' => 'getResponse',
            'data' => [
                'scope' => AuthenticationSubscriber::SSO_SCOPE,
            ],
        ],
        // @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/oauth/services-associes-au-sso/v002ssoutilisateurautorisationobjet-touristiquemodification
        'getUserPermissionOnObject' => [
            'httpMethod' => 'GET',
            'uri' => '/api/v002/sso/utilisateur/autorisation/objet-touristique/modification/{id}',
            'responseModel' => 'getResponse',
            'parameters' => [
                'id' => [
                    'type' => 'integer',
                    'location' => 'uri',
                    'required' => true,
                ],
            ],
            'data' => [
                'scope' => AuthenticationSubscriber::SSO_SCOPE,
            ],
        ],
    );
}
