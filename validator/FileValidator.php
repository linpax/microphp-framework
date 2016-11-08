<?php /** MicroFileValidator */

namespace Micro\Validator;

use Micro\Form\IFormModel;
use Micro\Web\RequestInjector;
use Psr\Http\Message\UploadedFileInterface;

/**
 * EmailValidator class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Validator
 * @version 1.0
 * @since 1.0
 */
class FileValidator extends BaseValidator
{
    /**
     * @inheritdoc
     */
    public function validate(IFormModel $model)
    {
        foreach ($this->elements AS $element) {
            if (!$model->checkAttributeExists($element)) {
                /** @var UploadedFileInterface[] $files */
                $files = (new RequestInjector)->build()->getUploadedFiles();
                if (!empty($this->params['maxFiles']) && (count($files) > $this->params['maxFiles'])) {
                    $this->errors[] = 'Too many files in parameter '.$element;

                    return false;
                }
                foreach ($files AS $fContext) {
                    if (!empty($this->params['types']) && (strpos($this->params['types'],
                                $fContext->getClientMediaType()) === false)
                    ) {
                        $this->errors[] = 'File '.$fContext->getClientFilename().' not allowed type';

                        return false;
                    }
                    if (!empty($this->params['minSize']) && ($fContext->getSize() < $this->params['minSize'])) {
                        $this->errors[] = 'File '.$fContext->getClientFilename().' too small size';

                        return false;
                    }
                    if (!empty($this->params['maxSize']) && ($fContext->getSize() > $this->params['maxSize'])) {
                        $this->errors[] = 'File '.$fContext->getClientFilename().' too many size';

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function client(IFormModel $model)
    {
        return '';
    }
}
