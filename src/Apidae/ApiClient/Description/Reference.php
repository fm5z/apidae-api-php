<?php

namespace Apidae\ApiClient\Description;

class Reference
{
    public static $operations = array(
        // @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/liste-des-services/v002referentielelements-reference
		// @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/liste-des-services/v002referentielcommunes
		// @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/liste-des-services/v002referentielcriteres-internes
		// @see https://dev.apidae-tourisme.com/fr/documentation-technique/v2/api-de-diffusion/liste-des-services/v002referentielselections
        'getReferenceCity' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/referentiel/communes',
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
        'getReferenceElement' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/referentiel/elements-reference',
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
        'getReferenceInternalCriteria' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/referentiel/criteres-internes',
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
        'getReferenceSelection' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v002/referentiel/selections',
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
