<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key_public = '87e0f01335218b75d733cc86bda2a613';
    private $api_key_secret = '268b1457ee456c026760d2fc8fb41c10';
    
    
    public function send($to_email, $to_name, $subject, $content)
    {
        $apikey = getenv('MJ_APIKEY_PUBLIC');
        $apisecret = getenv('MJ_APIKEY_PRIVATE');

        $mj = new Client($this->api_key_public, $this->api_key_secret, true, ['version' => 'v3.1']); // instance de l'objet email
        //$mj = new Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
        
        $body = [ // création du corps du mail
            'Messages' => [
              [
                'From' => [
                  'Email' => "ugoblackandwhite@gmail.com",
                  'Name' => "La Boutique Française"
                ],
                'To' => [
                  [
                    'Email' => $to_email,
                    'Name' => $to_name
                  ]
                ],
                'TemplateID' => 4555918,
                'TemplateLanguage' => true,
                'Subject' => $subject,
                'variables' => [
                    'content' => $content,
                ]

                // 'Variables' => json_decode('{
                //     "content": ""
                // }', true)
              ]
            ]
          ];
          $response = $mj->post(Resources::$Email, ['body' => $body]); // on passe le corps du mail à $mj->post pour qu'il l'envoi
          $response->success() && dd($response->getData()); // on regarde la réponse
    }
}