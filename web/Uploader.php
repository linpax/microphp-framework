<?php /** MicroUploader */

namespace Micro\Web;

/**
 * Uploader class file.
 *
 * Interface for upload files
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class Uploader
{
    /** @var array $files upload files */
    public $files = [];


    /**
     * Constructor uploads
     *
     * @access public
     *
     * @param array $rawData Raw data for setup
     *
     * @result void
     */
    public function __construct(array $rawData = null)
    {
        $this->setup($rawData);
    }

    /**
     * Setup uploader from config array
     *
     * @access public
     *
     * @param array $rawData Raw data
     *
     * @return void
     */
    public function setup(array $rawData = [])
    {
        if (0 === count($rawData)) {
            return;
        }

        if (!empty($rawData['name'])) {
            foreach (range(0, count($rawData['name']) - 1) AS $i) {
                if (empty($rawData['name'][$i])) {
                    continue;
                }
                $this->files[] = [
                    'name' => $rawData['name'][$i],
                    'type' => $rawData['type'][$i],
                    'error' => $rawData['error'][$i],
                    'tmp_name' => $rawData['tmp_name'][$i],
                    'size' => $rawData['size'][$i]
                ];
            }
        } else {
            $this->files[] = $rawData;
        }
    }
}
