<?php
namespace Airbrake;

/**
 * Airbrake connection class.
 *
 * @package    Airbrake
 * @author     Drew Butler <drew@dbtlr.com>
 * @copyright  (c) 2011-2013 Drew Butler
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Connection implements Connection\ConnectionInterface
{
    protected $configuration = null;
    protected $headers = array();

    /**
     * Build the object with the airbrake Configuration.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->addHeader(array(
            'Accept: text/xml, application/xml',
            'Content-Type: text/xml'
        ));
    }

    /**
     * Add a header to the connection.
     *
     * @param string $header
     */
    public function addHeader($header)
    {
        $this->headers += (array) $header;
    }

    /**
     * @param Notice $notice
     * @return string
     **/
    public function send(Notice $notice)
    {
        $xml = $notice->toXml($this->configuration);

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => $this->headers,
                'content' => $xml
            )
        );

        $context  = stream_context_create($opts);

        $result = file_get_contents($this->configuration->apiEndPoint, false, $context);

        return $result;
    }
}
