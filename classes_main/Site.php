<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Validator\PositiveNumber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Exception as Exception;

/**
 * Main class of the site. Handles dependencies, Google Analytics
 * and is responsible for rendering the common blocks of the site
 */
class Site
{
    /**
     * @var string
     */
    private $lang;
    
    /**
     * @var Request
     */
    public $request;
    
    /**
     * @var Response
     */
    public $response;
    
    public function __construct()
    {
        set_exception_handler(array($this, 'handleException'));
        
        $this->request = Request::createFromGlobals();
        $this->response = new Response();
        $this->response->setProtocolVersion('1.1');

        if (! Config::getValue('online')) {
            throw new FLPokerException('The site is currently down for maintenance', FLPokerException::SITE_OFFLINE);
        }

        $this->setSiteLanguage();
    }
    
    /**
     * Get the language the site is currently using.
     * @return string a valid language
     */
    public function getLanguage()
    {
        return $this->lang;
    }
    
    /**
     * Get the word specified by the key in the site's current language.
     * @param string $key the word key
     * @return mixed a string representing the desired word or null if it doesn't exist.
     */
    public function getWord($key)
    {
        return Dictionary::getWord($key, $this->lang);
    }
    
    /**
     * Utility function that allows validating numerical parameters passed through request query parameters
     * @param string $parameterName
     * @param int $maximumLength
     * @param mixed $default
     */
    public function isValidNumericQueryParameter($parameterName, $maximumLength, $default = null)
    {
        return $this->isValidNumericParameter(
            $this->request->query->get($parameterName, $default),
            $maximumLength
        );
    }
    
    /**
     * Utility function that allows validating numerical parameters passed through request POST parameters
     * @param string $parameterName
     * @param int $maximumLength
     * @param mixed $default
     */
    public function isValidNumericPostParameter($parameterName, $maximumLength, $default = null)
    {
        return $this->isValidNumericParameter(
            $this->request->request->get($parameterName, $default),
            $maximumLength
        );
    }
    
    /**
     * Global exception handler. This function will log all exceptions
     * and redirect the user to the appropiate error page, depending on the situation.
     * It's registered in this class's constructor, so it would be ideal to that the first thing
     * every page does is create an instance of this class.
     * 
     * @param Exception $e the thrown Exception.
     */
    public function handleException(Exception $e)
    {
        Logger::log($e->getMessage());
        
        $newLocation = '500.shtml';
        
        if ($e instanceof FLPokerException) {
            switch ($e->getCode()) {
                case FLPokerException::ERROR:
                    $newLocation = '500.shtml';
                    break;
                
                case FLPokerException::INVALID_REQUEST:
                    $newLocation = '400.shtml';
                    break;
                
                case FLPokerException::SITE_OFFLINE:
                    $newLocation = 'maintenance.shtml';
                    break;
                
                default:
                    $newLocation = '500.shtml';
                    break;
            }
        }
        
        $this->response->setStatusCode(Response::HTTP_TEMPORARY_REDIRECT);
        $this->response->headers->set('Location', $newLocation);
        $this->response->send();
        
        exit();
    }
    
    private function setSiteLanguage()
    {
        $cookieName = Config::getValue('lang_cookie_name');
    
        //read language from the cookie or use default language and write it to the cookie to be used
        //on the next request
        $lang = $this->request->cookies->get($cookieName);
        if (Dictionary::isValidLanguage($lang)) {
            $this->lang = $lang;
        } else {
            $this->lang = Config::getValue('default_lang');
            
            $cookieDuration = Config::getValue('lang_cookie_duration');
            
            $cookie = new Cookie($cookieName, $this->lang, time() + $cookieDuration);
            
            $this->response->headers->setCookie($cookie);
        }
    }
    
    private function isValidNumericParameter($parameter, $maximumLength)
    {
        $validatorBuilder = new ValidatorBuilder();

        /* @var $validator ValidatorInterface */
        $validator = $validatorBuilder->getValidator();

        $constraint = new PositiveNumber();
        $constraint->maxLength = $maximumLength;

        $errors = $validator->validate($parameter, $constraint);
        
        return $errors;
    }
}
