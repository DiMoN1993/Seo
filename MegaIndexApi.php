<?php
/**
 * Description of MegaIndex
 * @property user
 * @property password
 * @property url
 * @const MEGAINDEX_API
 * @const QUANTITY_RESULTS
 * @const POST
 * @const GET
 * @author Admin
 */
class MegaIndexApi
{
    const MEGAINDEX_API = 'http://api.megaindex.ru/';
    const QUANTITY_RESULTS = '100';
    const POST = 'post';
    const GET = 'get';
    const EMPTY_ANSWER = 'not found';

    public $user;
    public $password;
    public $url;

    public function __construct($url, $user, $password)
    {
        if (empty ($user))
          $user = 'megaindex-api-test@megaindex.ru';
        if (empty($password))
          $password = '123456';
        $this->user = $user;
        $this->password = $password;
        $this->url = $url;
    }

    private function curlQuery ($address, $data, $method)
    {
        $address = self::MEGAINDEX_API.$address;
        if (!is_array($data))
            throw new Exception ('Argument data is not array.');
        
        $data['user'] = $this->user;
        $data ['password'] = $this->password;
        
        $ch = curl_init();
        if (!$ch)
            throw new Exception ('cURL initialize error.');
        
        switch ($method)
        {
          case self::GET:
                curl_setopt($ch, CURLOPT_URL, $address.'?'.http_build_query($data));
                break;
          case self::POST:
                curl_setopt($ch, CURLOPT_URL, $address);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            default :
                throw new Exception ('Unexpected method '.$method);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $content = curl_exec($ch);
        if ($content === false)
        {
            throw new Exception ('Ошибка curl: '.curl_error($ch));
        }
        if (empty($content))
        {
            throw new Exception ('Empty content from '.$address);
        }
        else
        {
            $json = json_decode($content);
        
            if(!is_object($json) )
            {
                throw new Exception ('JSON answer from this url is not a object. '.json_last_error());
            }
            if($json->status!=0)
            {
                throw new Exception ('Query error - '.$json->err_msg);
            }

            return $json->data;
        }
        curl_close($ch);
    }
    
    public function getYandexPosition ($request, $region=213, $important=true, $showTitle=1)
    {
        if (!is_string($request))
          throw new Exception ('Variable request cant be array in method  MegaIndex::getYandexPosition()!');
        else
          $data['request'] = $request;

        $data['lr'] = $region;
        $data['results'] = self::QUANTITY_RESULTS;
        $data['show_title'] = $showTitle;
        $important ? $data['imp'] = 1 : $data['imp'] = 0;
        
        $content = $this->curlQuery('scan_yandex_position', $data, self::GET);

        for ($i=0; $i<self::QUANTITY_RESULTS; $i++)
        {
          if ($content[$i]->domain==$this->url)
          {
            return $this->isEmptyAnswer($content[$i]->position);
          }
        }

        return false;
    }

    public function getPrice ($request)
    {
        $data['request'] = $request;

        $scanPrice = $this->curlQuery('scan_price', $data, self::POST);

        if (is_array($request))
        {
          foreach ($request as $value)
          {
            $content[$value] = $this->isEmptyAnswer($scanPrice->$value);
          }
          return $content;
        }
        else
        {
          return $this->isEmptyAnswer($scanPrice->$request);
        }
    }

    private function isEmptyAnswer($answer)
    {
      if (!empty($answer))
        return $answer;
      else
        return self::EMPTY_ANSWER;
    }

    public function getWordStat ($request, $region=213, $important=true)
    {
        if (!is_string($request))
          throw new Exception ('Variable request cant be array in method MegaIndex::getWordStat()!');
        else
          $data['request'] = $request;

        $data['lr'] = $region;
        $important ? $data['imp'] = 1 : $data['imp'] = 0;

        $stat = $this->curlQuery('scan_wordstat', $data, self::POST);

        return $this->isEmptyAnswer($stat);
    }
}
