<?php
namespace App\service;

use DateTimeImmutable;

class JWTService

{

 /**
  * géneration jwt
  *
  * @param array $header
  * @param array $payload
  * @param string $secret
  * @param integer $validity
  * @return string
  */
  public function  generate(array $header, array $payload, string $secret, int $validity = 10800): string
  {
    if($validity >0){
        $now = new DateTimeImmutable();

        $exp = $now->getTimestamp() + $validity;
    
        $payload['iat'] = $now->getTimestamp();
        $payload['exp'] = $exp;
        
    }
 

    //  on encode en base64
    $base64header = base64_encode(json_encode($header));
    $base64payload = base64_encode(json_encode($payload));
   
    // on nettoie les valuers encodees (retrait des +,/ et =)
     $base64header = str_replace(['+','/','='],['-','_',''],$base64header);
     $base64payload = str_replace(['+','/','='],['-','_',''], $base64payload);
    
    //  on genere la signature
    $secret = base64_encode($secret);

    $signature = hash_hmac('sha256',$base64header .'.'. $base64payload,$secret,true);

    $base64signature = base64_encode($signature);
    $base64signature = str_replace(['+','/','='],['-','_',''], $base64signature);
   
    //  on cree le token 
    $jwt =  $base64header .'.'. $base64payload . '.' . $base64signature;

      return $jwt;
  }
//   on verier si token valide 
  public function isValid($token):bool
  {

    return preg_match(
        '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',$token
    )===1;
  }
  public function getHeader($token): array
  {
       $array = explode('.',$token);
       $header = json_decode(base64_decode($array[0]),true);
       return $header;
  }
//   on recupere le payload
  public function getPayload( $token): array
  {
       $array = explode('.',$token);
       $payload = json_decode(base64_decode($array[1]),true);
       return $payload;
  }
//   on verifier l'experation
  public function isExpired( $token)
  {
     $payload = $this->getPayload($token);
     $now = new DateTimeImmutable();

     return $payload['exp'] < $now->getTimestamp();

  }
//   on verifeir ssignature 
public function check($token, $secret)
{
  $header= $this->getHeader($token);
  $payload= $this->getPayload($token);
  // on recupere le token 
  $veriftoken = $this->generate($header,$payload,$secret,0);
  return $veriftoken === $token;
}

}
