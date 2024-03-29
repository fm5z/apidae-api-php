<?php

namespace Apidae\ApiClient\Description;

class Agenda
{
    public static $operations = array(
		// @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/format-des-recherches
        // @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/formats-de-reponse
        'searchAgenda' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/agenda/simple/list-objets-touristiques',
            'responseModel' => 'getResponse',
            'parameters' => [
                'query' => [
                    'type'      => 'string',
                    'location'  => 'formParam',
                    'required'  => true,
                    'filters' => [
                        '\Apidae\ApiClient\Description\Search::encodeSearchQuery',
                    ],
                ],
            ],
        ],
        'searchAgendaIdentifier' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/agenda/simple/list-identifiants',
            'responseModel' => 'getResponse',
            'parameters' => [
                'query' => [
                    'type'      => 'string',
                    'location'  => 'formParam',
                    'required'  => true,
                    'filters' => [
                        '\Apidae\ApiClient\Description\Search::encodeSearchQuery',
                    ],
                ],
            ],
        ],
        'searchDetailedAgenda' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/agenda/detaille/list-objets-touristiques',
            'responseModel' => 'getResponse',
            'parameters' => [
                'query' => [
                    'type'      => 'string',
                    'location'  => 'formParam',
                    'required'  => true,
                    'filters' => [
                        '\Apidae\ApiClient\Description\Search::encodeSearchQuery',
                    ],
                ],
            ],
        ],
        'searchDetailedAgendaIdentifier' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/agenda/detaille/list-identifiants',
            'responseModel' => 'getResponse',
            'parameters' => [
                'query' => [
                    'type'      => 'string',
                    'location'  => 'formParam',
                    'required'  => true,
                    'filters' => [
                        '\Apidae\ApiClient\Description\Search::encodeSearchQuery',
                    ],
                ],
            ],
        ],
    );
}
