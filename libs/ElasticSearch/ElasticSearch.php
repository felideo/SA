<?php
namespace  Libs\ElasticSearch;
use Elasticsearch\ClientBuilder;
use Curl\Curl;


class ElasticSearch{
	private $client;

	function __construct() {
		$this->start();
	}

	private function start(){
		$hosts = [
		    '127.0.0.1:9200',         // IP + Port
		    '127.0.0.1',              // Just IP
		    'swdb.localhost:9201', // Domain + Port
		    'swdb.localhost',     // Just Domain
		    'http://localhost',        // SSL to localhost
		    'http://127.0.0.1:9200'  // SSL to IP + Port
		];
		$clientBuilder = ClientBuilder::create();   // Instantiate a new ClientBuilder

		$clientBuilder->setHosts($hosts);           // Set the hosts
		$client = $clientBuilder->build();          // Build the client object

		$this->client = $client;
	}

	public function get_client(){

		return $this->client;
	}

	public function criar_intex(){
		$params = [
		    'index' => 'alternis_modus',
		    'body' => [
		        'settings' => [
		            'number_of_shards'   => 3,
		            'number_of_replicas' => 2
		        ],
		        'mappings' => [
		            'pista' => [
		                '_source' => [
		                    'enabled' => true
		                ],
		                'properties' => [
		                    'identificador' => [
								'type' => 'text'
							]
		                ]
		            ]
		        ]
		    ]
		];

		// Create the index with mappings and settings now
		$response = $this->client->indices()->create($params);
		debug2($response);
		exit;
	}

	// "bool" : {
 //            "must" : {
 //                "query_string" : {
 //                    "query" : "some query string here"
 //                }
 //            },

	public function buscar_pista($termo){
		try{
			$query = [
				'index'   => 'alternis_modus',
				'type'    => 'pista',
				'_source' => '*',
				'body'    => [
					'query' => [
						'bool' => [
      						'should' => [
        						['match_phrase' => [
            							'identificador' => $termo,
          							],
        						],

        						['match' => [
	            						'identificador' => $termo,
	          						],
	        					],
      						],
      						'minimum_should_match' => '99%',
    					]
					]
				]
			];

			return $this->client->search($query);
		} catch(\Exception $e) {
            $this->error = [
                'exception_msg' => $e->getMessage(),
                'code'          => $e->getCode(),
                'localizador'   => "Class => " . __CLASS__ . " - Function => " . __FUNCTION__ . "() - Line => " . __LINE__,
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
                'backtrace'     => $e->getTraceAsString(),
            ];

            throw new \Exception(json_encode($this->error));
        }
	}

	public function indexar($parametros){
		$this->verificar_preexistencia($parametros['id']);
		return $this->client->update($parametros);
	}

	public function verificar_preexistencia($id){
		try{
			$parametros = [
	    		'index' => 'alternis_modus',
	    		'type'  => 'pista',
	    		'id'    => $id
			];

			$retorno = $this->client->get($parametros);
		} catch(\Exception $e) {
            $this->error = [
                'exception_msg' => $e->getMessage(),
                'code'          => $e->getCode(),
                'localizador'   => "Class => " . __CLASS__ . " - Function => " . __FUNCTION__ . "() - Line => " . __LINE__,
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
                'backtrace'     => $e->getTraceAsString(),
            ];

        	$exception_msg = json_decode($this->error['exception_msg'], true);

        	if(empty($exception_msg['found'])){
        		$parametros['body']['identificador'] = true;
				$this->client->index($parametros);
        	}
        }
	}
}