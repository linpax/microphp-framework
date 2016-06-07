<?php /** MicroEmailDriver */

namespace Micro\Logger\Driver;

use Micro\Mail\Message;
use Micro\Mail\Transport\ITransport;
use Micro\Mail\Transport\TransportInjector;

/**
 * EmailDriver logger class file.
 *
 * Sender email for logger
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Logger\Driver
 * @version 1.0
 * @since 1.0
 */
class EmailDriver extends LoggerDriver
{
    /** @var string $from email for sender attribute */
    private $from;
    /** @var string $type message attribute */
    private $type = 'text/plain';
    /** @var string $to message recipient */
    private $to;
    /** @var string $subject message theme */
    private $subject;


    /**
     * Constructor is a initialize logger
     *
     * @access public
     *
     * @param array $params configuration params
     *
     * @throws \Micro\Base\Exception
     * @result void
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->from = !empty($params['from']) ? $params['from'] : getenv('SERVER_ADMIN');
        $this->to = !empty($params['to']) ? $params['to'] : $this->from;
        $this->subject = $params['subject'] ?: getenv('SERVER_NAME').' log message';
    }

    /**
     * Send message in log
     *
     * @access public
     *
     * @param integer $level level number
     * @param string $message message to write
     *
     * @result void
     * @throws \Micro\Base\Exception
     */
    public function sendMessage($level, $message)
    {
        $mail = new Message($this->from);

        $mail->setTo($this->to);
        $mail->setText(ucfirst($level).': '.$message, $this->type);

        /** @var ITransport $transport */
        $transport = (new TransportInjector)->build();
        $transport->send($mail);
    }
}
