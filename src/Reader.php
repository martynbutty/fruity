<?php
namespace fruity;

use Guzzle\Http\Client;

class Reader
{
    /**
     * The URL to read. We have a default value for this app, but this can be overridden if required.
     *
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $bytesRead;

    /**
     * @var string
     */
    private $content;

    /**
     * reader constructor. Allows an optional parameter to be passed to specify the URL to read. If not passed or is
     * passed an empty string (default behaviour), use the default URL for this app
     *
     * @param string $url
     */
    public function __construct($url = '')
    {
        if (trim($url) !== '') {
            $this->url = $url;
        } else {
            $this->url = 'http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html';
        }
    }

    /**
     * Only providing getter as we don't want clients to be able to set the value, i.e. give them read only access
     * @return int
     */
    public function getBytesRead()
    {
        return $this->bytesRead;
    }

    /**
     * Only providing getter as we don't want clients to be able to set the value, i.e. give them read only access
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function readFile()
    {
        if (trim($this->url) === '') {
            throw new \Exception('No URL set to read from');
        }

        $client = $this->getClient();
        $this->bytesRead = 0;
        $this->content = '';

        try {
            $response = $client->get($this->url)->send();
            $this->bytesRead = (int)$response->getHeader('Content-Length')->__toString();
            $this->content = $response->getBody(true); // get the body as a string
        } catch (\Exception $e) {
            /*
             * Would check for Guzzle exceptions like ServerErrorResponseException in production code, and provide
             * better info to consumer and be generally more resilient, but for this app, just catch the error and
             * output something vaguely useful
             */
            throw new \Exception($e->getMessage());
        }
    }

    private function getClient()
    {
        // If we've not already got a Guzzle client, create one.
        if (!$this->client instanceof Client) {
            $this->client = new Client('', array('timeout' => 5.0));
        }
        return $this->client;
    }

}